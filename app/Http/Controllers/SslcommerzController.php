<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Raziul\Sslcommerz\Facades\Sslcommerz;

class SslcommerzController extends Controller
{
    /**
     * SSLCommerz POSTs here after successful payment.
     * Note: user session is NOT available here (server-to-server POST).
     * We identify the user via the payment record using tran_id.
     */
    public function success(Request $request)
    {
        $tranId   = $request->input('tran_id');
        $amount   = $request->input('amount');
        $currency = $request->input('currency');

        // Validate with SSLCommerz servers
        $isValid = Sslcommerz::validatePayment($request->all(), $tranId, $amount, $currency);

        if (! $isValid) {
            return redirect()->route('user.plans')
                ->with('error', 'Payment validation failed. Contact support with Tran ID: ' . $tranId);
        }

        $payment = Payment::where('transaction_id', $tranId)
            ->where('status', 'pending')
            ->with('plan')
            ->first();

        if (! $payment) {
            // Already processed (IPN may have handled it first) — just redirect to dashboard
            return redirect()->route('user.dashboard')
                ->with('success', 'Payment already processed. Your plan is active.');
        }

        // Mark payment completed
        $payment->update([
            'status'           => 'completed',
            'gateway_response' => $request->all(),
        ]);

        // Cancel any existing active subscription for this user
        Subscription::where('user_id', $payment->user_id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        // Create new subscription
        $sub = Subscription::create([
            'user_id'    => $payment->user_id,
            'plan_id'    => $payment->plan_id,
            'starts_at'  => now(),
            'expires_at' => now()->addDays($payment->plan->duration_days),
            'status'     => 'active',
        ]);

        $payment->update(['subscription_id' => $sub->id]);

        return redirect()->route('user.dashboard')
            ->with('success', 'Payment successful! Your ' . $payment->plan->name . ' plan is now active.');
    }

    /**
     * SSLCommerz POSTs here on payment failure.
     */
    public function failure(Request $request)
    {
        $tranId = $request->input('tran_id');

        Payment::where('transaction_id', $tranId)
            ->where('status', 'pending')
            ->update(['status' => 'failed']);

        return redirect()->route('user.plans')
            ->with('error', 'Payment failed. Please try again.');
    }

    /**
     * SSLCommerz POSTs here when user cancels.
     */
    public function cancel(Request $request)
    {
        $tranId = $request->input('tran_id');

        Payment::where('transaction_id', $tranId)
            ->where('status', 'pending')
            ->update(['status' => 'failed']);

        return redirect()->route('user.plans')
            ->with('error', 'Payment cancelled.');
    }

    /**
     * Instant Payment Notification — server-to-server background call.
     * This fires even if the user closes the browser.
     * Must return HTTP 200 plain text.
     */
    public function ipn(Request $request)
    {
        $tranId   = $request->input('tran_id');
        $amount   = $request->input('amount');
        $currency = $request->input('currency');

        // Correct argument order: (data, tran_id, amount, currency)
        if (! Sslcommerz::validatePayment($request->all(), $tranId, $amount, $currency)) {
            return response('Invalid IPN', 400);
        }

        $payment = Payment::where('transaction_id', $tranId)
            ->where('status', 'pending')
            ->with('plan')
            ->first();

        if (! $payment) {
            // Already handled by success callback
            return response('Already processed', 200);
        }

        $payment->update([
            'status'           => 'completed',
            'gateway_response' => $request->all(),
        ]);

        Subscription::where('user_id', $payment->user_id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        $sub = Subscription::create([
            'user_id'    => $payment->user_id,
            'plan_id'    => $payment->plan_id,
            'starts_at'  => now(),
            'expires_at' => now()->addDays($payment->plan->duration_days),
            'status'     => 'active',
        ]);

        $payment->update(['subscription_id' => $sub->id]);

        return response('IPN processed', 200);
    }
}
