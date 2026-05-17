<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeDocument extends Model
{
    protected $fillable = [
        'employee_id',
        'requirement_id',
        'company_id',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size_bytes',
        'expiration_date',
        'status',
        'rejection_reason',
        'version',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_id');
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(DocumentRequirement::class, 'requirement_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class, 'employee_document_id');
    }

    public function getSignedDownloadUrlAttribute(): string
    {
        return \Storage::disk('private')->temporaryUrl(
            $this->file_path,
            now()->addHours(1)
        );
    }

    public function isExpiring(): bool
    {
        if (!$this->expiration_date) {
            return false;
        }

        $daysUntilExpiration = now()->diffInDays($this->expiration_date, false);
        return $daysUntilExpiration <= 30 && $daysUntilExpiration > 0;
    }

    public function isExpired(): bool
    {
        return $this->expiration_date && $this->expiration_date->isPast();
    }
}
