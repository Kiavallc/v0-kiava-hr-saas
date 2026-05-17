<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_storage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('file_name');
            $table->string('s3_path');
            $table->string('s3_bucket');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->string('storage_class')->default('STANDARD'); // STANDARD, INTELLIGENT_TIERING, GLACIER
            $table->string('encryption')->default('AES256');
            $table->string('file_hash')->unique(); // SHA-256 hash for deduplication
            $table->dateTime('archived_at')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_storage');
    }
};
