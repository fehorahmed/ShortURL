<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use \Raziul\Sslcommerz\Facades\Sslcommerz;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $currentPlan = auth()->user()->currentPlan();
        $sub = auth()->user()->activeSubscription;

        return view('user.plans.index', compact('plans', 'currentPlan', 'sub'));
    }

    public function purchase(Plan $plan)
    {
        $user = auth()->user();

        if ($plan->isFree()) {
            return back()->with('error', 'This is the free plan.');
        }

        $tranId = 'TXN_'.uniqid();

        $payment = Payment::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'currency' => $plan->currency,
            'gateway' => 'sslcommerz',
            'transaction_id' => $tranId,
            'status' => 'pending',
        ]);

        // SSLCommerz direct API call — no package needed
        // $baseUrl = config('sslcommerz.sandbox')
        //     ? 'https://sandbox.sslcommerz.com'
        //     : 'https://securepay.sslcommerz.com';
        $response = Sslcommerz::setOrder($plan->price, $tranId, $plan->name)
            ->setCustomer($user->name, $user->email)
            // ->setShippingInfo($itemsQuantity, $address)
            ->makePayment();

        if ($response->success()) {
            // payment initiated, redirect to payment page
            return redirect($response->gatewayPageURL());
        } else {
            dd($data = $response->json()); // Handle payment initiation failure
            // Handle payment failure
        }

        $response = Http::asForm()->post($baseUrl.'/gwprocess/v4/api.php', [
            'store_id' => config('sslcommerz.store_id'),
            'store_passwd' => config('sslcommerz.store_passwd'),
            'total_amount' => $plan->price,
            'currency' => 'BDT',
            'tran_id' => $tranId,
            'success_url' => route('user.payment.success'),
            'fail_url' => route('user.payment.fail'),
            'cancel_url' => route('user.plans'),
            'cus_name' => $user->name,
            'cus_email' => $user->email,
            'cus_phone' => '01700000000',
            'cus_add1' => 'Dhaka',
            'cus_city' => 'Dhaka',
            'cus_country' => 'Bangladesh',
            'product_name' => $plan->name.' Plan',
            'product_category' => 'Software',
            'product_profile' => 'general',
            'shipping_method' => 'NO',
            'num_of_item' => 1,
            'weight_of_items' => 0,
            'logistic_pickup_id' => 0,
            'logistic_delivery_type' => 0,
        ]);

        $data = $response->json();

        if (isset($data['GatewayPageURL'])) {
            return redirect($data['GatewayPageURL']);
        }

        $payment->delete();

        return back()->with('error', 'Payment initiation failed: '.($data['failedreason'] ?? 'Unknown error'));
    }

    public function paymentSuccess(Request $request)
    {
        $payment = Payment::where('transaction_id', $request->tran_id)->first();

        if (! $payment) {
            return redirect()->route('user.plans')->with('error', 'Payment not found.');
        }

        if ($request->status === 'VALID' || $request->status === 'VALIDATED') {
            $payment->update([
                'status' => 'completed',
                'gateway_response' => $request->all(),
            ]);

            // Cancel any existing active subscription
            auth()->user()->subscriptions()
                ->where('status', 'active')
                ->update(['status' => 'cancelled']);

            $sub = Subscription::create([
                'user_id' => $payment->user_id,
                'plan_id' => $payment->plan_id,
                'starts_at' => now(),
                'expires_at' => now()->addDays($payment->plan->duration_days),
                'status' => 'active',
            ]);

            $payment->update(['subscription_id' => $sub->id]);

            return redirect()->route('user.dashboard')->with('success', 'Plan activated! Enjoy your '.$payment->plan->name.' plan.');
        }

        $payment->update(['status' => 'failed']);

        return redirect()->route('user.plans')->with('error', 'Payment failed. Please try again.');
    }

    public function paymentFail()
    {
        return redirect()->route('user.plans')->with('error', 'Payment was cancelled or failed.');
    }
}
