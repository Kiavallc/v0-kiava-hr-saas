<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mfa_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->boolean('require_mfa_for_admins')->default(false);
            $table->boolean('require_mfa_for_all')->default(false);
            $table->enum('allowed_methods', ['totp', 'sms', 'email'])->default('totp');
            $table->integer('grace_period_days')->default(7);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mfa_settings');
    }
};
