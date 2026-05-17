<?php

namespace App\Services;

use App\Models\DocumentStorage;
use App\Models\EmployeeDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class S3StorageService
{
    private $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('s3');
    }

    public function uploadDocument(UploadedFile $file, EmployeeDocument $document, $companyId)
    {
        // Generate unique filename
        $filename = Str::uuid() . '.' . $file->extension();
        $path = "documents/company-{$companyId}/employee-{$document->employee_id}/{$filename}";

        // Calculate hash for deduplication
        $fileHash = hash('sha256', $file->getContent());

        // Check if this file already exists
        $existing = DocumentStorage::where('file_hash', $fileHash)
            ->where('company_id', $companyId)
            ->first();

        if ($existing) {
            return $existing;
        }

        // Upload to S3
        $this->disk->putFileAs(
            "documents/company-{$companyId}/employee-{$document->employee_id}",
            $file,
            $filename,
            [
                'visibility' => 'private',
                'ServerSideEncryption' => 'AES256',
                'StorageClass' => 'INTELLIGENT_TIERING',
            ]
        );

        // Create storage record
        return DocumentStorage::create([
            'employee_document_id' => $document->id,
            'company_id' => $companyId,
            'file_name' => $file->getClientOriginalName(),
            's3_path' => $path,
            's3_bucket' => config('filesystems.disks.s3.bucket'),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'storage_class' => 'INTELLIGENT_TIERING',
            'encryption' => 'AES256',
            'file_hash' => $fileHash,
        ]);
    }

    public function deleteDocument(DocumentStorage $storage)
    {
        // Soft delete
        $storage->update(['is_deleted' => true]);

        // Schedule actual deletion after 30 days
        \Illuminate\Support\Facades\Bus::dispatch(function () use ($storage) {
            if ($storage->created_at->addDays(30)->isPast()) {
                $this->disk->delete($storage->s3_path);
                $storage->forceDelete();
            }
        })->delay(now()->addDays(30));
    }

    public function archiveOldDocuments($days = 90)
    {
        DocumentStorage::whereDate('created_at', '<', now()->subDays($days))
            ->where('storage_class', '!=', 'GLACIER')
            ->get()
            ->each(function ($storage) {
                $storage->archive();
            });
    }

    public function downloadDocument(DocumentStorage $storage)
    {
        return $this->disk->download($storage->s3_path, $storage->file_name);
    }

    public function getPreviewUrl(DocumentStorage $storage, $expiresIn = 3600)
    {
        return $storage->getPublicUrl($expiresIn);
    }

    public function getStorageStats($companyId)
    {
        $storage = DocumentStorage::where('company_id', $companyId)
            ->where('is_deleted', false)
            ->get();

        return [
            'total_files' => $storage->count(),
            'total_size' => $storage->sum('file_size'),
            'by_storage_class' => $storage->groupBy('storage_class')->map->count(),
            'archived_count' => $storage->where('storage_class', 'GLACIER')->count(),
        ];
    }
}
