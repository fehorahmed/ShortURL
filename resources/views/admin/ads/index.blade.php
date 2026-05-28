@extends('layouts.admin')
@section('title', 'Advertisements')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Advertisements</h1>
    <button onclick="document.getElementById('create-ad-modal').classList.remove('hidden')"
        class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">+ New Ad</button>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-6 py-3 text-left">Name</th>
                <th class="px-6 py-3 text-left">Type</th>
                <th class="px-6 py-3 text-left">Placement</th>
                <th class="px-6 py-3 text-left">Status</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
        @forelse($ads as $ad)
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-3 font-medium">{{ $ad->name }}</td>
            <td class="px-6 py-3">
                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">{{ $ad->type }}</span>
            </td>
            <td class="px-6 py-3 text-gray-500 text-xs">{{ str_replace('_', ' ', $ad->placement) }}</td>
            <td class="px-6 py-3">
                <span class="px-2 py-0.5 rounded-full text-xs {{ $ad->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $ad->is_active ? 'Active' : 'Inactive' }}
                </span>
            </td>
            <td class="px-6 py-3">
                <div class="flex items-center gap-3">
                    <button onclick="openEditAd({{ $ad->toJson() }})" class="text-indigo-600 hover:underline text-xs">Edit</button>
                    <form action="{{ route('admin.ads.toggle', $ad) }}" method="POST">
                        @csrf
                        <button class="text-xs {{ $ad->is_active ? 'text-amber-500 hover:text-amber-700' : 'text-green-600 hover:text-green-800' }}">
                            {{ $ad->is_active ? 'Disable' : 'Enable' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.ads.destroy', $ad) }}" method="POST"
                          onsubmit="return confirm('Delete this ad?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-600 text-xs">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">No advertisements yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Placement guide --}}
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800">
    <strong>Placement Guide:</strong>
    <span class="ml-2">redirect_page = shown on the 5-second countdown redirect</span> ·
    <span>homepage = shown on the homepage below the shortener</span> ·
    <span>dashboard_top = shown at the top of user dashboard</span>
</div>

{{-- Create Modal --}}
<div id="create-ad-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 max-h-screen overflow-y-auto">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-bold">New Advertisement</h2>
            <button onclick="document.getElementById('create-ad-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>
        <form action="{{ route('admin.ads.store') }}" method="POST" class="space-y-4">
            @csrf
            @include('admin.ads._form')
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700">Create</button>
                <button type="button" onclick="document.getElementById('create-ad-modal').classList.add('hidden')"
                    class="flex-1 border border-gray-300 text-gray-600 py-2.5 rounded-lg text-sm">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="edit-ad-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 max-h-screen overflow-y-auto">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-bold">Edit Advertisement</h2>
            <button onclick="document.getElementById('edit-ad-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>
        <form id="edit-ad-form" method="POST" class="space-y-4">
            @csrf @method('PUT')
            @include('admin.ads._form', ['edit' => true])
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700">Save</button>
                <button type="button" onclick="document.getElementById('edit-ad-modal').classList.add('hidden')"
                    class="flex-1 border border-gray-300 text-gray-600 py-2.5 rounded-lg text-sm">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditAd(ad) {
    const form = document.getElementById('edit-ad-form');
    form.action = '/admin/ads/' + ad.id;
    form.querySelector('[name="name"]').value      = ad.name;
    form.querySelector('[name="type"]').value      = ad.type;
    form.querySelector('[name="placement"]').value = ad.placement;
    form.querySelector('[name="ad_code"]').value   = ad.ad_code ?? '';
    form.querySelector('[name="image_url"]').value = ad.image_url ?? '';
    form.querySelector('[name="link_url"]').value  = ad.link_url ?? '';
    form.querySelector('[name="sort_order"]').value= ad.sort_order ?? 0;
    form.querySelector('[name="is_active"]').checked = !!ad.is_active;
    document.getElementById('edit-ad-modal').classList.remove('hidden');
}
</script>
@endsection
