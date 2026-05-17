<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMfaMethod extends Model
{
    protected $fillable = [
        'user_id',
        'method',
        'secret',
        'phone_number',
        'backup_codes',
        'is_primary',
        'is_verified',
        'verified_at',
        'last_used_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    protected $encrypted = ['secret', 'backup_codes'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verify()
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function recordUsage()
    {
        $this->update(['last_used_at' => now()]);
    }

    public function getBackupCodes()
    {
        return json_decode($this->backup_codes, true) ?? [];
    }

    public function generateBackupCodes()
    {
        $codes = collect(range(1, 10))->map(fn() => bin2hex(random_bytes(4)))->toArray();
        $this->update(['backup_codes' => json_encode($codes)]);
        return $codes;
    }

    public function useBackupCode($code)
    {
        $codes = $this->getBackupCodes();
        $key = array_search($code, $codes);
        
        if ($key !== false) {
            unset($codes[$key]);
            $this->update(['backup_codes' => json_encode(array_values($codes))]);
            $this->recordUsage();
            return true;
        }
        
        return false;
    }
}
