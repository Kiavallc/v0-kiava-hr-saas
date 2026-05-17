<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplianceReport extends Model
{
    protected $fillable = [
        'company_id',
        'report_date',
        'total_employees',
        'documents_submitted',
        'documents_approved',
        'documents_pending',
        'documents_expired',
        'documents_expiring_soon',
        'compliance_percentage',
        'breakdown_by_document_type',
    ];

    protected $casts = [
        'report_date' => 'date',
        'compliance_percentage' => 'decimal:2',
        'breakdown_by_document_type' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
