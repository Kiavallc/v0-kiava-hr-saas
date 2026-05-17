<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_id',
        'employee_id',
        'phone',
        'hire_date',
        'birth_date',
        'ssn_encrypted',
        'position',
        'department',
        'status',
        'upload_storage_used_mb',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'birth_date' => 'date',
        'archived_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function getMaskedSSNAttribute(): ?string
    {
        if (!$this->ssn_encrypted) {
            return null;
        }
        return 'XXX-XX-' . substr($this->ssn_encrypted, -4);
    }

    public function getCompliancePercentageAttribute(): float
    {
        $requirements = DocumentRequirement::where('company_id', $this->company_id)
            ->where('is_required', true)
            ->count();

        if ($requirements === 0) {
            return 100;
        }

        $approved = EmployeeDocument::where('employee_id', $this->id)
            ->whereIn('status', ['approved', 'expiring_soon'])
            ->count();

        return round(($approved / $requirements) * 100, 2);
    }

    public function getMissingDocumentsAttribute()
    {
        return DocumentRequirement::where('company_id', $this->company_id)
            ->where('is_required', true)
            ->whereNotIn('id', function($query) {
                $query->select('requirement_id')
                    ->from('employee_documents')
                    ->where('employee_id', $this->id)
                    ->whereIn('status', ['approved', 'expiring_soon']);
            })
            ->get();
    }

    public function getExpiringDocumentsAttribute()
    {
        return $this->documents()
            ->where('status', 'expiring_soon')
            ->orWhere('status', 'expired')
            ->get();
    }
}
