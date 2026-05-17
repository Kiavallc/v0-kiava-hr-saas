<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingEvent extends Model
{
    protected $fillable = [
        'company_id',
        'stripe_event_id',
        'event_type',
        'payload',
        'status',
        'error_message',
        'retry_count',
    ];

    protected $casts = [
        'payload' => 'array',
        'retry_count' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function markAsProcessed()
    {
        $this->update(['status' => 'processed', 'error_message' => null]);
    }

    public function markAsFailed($error)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $error,
            'retry_count' => $this->retry_count + 1,
        ]);
    }
}
