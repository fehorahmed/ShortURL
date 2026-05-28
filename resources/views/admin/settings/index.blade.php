@extends('layouts.admin')
@section('title', 'Settings')

@section('content')
<h1 class="text-2xl font-bold mb-6">Settings</h1>

<form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6 max-w-2xl">
    @csrf

    {{-- General --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-semibold text-gray-800 mb-4">General</h2>
        <div class="space-y-4">
            <div>
                <label class="text-sm text-gray-600 block mb-1 font-medium">Site Name</label>
                <input type="text" name="site_name"
                    value="{{ $settings['site_name']->value ?? 'ShortURL' }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="text-sm text-gray-600 block mb-1 font-medium">Site Description</label>
                <input type="text" name="site_description"
                    value="{{ $settings['site_description']->value ?? '' }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="flex items-center gap-3 text-sm text-gray-600 font-medium cursor-pointer">
                    <input type="hidden"   name="maintenance_mode" value="0">
                    <input type="checkbox" name="maintenance_mode" value="1"
                        {{ ($settings['maintenance_mode']->value ?? '0') === '1' ? 'checked' : '' }}
                        class="rounded w-4 h-4">
                    Enable Maintenance Mode
                </label>
                <p class="text-xs text-gray-400 mt-1 ml-7">When enabled, only admins can access the site.</p>
            </div>
        </div>
    </div>

    {{-- Links --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-semibold text-gray-800 mb-4">Links</h2>
        <div class="space-y-4">
            <div>
                <label class="text-sm text-gray-600 block mb-1 font-medium">Default Link Expiry (days)</label>
                <input type="number" name="default_link_expiry" min="1"
                    value="{{ $settings['default_link_expiry']->value ?? 30 }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="text-xs text-gray-400 mt-1">Applied to guest links and users on the free plan.</p>
            </div>
            <div>
                <label class="flex items-center gap-3 text-sm text-gray-600 font-medium cursor-pointer">
                    <input type="hidden"   name="allow_guest_links" value="0">
                    <input type="checkbox" name="allow_guest_links" value="1"
                        {{ ($settings['allow_guest_links']->value ?? '1') === '1' ? 'checked' : '' }}
                        class="rounded w-4 h-4">
                    Allow Guest (non-registered) Link Creation
                </label>
            </div>
        </div>
    </div>

    {{-- Ads --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-semibold text-gray-800 mb-4">Advertisements</h2>
        <div>
            <label class="text-sm text-gray-600 block mb-1 font-medium">Redirect Wait Seconds</label>
            <input type="number" name="redirect_wait_seconds" min="0" max="30"
                value="{{ $settings['redirect_wait_seconds']->value ?? 5 }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <p class="text-xs text-gray-400 mt-1">How long to show the ad countdown before redirecting. Set 0 to skip immediately.</p>
        </div>
    </div>

    <button type="submit"
        class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 font-medium text-sm">
        Save Settings
    </button>
</form>
@endsection
