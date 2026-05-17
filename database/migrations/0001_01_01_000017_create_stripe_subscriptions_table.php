<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stripe_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('stripe_customer_id');
            $table->string('stripe_subscription_id')->unique();
            $table->foreignId('stripe_price_id')->constrained('stripe_prices');
            $table->enum('status', ['trialing', 'active', 'past_due', 'canceled', 'unpaid', 'incomplete', 'incomplete_expired'])->default('trialing');
            $table->dateTime('trial_ends_at')->nullable();
            $table->dateTime('current_period_start')->nullable();
            $table->dateTime('current_period_end')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'stripe_subscription_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stripe_subscriptions');
    }
};
