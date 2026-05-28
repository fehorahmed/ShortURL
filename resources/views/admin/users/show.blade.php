@extends('layouts.admin')
@section('title', 'User: '.$user->name)

@section('content')
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700">← Users</a>
    <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
    <span class="px-2 py-0.5 rounded-full text-xs {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
        {{ $user->is_active ? 'Active' : 'Banned' }}
    </span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- User Info --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold mb-4">Account Info</h3>
        <dl class="space-y-3 text-sm">
            <div><dt class="text-gray-400 text-xs">Email</dt><dd>{{ $user->email }}</dd></div>
            <div><dt class="text-gray-400 text-xs">Role</dt><dd>{{ ucfirst($user->role) }}</dd></div>
            <div><dt class="text-gray-400 text-xs">Joined</dt><dd>{{ $user->created_at->format('d M Y') }}</dd></div>
            <div><dt class="text-gray-400 text-xs">Total Links</dt><dd>{{ $user->shortLinks->count() }}</dd></div>
        </dl>

        @if(!$user->isAdmin())
        <div class="mt-6 pt-4 border-t border-gray-100 space-y-2">
            <form action="{{ route('admin.users.toggle', $user) }}" method="POST">
                @csrf
                <button class="w-full py-2 rounded-lg text-sm font-medium {{ $user->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                    {{ $user->is_active ? 'Ban User' : 'Unban User' }}
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- Assign Plan --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold mb-4">Assign Plan</h3>
        <form action="{{ route('admin.users.assign-plan', $user) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs text-gray-500 block mb-1">Plan</label>
                <select name="plan_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}">{{ $plan->name }} ({{ $plan->formattedPrice() }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-gray-500 block mb-1">Duration (days)</label>
                <input type="number" name="days" value="30" min="1"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button class="w-full bg-indigo-600 text-white py-2 rounded-lg text-sm hover:bg-indigo-700 font-medium">
                Assign Plan
            </button>
        </form>

        @if($user->subscriptions->isNotEmpty())
        <div class="mt-4 pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-500 mb-2">Subscription History</p>
            @foreach($user->subscriptions->take(3) as $sub)
            <div class="flex justify-between text-xs py-1">
                <span>{{ $sub->plan->name }}</span>
                <span class="px-1.5 py-0.5 rounded {{ $sub->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $sub->status }}</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Recent Links --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold mb-4">Recent Links</h3>
        @if($user->shortLinks->isEmpty())
            <p class="text-gray-400 text-sm">No links created.</p>
        @else
        <div class="space-y-2">
            @foreach($user->shortLinks as $link)
            <div class="flex justify-between items-center text-sm py-1 border-b border-gray-50">
                <a href="{{ $link->shortUrl() }}" target="_blank" class="text-indigo-600 hover:underline text-xs">
                    {{ $link->short_code }}
                </a>
                <span class="text-gray-400 text-xs">{{ $link->click_count }} clicks</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection
