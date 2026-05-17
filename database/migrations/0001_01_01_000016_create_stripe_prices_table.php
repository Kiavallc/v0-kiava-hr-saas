<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stripe_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stripe_product_id')->constrained('stripe_products')->cascadeOnDelete();
            $table->string('stripe_id')->unique();
            $table->string('currency');
            $table->integer('amount'); // in cents
            $table->enum('billing_period', ['monthly', 'yearly', 'one_time']);
            $table->integer('trial_days')->default(14);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stripe_prices');
    }
};
