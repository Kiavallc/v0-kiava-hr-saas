<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'company_id',
        'stripe_invoice_id',
        'amount',
        'currency',
        'status',
        'paid_at',
        'due_date',
        'pdf_url',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'due_date' => 'datetime',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function getFormattedAmountAttribute()
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }
}
