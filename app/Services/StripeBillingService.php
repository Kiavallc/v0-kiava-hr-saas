<?php

namespace App\Services;

use App\Models\Company;
use App\Models\StripeSubscription;
use App\Models\StripePrice;
use App\Models\Invoice;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\StripeClient;

class StripeBillingService
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function createCustomer(Company $company)
    {
        if ($company->stripe_customer_id) {
            return $this->stripe->customers->retrieve($company->stripe_customer_id);
        }

        $customer = $this->stripe->customers->create([
            'name' => $company->name,
            'email' => $company->billing_email,
            'metadata' => [
                'company_id' => $company->id,
            ],
        ]);

        $company->update(['stripe_customer_id' => $customer->id]);
        return $customer;
    }

    public function createSubscription(Company $company, StripePrice $price)
    {
        if (!$company->stripe_customer_id) {
            $this->createCustomer($company);
        }

        $subscription = $this->stripe->subscriptions->create([
            'customer' => $company->stripe_customer_id,
            'items' => [['price' => $price->stripe_id]],
            'trial_period_days' => $price->trial_days,
            'metadata' => [
                'company_id' => $company->id,
                'plan' => $price->stripeProduct->name,
            ],
        ]);

        return StripeSubscription::create([
            'company_id' => $company->id,
            'stripe_customer_id' => $company->stripe_customer_id,
            'stripe_subscription_id' => $subscription->id,
            'stripe_price_id' => $price->id,
            'status' => $subscription->status,
            'trial_ends_at' => $subscription->trial_end ? \Carbon\Carbon::createFromTimestamp($subscription->trial_end) : null,
            'current_period_start' => \Carbon\Carbon::createFromTimestamp($subscription->current_period_start),
            'current_period_end' => \Carbon\Carbon::createFromTimestamp($subscription->current_period_end),
        ]);
    }

    public function cancelSubscription(StripeSubscription $subscription, $reason = null)
    {
        $this->stripe->subscriptions->cancel($subscription->stripe_subscription_id);

        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        $subscription->company->broadcast('subscription.canceled', $subscription);
    }

    public function updateSubscription(StripeSubscription $subscription, StripePrice $newPrice)
    {
        $this->stripe->subscriptions->update(
            $subscription->stripe_subscription_id,
            ['items' => [['id' => $subscription->stripe_subscription_id, 'price' => $newPrice->stripe_id]]]
        );

        $subscription->update(['stripe_price_id' => $newPrice->id]);
    }

    public function handleWebhookEvent($event)
    {
        match ($event->type) {
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($event->data->object),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event->data->object),
            'invoice.paid' => $this->handleInvoicePaid($event->data->object),
            'invoice.payment_failed' => $this->handlePaymentFailed($event->data->object),
            default => null,
        };
    }

    private function handleSubscriptionUpdated($stripeSubscription)
    {
        $subscription = StripeSubscription::where('stripe_subscription_id', $stripeSubscription->id)->firstOrFail();
        
        $subscription->update([
            'status' => $stripeSubscription->status,
            'current_period_start' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_start),
            'current_period_end' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end),
        ]);
    }

    private function handleSubscriptionDeleted($stripeSubscription)
    {
        $subscription = StripeSubscription::where('stripe_subscription_id', $stripeSubscription->id)->firstOrFail();
        
        $subscription->update([
            'status' => 'canceled',
            'ended_at' => now(),
        ]);

        // Revoke access
        $subscription->company->update(['is_active' => false]);
    }

    private function handleInvoicePaid($stripeInvoice)
    {
        $invoice = Invoice::where('stripe_invoice_id', $stripeInvoice->id)->firstOrFail();
        
        $invoice->update([
            'status' => 'paid',
            'paid_at' => \Carbon\Carbon::createFromTimestamp($stripeInvoice->paid),
        ]);

        $invoice->company->broadcast('invoice.paid', $invoice);
    }

    private function handlePaymentFailed($stripeInvoice)
    {
        $invoice = Invoice::where('stripe_invoice_id', $stripeInvoice->id)->firstOrFail();
        
        $invoice->update(['status' => 'open']);

        $invoice->company->broadcast('payment.failed', $invoice);
    }
}
