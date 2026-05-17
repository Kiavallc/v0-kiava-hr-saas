<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->enum('event_type', ['login_success', 'login_failed', 'mfa_enabled', 'mfa_disabled', 'password_changed', 'ip_changed', 'suspicious_activity']);
            $table->string('ip_address');
            $table->string('user_agent');
            $table->string('device_name')->nullable();
            $table->json('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_events');
    }
};
