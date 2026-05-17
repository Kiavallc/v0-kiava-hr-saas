<?php

namespace App\Http\Controllers;

use App\Models\StripePrice;
use App\Services\StripeBillingService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function showPlans()
    {
        $plans = StripePrice::where('is_active', true)
            ->with('stripeProduct')
            ->get()
            ->groupBy('stripeProduct.name');

        return view('billing.plans', ['plans' => $plans]);
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'price_id' => 'required|exists:stripe_prices,id',
        ]);

        $price = StripePrice::findOrFail($request->price_id);
        $company = $request->user()->company;

        try {
            $service = new StripeBillingService();
            $subscription = $service->createSubscription($company, $price);

            return redirect()->route('billing.success', ['subscription_id' => $subscription->id]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function success(Request $request)
    {
        $subscription = $request->user()->company->stripeSubscriptions()
            ->findOrFail($request->subscription_id);

        return view('billing.success', ['subscription' => $subscription]);
    }

    public function manageSubscription()
    {
        $subscription = auth()->user()->company->activeSubscription();

        if (!$subscription) {
            return redirect()->route('billing.plans');
        }

        return view('billing.manage', ['subscription' => $subscription]);
    }

    public function cancelSubscription(Request $request)
    {
        $subscription = auth()->user()->company->activeSubscription();

        if (!$subscription) {
            return redirect()->back()->withErrors(['error' => 'No active subscription']);
        }

        $service = new StripeBillingService();
        $service->cancelSubscription($subscription, $request->reason);

        return redirect()->route('billing.canceled');
    }

    public function viewInvoices()
    {
        $invoices = auth()->user()->company->invoices()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('billing.invoices', ['invoices' => $invoices]);
    }

    public function downloadInvoice($invoiceId)
    {
        $invoice = auth()->user()->company->invoices()->findOrFail($invoiceId);

        return \Illuminate\Support\Facades\Storage::disk('s3')->download($invoice->pdf_url);
    }
}
