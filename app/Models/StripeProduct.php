<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StripeProduct extends Model
{
    protected $fillable = [
        'stripe_id',
        'name',
        'description',
        'is_active',
        'employee_count_limit',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'employee_count_limit' => 'integer',
    ];

    public function stripePrices(): HasMany
    {
        return $this->hasMany(StripePrice::class);
    }

    public function stripeSubscriptions()
    {
        return $this->hasManyThrough(StripeSubscription::class, StripePrice::class);
    }
}
