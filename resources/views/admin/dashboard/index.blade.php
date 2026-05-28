@extends('layouts.admin')
@section('title', 'Admin Dashboard')

@section('content')
<h1 class="text-2xl font-bold mb-6">Dashboard</h1>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['label' => 'Total Users',     'value' => $stats['total_users'],    'sub' => '+'.$stats['new_users_today'].' today',   'color' => 'indigo'],
        ['label' => 'Total Links',     'value' => $stats['total_links'],    'sub' => '+'.$stats['links_today'].' today',       'color' => 'blue'],
        ['label' => 'Active Subs',     'value' => $stats['active_subs'],    'sub' => 'paid subscribers',                       'color' => 'green'],
        ['label' => 'Revenue (Month)', 'value' => 'BDT '.number_format($stats['revenue_month'],0), 'sub' => 'Total: BDT '.number_format($stats['total_revenue'],0), 'color' => 'purple'],
    ] as $card)
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">{{ $card['label'] }}</p>
        <p class="text-2xl font-bold text-{{ $card['color'] }}-600">{{ $card['value'] }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $card['sub'] }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent Users --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-semibold">Recent Users</h3>
            <a href="{{ route('admin.users.index') }}" class="text-indigo-600 text-sm hover:underline">View all</a>
        </div>
        <table class="w-full text-sm">
            <tbody>
            @foreach($recentUsers as $user)
            <tr class="border-b border-gray-50 hover:bg-gray-50">
                <td class="px-5 py-3 font-medium">{{ $user->name }}</td>
                <td class="px-5 py-3 text-gray-500 text-xs">{{ $user->email }}</td>
                <td class="px-5 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $user->is_active ? 'Active' : 'Banned' }}
                    </span>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- Recent Payments --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-semibold">Recent Payments</h3>
            <a href="{{ route('admin.payments.index') }}" class="text-indigo-600 text-sm hover:underline">View all</a>
        </div>
        <table class="w-full text-sm">
            <tbody>
            @foreach($recentPayments as $payment)
            <tr class="border-b border-gray-50 hover:bg-gray-50">
                <td class="px-5 py-3 font-medium">{{ $payment->user->name }}</td>
                <td class="px-5 py-3 text-gray-500 text-xs">{{ $payment->plan->name }}</td>
                <td class="px-5 py-3 font-medium text-xs">{{ $payment->currency }} {{ number_format($payment->amount,0) }}</td>
                <td class="px-5 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs
                        {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' :
                           ($payment->status === 'pending'   ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </td>
            </tr>
            @endforeach
            @if($recentPayments->isEmpty())
                <tr><td colspan="4" class="px-5 py-6 text-center text-gray-400 text-sm">No payments yet.</td></tr>
            @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
