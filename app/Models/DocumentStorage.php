<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentStorage extends Model
{
    protected $fillable = [
        'employee_document_id',
        'company_id',
        'file_name',
        's3_path',
        's3_bucket',
        'mime_type',
        'file_size',
        'storage_class',
        'encryption',
        'file_hash',
        'archived_at',
        'is_deleted',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'archived_at' => 'datetime',
        'is_deleted' => 'boolean',
    ];

    public function employeeDocument(): BelongsTo
    {
        return $this->belongsTo(EmployeeDocument::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getPublicUrl($expiresIn = 3600)
    {
        return \Storage::disk('s3')->temporaryUrl(
            $this->s3_path,
            now()->addSeconds($expiresIn)
        );
    }

    public function archive()
    {
        $this->update([
            'storage_class' => 'GLACIER',
            'archived_at' => now(),
        ]);
    }

    public function restore()
    {
        $this->update([
            'storage_class' => 'STANDARD',
            'archived_at' => null,
        ]);
    }
}
