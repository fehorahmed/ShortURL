@extends('layouts.admin')
@section('title', 'All Links')

@section('content')
<h1 class="text-2xl font-bold mb-6">All Short Links</h1>

<form method="GET" class="bg-white rounded-xl border border-gray-100 p-4 mb-6 flex gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search URL or short code..."
        class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">All</option>
        <option value="active"  {{ request('status') === 'active'  ? 'selected' : '' }}>Active</option>
        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
    </select>
    <button class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-indigo-700">Filter</button>
    @if(request()->hasAny(['search','status']))
        <a href="{{ route('admin.links.index') }}" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-6 py-3 text-left">Short Code</th>
                <th class="px-6 py-3 text-left">Original URL</th>
                <th class="px-6 py-3 text-left">User</th>
                <th class="px-6 py-3 text-left">Clicks</th>
                <th class="px-6 py-3 text-left">Expires</th>
                <th class="px-6 py-3 text-left">Status</th>
                <th class="px-6 py-3 text-left">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
        @forelse($links as $link)
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-3">
                <a href="{{ $link->shortUrl() }}" target="_blank" class="text-indigo-600 hover:underline font-mono text-xs">
                    {{ $link->custom_alias ?? $link->short_code }}
                </a>
            </td>
            <td class="px-6 py-3 text-gray-500 max-w-xs">
                <span class="truncate block text-xs" title="{{ $link->original_url }}">{{ Str::limit($link->original_url, 50) }}</span>
            </td>
            <td class="px-6 py-3 text-xs">{{ $link->user?->name ?? 'Guest' }}</td>
            <td class="px-6 py-3 font-medium">{{ number_format($link->click_count) }}</td>
            <td class="px-6 py-3 text-gray-400 text-xs">{{ $link->expires_at?->format('d M Y') ?? '—' }}</td>
            <td class="px-6 py-3">
                @if($link->isExpired())
                    <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs">Expired</span>
                @elseif($link->is_active)
                    <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Active</span>
                @else
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full text-xs">Inactive</span>
                @endif
            </td>
            <td class="px-6 py-3">
                <form action="{{ route('admin.links.destroy', $link) }}" method="POST"
                      onsubmit="return confirm('Delete this link?')">
                    @csrf @method('DELETE')
                    <button class="text-red-400 hover:text-red-600 text-xs font-medium">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400">No links found.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $links->links() }}</div>
</div>
@endsection
