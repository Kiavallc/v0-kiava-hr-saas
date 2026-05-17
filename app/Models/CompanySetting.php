<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySetting extends Model
{
    protected $table = 'company_settings';

    protected $fillable = [
        'company_id',
        'primary_color',
        'secondary_color',
        'login_page_text',
        'logo_path',
        'enable_notifications',
        'enable_email_reminders',
        'reminder_days_before_expiration',
    ];

    protected $casts = [
        'enable_notifications' => 'boolean',
        'enable_email_reminders' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
