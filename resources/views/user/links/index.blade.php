@extends('layouts.app')
@section('title', 'My Links')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">My Links</h1>
        <a href="{{ route('user.dashboard') }}" class="text-indigo-600 text-sm hover:underline">← Back to Dashboard</a>
    </div>

    {{-- Search/Filter --}}
    <form method="GET" class="bg-white rounded-xl border border-gray-100 p-4 mb-6 flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by URL or code..."
            class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
            <option value="">All Status</option>
            <option value="active"  {{ request('status') === 'active'  ? 'selected' : '' }}>Active</option>
            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
        </select>
        <button class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-indigo-700">Filter</button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('user.links') }}" class="border border-gray-300 text-gray-600 px-5 py-2 rounded-lg text-sm hover:bg-gray-50">Clear</a>
        @endif
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @if($links->isEmpty())
            <div class="text-center py-16 text-gray-400">
                <p class="text-4xl mb-3">🔍</p>
                <p>No links found.</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-3 text-left">Short URL</th>
                        <th class="px-6 py-3 text-left">Original URL</th>
                        <th class="px-6 py-3 text-left">Clicks</th>
                        <th class="px-6 py-3 text-left">Created</th>
                        <th class="px-6 py-3 text-left">Expires</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                @foreach($links as $link)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">
                        <a href="{{ $link->shortUrl() }}" target="_blank" class="text-indigo-600 hover:underline font-medium">
                            {{ $link->custom_alias ?? $link->short_code }}
                        </a>
                    </td>
                    <td class="px-6 py-3 text-gray-500 max-w-xs">
                        <a href="{{ $link->original_url }}" target="_blank" class="truncate block hover:underline" title="{{ $link->original_url }}">
                            {{ Str::limit($link->original_url, 50) }}
                        </a>
                    </td>
                    <td class="px-6 py-3">{{ number_format($link->click_count) }}</td>
                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $link->created_at->format('d M Y') }}</td>
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
        <div class="px-6 py-4">{{ $links->links() }}</div>
        @endif
    </div>
</div>
@endsection
