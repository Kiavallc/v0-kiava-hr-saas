<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityEvent extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'event_type',
        'ip_address',
        'user_agent',
        'device_name',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getDeviceNameAttribute($value)
    {
        if ($value) return $value;
        
        $agent = $this->user_agent;
        if (str_contains($agent, 'Chrome')) return 'Chrome';
        if (str_contains($agent, 'Firefox')) return 'Firefox';
        if (str_contains($agent, 'Safari')) return 'Safari';
        if (str_contains($agent, 'Mobile')) return 'Mobile';
        
        return 'Unknown';
    }
}
