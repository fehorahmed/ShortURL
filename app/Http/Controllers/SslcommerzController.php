<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Raziul\Sslcommerz\Facades\Sslcommerz;
use App\Models\Payment;
use App\Models\Subscription;

class SslcommerzController extends Controller
{
    public function initiatePayment()
    {
        $post_data = [
            'total_amount' => '1000',
            'currency' => 'BDT',
            'tran_id' => uniqid('SSLC_', true), // Generate unique tracking ID

            // Callback URLs matching your defined routes
            'success_url' => url('/pay-success'),
            'fail_url' => url('/pay-fail'),
            'cancel_url' => url('/pay-cancel'),
            'ipn_url' => url('/pay-ipn'),

            // Customer Details (Mandatory fields)
            'cus_name' => 'John Doe',
            'cus_email' => 'john.doe@example.com',
            'cus_add1' => 'Dhaka',
            'cus_city' => 'Dhaka',
            'cus_country' => 'Bangladesh',
            'cus_phone' => '01700000000',

            // Shipping & Product Details
            'shipping_method' => 'NO',
            'product_name' => 'Computer',
            'product_category' => 'Electronics',
            'product_profile' => 'general',
        ];

        // Requests token from SSLCommerz and redirects the user
        $paymentUrl = Sslcommerz::initiatePayment($post_data);

        return redirect()->to($paymentUrl);
    }

    public function success(Request $request)
    {
        // dd($request->all()); // For debugging, you can log or inspect the request data
        // Capture transaction ID from the payload
        $tran_id = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');

        // Always validate the transaction signature with SSLCommerz servers
        $validation = Sslcommerz::validatePayment($request->all(), $tran_id, $amount, $currency);

        if ($validation) {
            // Transaction verified. Update database record to 'Paid'
            $payment = Payment::where('transaction_id', $tran_id)->first();
            if ($payment) {
                // $payment->update(['status' => 'completed']);
                $payment->update([
                    'status' => 'completed',
                    'gateway_response' => $request->all(),
                ]);
                // dd(auth()->user());
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
            }
            return response()->json(['status' => 'Success', 'message' => 'Payment Completed Successfully.']);
        }

        return response()->json(['status' => 'Failed', 'message' => 'Validation mismatched.']);
    }

    public function failure(Request $request)
    {
        return response()->json(['status' => 'Failed', 'message' => 'Payment Transaction Failed.']);
    }

    public function cancel()
    {
        return response()->json(['status' => 'Cancelled', 'message' => 'User Cancelled Payment.']);
    }

    public function ipn(Request $request)
    {
        // Instant Payment Notification handler (Server-to-Server communication)
        $tran_id = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');

        if (Sslcommerz::validatePayment($tran_id, $amount, $currency, $request->all())) {
            // Safely update transaction status on background disconnects
            return response('Valid IPN Received', 200);
        }

        return response('Invalid IPN', 400);
    }
}
