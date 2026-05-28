@extends('layouts.admin')
@section('title', 'Payments')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Payments</h1>
    <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-2 text-sm text-green-800">
        Total Revenue: <strong>BDT {{ number_format($totalRevenue, 0) }}</strong>
    </div>
</div>

<form method="GET" class="bg-white rounded-xl border border-gray-100 p-4 mb-6 flex gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search transaction ID..."
        class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">All Status</option>
        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
        <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
        <option value="failed"    {{ request('status') === 'failed'    ? 'selected' : '' }}>Failed</option>
        <option value="refunded"  {{ request('status') === 'refunded'  ? 'selected' : '' }}>Refunded</option>
    </select>
    <button class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-indigo-700">Filter</button>
    @if(request()->hasAny(['search','status']))
        <a href="{{ route('admin.payments.index') }}" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-6 py-3 text-left">Transaction ID</th>
                <th class="px-6 py-3 text-left">User</th>
                <th class="px-6 py-3 text-left">Plan</th>
                <th class="px-6 py-3 text-left">Amount</th>
                <th class="px-6 py-3 text-left">Gateway</th>
                <th class="px-6 py-3 text-left">Status</th>
                <th class="px-6 py-3 text-left">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
        @forelse($payments as $payment)
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-3 font-mono text-xs text-gray-500">{{ $payment->transaction_id ?? '—' }}</td>
            <td class="px-6 py-3">
                <a href="{{ route('admin.users.show', $payment->user) }}" class="text-indigo-600 hover:underline text-xs font-medium">
                    {{ $payment->user->name }}
                </a>
                <p class="text-gray-400 text-xs">{{ $payment->user->email }}</p>
            </td>
            <td class="px-6 py-3 text-xs">{{ $payment->plan->name }}</td>
            <td class="px-6 py-3 font-semibold">{{ $payment->currency }} {{ number_format($payment->amount, 0) }}</td>
            <td class="px-6 py-3">
                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs capitalize">{{ $payment->gateway }}</span>
            </td>
            <td class="px-6 py-3">
                <span class="px-2 py-0.5 rounded-full text-xs
                    {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' :
                       ($payment->status === 'pending'  ? 'bg-yellow-100 text-yellow-700' :
                       ($payment->status === 'refunded' ? 'bg-blue-100 text-blue-700' :
                       'bg-red-100 text-red-700')) }}">
                    {{ ucfirst($payment->status) }}
                </span>
            </td>
            <td class="px-6 py-3 text-gray-400 text-xs">{{ $payment->created_at->format('d M Y H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400">No payments found.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $payments->links() }}</div>
</div>
@endsection
