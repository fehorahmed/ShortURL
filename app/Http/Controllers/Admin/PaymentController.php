<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with(['user', 'plan'])
            ->when($request->search, fn($q, $s) => $q->where('transaction_id', 'like', "%$s%"))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20);

        $totalRevenue = Payment::where('status', 'completed')->sum('amount');

        return view('admin.payments.index', compact('payments', 'totalRevenue'));
    }
}
