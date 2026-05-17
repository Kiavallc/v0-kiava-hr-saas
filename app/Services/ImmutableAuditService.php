<?php

namespace App\Services;

use App\Models\Company;

class ImmutableAuditService
{
    public function logAction($company, $user, $action, $modelType, $modelId, $changes = [])
    {
        $previousLog = \DB::table('immutable_audit_logs')
            ->where('company_id', $company->id)
            ->latest()
            ->first();

        $previousHash = $previousLog->hash ?? null;
        $hash = $this->generateHash($company, $user, $action, $previousHash, $changes);

        \DB::table('immutable_audit_logs')->insert([
            'company_id' => $company->id,
            'user_id' => $user?->id,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'changes' => json_encode($changes),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'hash' => $hash,
            'previous_hash' => $previousHash,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function verifyIntegrity($company)
    {
        $logs = \DB::table('immutable_audit_logs')
            ->where('company_id', $company->id)
            ->orderBy('created_at')
            ->get();

        $previousHash = null;
        foreach ($logs as $log) {
            $expectedHash = $this->generateHash(
                $company,
                null,
                $log->action,
                $previousHash,
                json_decode($log->changes, true)
            );

            if ($log->hash !== $expectedHash) {
                return [
                    'valid' => false,
                    'tampered_log_id' => $log->id,
                    'message' => 'Audit log has been tampered with',
                ];
            }

            $previousHash = $log->hash;
        }

        return ['valid' => true, 'message' => 'All audit logs verified'];
    }

    private function generateHash($company, $user, $action, $previousHash, $changes)
    {
        $data = implode('|', [
            $company->id,
            $user?->id ?? 'system',
            $action,
            $previousHash ?? 'start',
            json_encode($changes),
        ]);

        return hash('sha256', $data);
    }
}
