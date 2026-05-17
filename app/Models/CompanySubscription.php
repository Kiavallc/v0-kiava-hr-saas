<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySubscription extends Model
{
    protected $table = 'company_subscriptions';

    protected $fillable = [
        'company_id',
        'subscription_plan_id',
        'subscription_id',
        'billing_cycle',
        'status',
        'trial_ends_at',
        'billing_cycle_starts_at',
        'billing_cycle_ends_at',
        'cancelled_at',
        'amount',
    ];

    protected $casts = [
        'trial_ends_at' => 'date',
        'billing_cycle_starts_at' => 'date',
        'billing_cycle_ends_at' => 'date',
        'cancelled_at' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['trial', 'active']);
    }
}
