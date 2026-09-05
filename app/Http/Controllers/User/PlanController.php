<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use Illuminate\Http\Request;
use Raziul\Sslcommerz\Facades\Sslcommerz;

class PlanController extends Controller
{
    public function index()
    {
        $plans       = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $currentPlan = auth()->user()->currentPlan();
        $sub         = auth()->user()->activeSubscription;

        return view('user.plans.index', compact('plans', 'currentPlan', 'sub'));
    }

    public function purchase(Plan $plan)
    {
        $user = auth()->user();

        if ($plan->isFree()) {
            return back()->with('error', 'This is the free plan.');
        }

        $tranId = 'TXN_' . uniqid();

        // Create pending payment record first
        Payment::create([
            'user_id'        => $user->id,
            'plan_id'        => $plan->id,
            'amount'         => $plan->price,
            'currency'       => $plan->currency,
            'gateway'        => 'sslcommerz',
            'transaction_id' => $tranId,
            'status'         => 'pending',
        ]);

        // Initiate SSLCommerz payment
        $response = Sslcommerz::setOrder($plan->price, $tranId, $plan->name)
            ->setCustomer($user->name, $user->email, $user->phone ?? '01700000000')
            ->makePayment();

        if ($response->success()) {
            return redirect($response->gatewayPageURL());
        }

        // Payment initiation failed — delete pending record and go back
        Payment::where('transaction_id', $tranId)->delete();

        return back()->with('error', 'Could not initiate payment. Please try again.');
    }

    // These are kept for the old Http routes — but SSLCommerz now posts to /sslcommerz/* routes
    public function paymentFail()
    {
        return redirect()->route('user.plans')->with('error', 'Payment was cancelled or failed.');
    }
}
