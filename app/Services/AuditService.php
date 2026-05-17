<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public static function log(
        string $action,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $changes = null,
        ?string $reason = null
    ): void {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        $companyId = $user->company_id ?? null;

        if (!$companyId && !$user->isSuperAdmin()) {
            return;
        }

        AuditLog::create([
            'company_id' => $companyId,
            'user_id' => $user->id,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'changes' => $changes,
            'reason' => $reason,
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
        ]);
    }

    public static function logLogin(int $userId): void
    {
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return;
        }

        self::log('login', 'User', $userId);
    }

    public static function logFailedLogin(string $email): void
    {
        self::log('failed_login', 'User', null, ['email' => $email]);
    }

    public static function logPasswordChange(int $userId): void
    {
        self::log('password_change', 'User', $userId);
    }

    public static function logDocumentUpload(int $documentId): void
    {
        self::log('document_uploaded', 'EmployeeDocument', $documentId);
    }

    public static function logDocumentApproved(int $documentId): void
    {
        self::log('document_approved', 'EmployeeDocument', $documentId);
    }

    public static function logDocumentRejected(int $documentId, string $reason): void
    {
        self::log('document_rejected', 'EmployeeDocument', $documentId, ['reason' => $reason]);
    }
}
