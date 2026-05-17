<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compliance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->date('report_date');
            $table->integer('total_employees');
            $table->integer('documents_submitted');
            $table->integer('documents_approved');
            $table->integer('documents_pending');
            $table->integer('documents_expired');
            $table->integer('documents_expiring_soon');
            $table->decimal('compliance_percentage', 5, 2);
            $table->json('breakdown_by_document_type')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'report_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compliance_reports');
    }
};
