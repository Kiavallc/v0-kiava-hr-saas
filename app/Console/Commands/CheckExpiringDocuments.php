<?php

namespace App\Console\Commands;

use App\Models\EmployeeDocument;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckExpiringDocuments extends Command
{
    protected $signature = 'documents:check-expiring';
    protected $description = 'Check for documents expiring in 30, 14, and 7 days';

    public function handle()
    {
        $this->checkExpiringIn(30, '30 days');
        $this->checkExpiringIn(14, '14 days');
        $this->checkExpiringIn(7, '7 days');
    }

    private function checkExpiringIn(int $days, string $message)
    {
        $expirationDate = Carbon::now()->addDays($days);

        $documents = EmployeeDocument::where('status', 'approved')
            ->whereDate('expiration_date', $expirationDate->format('Y-m-d'))
            ->with('employee.user', 'company')
            ->get();

        foreach ($documents as $document) {
            Notification::create([
                'user_id' => $document->employee->user_id,
                'company_id' => $document->company_id,
                'type' => 'document_expiring',
                'title' => 'Document Expiring',
                'message' => "Your {$document->requirement->name} will expire in {$message}",
                'data' => [
                    'document_id' => $document->id,
                    'days_until_expiry' => $days,
                ],
            ]);

            $this->info("Notification sent for document {$document->id}");
        }
    }
}
