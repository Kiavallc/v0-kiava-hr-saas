<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('stripe_invoice_id')->unique();
            $table->decimal('amount', 12, 2);
            $table->string('currency');
            $table->enum('status', ['draft', 'open', 'paid', 'void', 'uncollectible'])->default('draft');
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->string('pdf_url')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
