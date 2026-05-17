<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_document_id')->constrained('employee_documents')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_filename');
            $table->integer('version_number');
            $table->date('expiration_date')->nullable();
            $table->enum('status', ['active', 'superseded', 'archived'])->default('active');
            $table->timestamps();

            $table->unique(['employee_document_id', 'version_number']);
            $table->index(['employee_document_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
};
