<?php

namespace App\Http\Controllers;

use App\Models\BillingEvent;
use App\Services\StripeBillingService;
use Illuminate\Http\Request;
use Stripe\Event;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('stripe-signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Store event for processing
        $billingEvent = BillingEvent::create([
            'company_id' => $this->getCompanyIdFromEvent($event),
            'stripe_event_id' => $event->id,
            'event_type' => $event->type,
            'payload' => $event->data,
        ]);

        // Process immediately for critical events
        if (in_array($event->type, [
            'customer.subscription.deleted',
            'invoice.payment_failed',
        ])) {
            try {
                (new StripeBillingService())->handleWebhookEvent($event);
                $billingEvent->markAsProcessed();
            } catch (\Exception $e) {
                $billingEvent->markAsFailed($e->getMessage());
                \Log::error('Stripe webhook error', ['event' => $event->id, 'error' => $e->getMessage()]);
            }
        }

        return response()->json(['success' => true]);
    }

    private function getCompanyIdFromEvent(Event $event)
    {
        return $event->data['object']['metadata']['company_id'] ?? null;
    }
}
