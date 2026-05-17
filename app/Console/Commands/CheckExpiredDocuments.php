<?php

namespace App\Console\Commands;

use App\Models\EmployeeDocument;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckExpiredDocuments extends Command
{
    protected $signature = 'documents:check-expired';
    protected $description = 'Check for expired documents';

    public function handle()
    {
        $now = Carbon::now();

        $documents = EmployeeDocument::where('status', 'approved')
            ->where('expiration_date', '<', $now)
            ->with('employee.user', 'company')
            ->get();

        foreach ($documents as $document) {
            Notification::create([
                'user_id' => $document->employee->user_id,
                'company_id' => $document->company_id,
                'type' => 'document_expired',
                'title' => 'Document Expired',
                'message' => "Your {$document->requirement->name} has expired. Please upload a new one.",
                'data' => [
                    'document_id' => $document->id,
                ],
            ]);

            $this->info("Expiration notification sent for document {$document->id}");
        }
    }
}
