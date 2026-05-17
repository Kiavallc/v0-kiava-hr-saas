<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->unique()->constrained('companies')->cascadeOnDelete();
            $table->string('primary_color')->default('#3b82f6');
            $table->string('secondary_color')->default('#1e40af');
            $table->text('login_page_text')->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('enable_notifications')->default(true);
            $table->boolean('enable_email_reminders')->default(true);
            $table->integer('reminder_days_before_expiration')->default(30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
