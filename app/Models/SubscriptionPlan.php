<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'employee_limit',
        'storage_gb',
        'features',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'json',
        'is_active' => 'boolean',
    ];
}
