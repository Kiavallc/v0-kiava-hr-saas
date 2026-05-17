<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StripePrice extends Model
{
    protected $fillable = [
        'stripe_product_id',
        'stripe_id',
        'currency',
        'amount',
        'billing_period',
        'trial_days',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'integer',
        'trial_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function stripeProduct(): BelongsTo
    {
        return $this->belongsTo(StripeProduct::class);
    }

    public function stripeSubscriptions(): HasMany
    {
        return $this->hasMany(StripeSubscription::class);
    }

    public function getDisplayAmountAttribute()
    {
        return number_format($this->amount / 100, 2);
    }
}
