<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_mfa_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('method', ['totp', 'sms', 'email']);
            $table->text('secret')->encrypted();
            $table->string('phone_number')->nullable();
            $table->string('backup_codes')->nullable(); // JSON array
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->dateTime('verified_at')->nullable();
            $table->dateTime('last_used_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'method']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_mfa_methods');
    }
};
