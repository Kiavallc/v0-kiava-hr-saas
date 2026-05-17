<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employee_profiles')->cascadeOnDelete();
            $table->foreignId('requirement_id')->constrained('document_requirements')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->bigInteger('file_size_bytes');
            $table->date('expiration_date')->nullable();
            $table->enum('status', ['pending_review', 'approved', 'rejected', 'expired', 'expiring_soon'])->default('pending_review');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();

            $table->unique(['employee_id', 'requirement_id']);
            $table->index(['company_id', 'status']);
            $table->index(['expiration_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};
