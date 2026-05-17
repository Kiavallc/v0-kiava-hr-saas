<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function sendNotification($user, $templateSlug, $data = [])
    {
        $template = NotificationTemplate::where('slug', $templateSlug)
            ->where(function($q) {
                $q->whereNull('company_id')
                  ->orWhere('company_id', auth()?->user()?->company_id ?? $user->company_id);
            })
            ->firstOrFail();

        $subject = $this->interpolate($template->subject ?? '', $data);
        $body = $this->interpolate($template->body, $data);

        if ($template->type === 'email') {
            $this->sendEmail($user, $subject, $body, $data);
        } elseif ($template->type === 'sms') {
            $this->sendSms($user, $body, $data);
        } elseif ($template->type === 'in_app') {
            $this->sendInApp($user, $subject, $body, $data);
        }
    }

    public function sendEmail($user, $subject, $body, $data = [])
    {
        try {
            Mail::send('emails.notification', [
                'subject' => $subject,
                'body' => $body,
                'data' => $data,
            ], function($message) use ($user, $subject) {
                $message->to($user->email)->subject($subject);
            });

            Notification::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'type' => 'email',
                'title' => $subject,
                'message' => $body,
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Email notification failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
        }
    }

    public function sendSms($user, $message, $data = [])
    {
        // Integration point for Twilio or similar SMS service
        Notification::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'type' => 'sms',
            'message' => $message,
            'sent_at' => now(),
        ]);
    }

    public function sendInApp($user, $title, $message, $data = [])
    {
        Notification::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'type' => 'in_app',
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);

        // Broadcast to user in real-time
        if ($user->id) {
            \Illuminate\Support\Facades\Broadcast::channel("user.{$user->id}")
                ->later('notification.received', [
                    'title' => $title,
                    'message' => $message,
                ]);
        }
    }

    public function bulkNotify($users, $templateSlug, $data = [])
    {
        foreach ($users as $user) {
            try {
                $this->sendNotification($user, $templateSlug, $data);
            } catch (\Exception $e) {
                \Log::error('Bulk notification failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            }
        }
    }

    public function notifyDocumentExpiring($document)
    {
        $daysUntilExpiry = now()->diffInDays($document->expiration_date);

        if ($daysUntilExpiry <= 30 && $daysUntilExpiry > 0) {
            $this->sendNotification($document->employee->user, 'document-expiring-soon', [
                'employee_name' => $document->employee->user->name,
                'document_type' => $document->requirement->name,
                'expiration_date' => $document->expiration_date->format('M d, Y'),
                'days_remaining' => $daysUntilExpiry,
            ]);
        }
    }

    public function notifyDocumentExpired($document)
    {
        $this->sendNotification($document->employee->user, 'document-expired', [
            'employee_name' => $document->employee->user->name,
            'document_type' => $document->requirement->name,
        ]);

        // Also notify admins
        $admins = $document->company->users()->where('role', 'hr_admin')->get();
        foreach ($admins as $admin) {
            $this->sendNotification($admin, 'admin-document-expired', [
                'employee_name' => $document->employee->user->name,
                'document_type' => $document->requirement->name,
            ]);
        }
    }

    public function notifyApprovalRequired($document)
    {
        $admins = $document->company->users()
            ->whereIn('role', ['hr_admin', 'manager'])
            ->get();

        foreach ($admins as $admin) {
            $this->sendNotification($admin, 'document-awaiting-approval', [
                'admin_name' => $admin->name,
                'employee_name' => $document->employee->user->name,
                'document_type' => $document->requirement->name,
            ]);
        }
    }

    private function interpolate($template, $data)
    {
        foreach ($data as $key => $value) {
            $template = str_replace("{{$key}}", $value, $template);
        }
        return $template;
    }
}
