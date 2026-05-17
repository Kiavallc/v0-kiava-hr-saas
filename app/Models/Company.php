<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'description',
        'logo_path',
        'primary_color',
        'secondary_color',
        'login_page_text',
        'employee_limit',
        'storage_limit_gb',
        'status',
        'subscription_expires_at',
        'stripe_customer_id',
        'billing_email',
        'is_active',
    ];

    protected $casts = [
        'subscription_expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(EmployeeProfile::class);
    }

    public function documentRequirements(): HasMany
    {
        return $this->hasMany(DocumentRequirement::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(CompanySetting::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(CompanySubscription::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function loginSessions(): HasMany
    {
        return $this->hasMany(LoginSession::class);
    }

    public function stripeSubscriptions(): HasMany
    {
        return $this->hasMany(StripeSubscription::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function billingEvents(): HasMany
    {
        return $this->hasMany(BillingEvent::class);
    }

    public function documentStorage(): HasMany
    {
        return $this->hasMany(DocumentStorage::class);
    }

    public function activeSubscription()
    {
        return $this->stripeSubscriptions()
            ->where('status', 'active')
            ->orWhere('status', 'trialing')
            ->latest()
            ->first();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription() !== null;
    }
}
