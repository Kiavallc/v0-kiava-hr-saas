<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StripeSubscription extends Model
{
    protected $fillable = [
        'company_id',
        'stripe_customer_id',
        'stripe_subscription_id',
        'stripe_price_id',
        'status',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'canceled_at',
        'ended_at',
        'cancellation_reason',
        'metadata',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'canceled_at' => 'datetime',
        'ended_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function stripePrice(): BelongsTo
    {
        return $this->belongsTo(StripePrice::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['trialing', 'active']);
    }

    public function onTrial(): bool
    {
        return $this->status === 'trialing' && $this->trial_ends_at?->isFuture();
    }

    public function isPastDue(): bool
    {
        return $this->status === 'past_due';
    }
}
