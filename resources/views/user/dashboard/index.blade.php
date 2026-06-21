@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        @foreach($ads as $ad)
            <div class="text-center">
                {!! $ad->ad_code !!}
            </div>
        @endforeach
    </div>

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold">Welcome back, {{ $user->name }} 👋</h1>
            <p class="text-gray-500 text-sm mt-1">
                Plan: <span class="font-semibold text-indigo-600">{{ $plan?->name ?? 'Free' }}</span>
                @if($sub)
                    · Expires {{ $sub->expires_at->format('d M Y') }} ({{ $sub->daysRemaining() }} days left)
                @endif
            </p>
        </div>
        <a href="{{ route('user.plans') }}" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm hover:bg-indigo-700 font-medium">
            {{ $sub ? '⬆ Upgrade Plan' : 'View Plans' }}
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach([
            ['label' => 'Total Links',  'value' => $stats['total_links'],  'color' => 'indigo'],
            ['label' => 'Active Links', 'value' => $stats['active_links'], 'color' => 'green'],
            ['label' => 'Total Clicks', 'value' => $stats['total_clicks'], 'color' => 'blue'],
            ['label' => 'Clicks Today', 'value' => $stats['clicks_today'], 'color' => 'purple'],
        ] as $stat)
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 text-center">
            <p class="text-2xl font-bold text-{{ $stat['color'] }}-600">{{ number_format($stat['value']) }}</p>
            <p class="text-gray-500 text-sm mt-1">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Shorten Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <h2 class="font-semibold mb-4 text-gray-800">🔗 Shorten a New URL</h2>
        <form action="{{ route('links.shorten') }}" method="POST">
            @csrf
            @if(session('short_url'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between gap-3">
                    <a href="{{ session('short_url') }}" target="_blank" class="text-green-800 font-medium text-sm truncate">{{ session('short_url') }}</a>
                    <button type="button"
                        onclick="navigator.clipboard.writeText('{{ session('short_url') }}').then(() => this.textContent = '✅ Copied!')"
                        class="shrink-0 text-xs bg-green-600 text-white px-3 py-1.5 rounded-md">Copy</button>
                </div>
            @endif
            <div class="flex flex-col sm:flex-row gap-3">
                <input type="url" name="url" placeholder="https://your-long-url.com/..." required
                    class="flex-1 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @if($plan?->custom_alias)
                <input type="text" name="custom_alias" placeholder="custom-alias (optional)"
                    class="border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none w-52">
                @endif
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 text-sm font-medium">
                    Shorten
                </button>
            </div>
            @error('url')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
            @error('custom_alias')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror

            @if(!$user->canCreateLink())
                <p class="text-amber-600 text-xs mt-2">⚠️ You've reached your plan's link limit.
                    <a href="{{ route('user.plans') }}" class="underline">Upgrade to create more.</a>
                </p>
            @endif
        </form>
    </div>

    {{-- Recent Links --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="font-semibold">Recent Links</h2>
            <a href="{{ route('user.links') }}" class="text-indigo-600 text-sm hover:underline">View all →</a>
        </div>

        @if($links->isEmpty())
            <div class="text-center py-16 text-gray-400">
                <p class="text-5xl mb-3">🔗</p>
                <p class="text-sm">No links yet. Paste a URL above to get started!</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-6 py-3 text-left">Short URL</th>
                        <th class="px-6 py-3 text-left">Original URL</th>
                        <th class="px-6 py-3 text-left">Clicks</th>
                        <th class="px-6 py-3 text-left">Expires</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @foreach($links as $link)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3">
                        <a href="{{ $link->shortUrl() }}" target="_blank" class="text-indigo-600 hover:underline font-medium">
                            {{ $link->custom_alias ?? $link->short_code }}
                        </a>
                        <button title="Copy"
                            onclick="navigator.clipboard.writeText('{{ $link->shortUrl() }}').then(() => this.textContent = '✅')"
                            class="ml-1 text-gray-400 hover:text-gray-600 text-xs">📋</button>
                    </td>
                    <td class="px-6 py-3 text-gray-500 max-w-xs">
                        <span class="truncate block" title="{{ $link->original_url }}">{{ Str::limit($link->original_url, 45) }}</span>
                    </td>
                    <td class="px-6 py-3 font-medium">{{ number_format($link->click_count) }}</td>
                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $link->expires_at?->format('d M Y') ?? '—' }}</td>
                    <td class="px-6 py-3">
                        @if($link->isExpired())
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs">Expired</span>
                        @elseif($link->is_active)
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Active</span>
                        @else
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-3">
                        <form action="{{ route('user.links.destroy', $link) }}" method="POST"
                              onsubmit="return confirm('Delete this link?')">
                            @csrf @method('DELETE')
                            <button class="text-red-400 hover:text-red-600 text-xs font-medium">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-50">{{ $links->links() }}</div>
        @endif
    </div>
</div>
@endsection
