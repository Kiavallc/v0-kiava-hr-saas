<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserMfaMethod;
use App\Models\MfaSetting;
use App\Models\SecurityEvent;
use PragmaRX\Google2FA\Google2FA;

class MfaService
{
    private Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function requiresMfa(User $user): bool
    {
        $settings = MfaSetting::where('company_id', $user->company_id)->first();
        
        if ($settings?->require_mfa_for_all) {
            return true;
        }

        if ($settings?->require_mfa_for_admins && $user->isCompanyAdmin()) {
            return true;
        }

        return $user->mfaMethods()->where('is_verified', true)->exists();
    }

    public function generateTotpSecret(User $user): array
    {
        $secret = $this->google2fa->generateSecretKey();
        
        $qrCode = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return [
            'secret' => $secret,
            'qr_code' => $qrCode,
        ];
    }

    public function enableTotp(User $user, string $secret, string $code): bool
    {
        if (!$this->verifyCode($secret, $code)) {
            return false;
        }

        UserMfaMethod::updateOrCreate(
            ['user_id' => $user->id, 'method' => 'totp'],
            [
                'secret' => $secret,
                'is_verified' => true,
                'is_primary' => !$user->mfaMethods()->exists(),
                'verified_at' => now(),
            ]
        );

        $backupCodes = $user->mfaMethods()
            ->firstOrCreate(['user_id' => $user->id, 'method' => 'totp'])
            ->generateBackupCodes();

        $this->logSecurityEvent($user, 'mfa_enabled', ['method' => 'totp']);

        return true;
    }

    public function enableSms(User $user, string $phoneNumber): void
    {
        UserMfaMethod::create([
            'user_id' => $user->id,
            'method' => 'sms',
            'phone_number' => $phoneNumber,
            'is_verified' => false,
        ]);

        // Send verification code
        $this->sendVerificationCode($user, 'sms', $phoneNumber);
    }

    public function verifyCode(string $secret, string $code): bool
    {
        $isValid = $this->google2fa->verifyKey($secret, $code);
        
        // Allow 30-second window for time drift
        if (!$isValid) {
            $isValid = $this->google2fa->verifyKey($secret, $code, 1);
        }

        return $isValid;
    }

    public function verifyUserCode(User $user, string $code): bool
    {
        $method = $user->mfaMethods()
            ->where('is_verified', true)
            ->first();

        if (!$method) {
            return false;
        }

        // Check backup codes first
        if ($method->useBackupCode($code)) {
            return true;
        }

        // Check TOTP
        if ($method->method === 'totp') {
            if ($this->verifyCode($method->secret, $code)) {
                $method->recordUsage();
                return true;
            }
        }

        return false;
    }

    public function disableMfa(User $user, string $method): void
    {
        UserMfaMethod::where('user_id', $user->id)
            ->where('method', $method)
            ->delete();

        $this->logSecurityEvent($user, 'mfa_disabled', ['method' => $method]);
    }

    private function sendVerificationCode(User $user, string $method, string $destination): void
    {
        $code = random_int(100000, 999999);
        // Store and send via appropriate channel
        // This is a simplified example
    }

    private function logSecurityEvent(User $user, string $eventType, array $details = []): void
    {
        SecurityEvent::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'event_type' => $eventType,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details' => $details,
        ]);
    }
}
