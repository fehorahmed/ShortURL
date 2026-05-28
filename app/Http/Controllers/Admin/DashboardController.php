<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\ShortLink;
use App\Models\Subscription;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'     => User::count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'total_links'     => ShortLink::count(),
            'links_today'     => ShortLink::whereDate('created_at', today())->count(),
            'total_revenue'   => Payment::where('status', 'completed')->sum('amount'),
            'revenue_month'   => Payment::where('status', 'completed')->whereMonth('created_at', now()->month)->sum('amount'),
            'active_subs'     => Subscription::where('status', 'active')->where('expires_at', '>', now())->count(),
        ];

        $recentUsers    = User::latest()->take(5)->get();
        $recentPayments = Payment::with(['user', 'plan'])->latest()->take(5)->get();

        return view('admin.dashboard.index', compact('stats', 'recentUsers', 'recentPayments'));
    }
}
