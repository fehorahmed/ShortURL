<div class="grid grid-cols-2 gap-4">
    <div class="col-span-2">
        <label class="text-xs text-gray-500 block mb-1">Plan Name *</label>
        <input type="text" name="name" required placeholder="e.g. Pro"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div class="col-span-2">
        <label class="text-xs text-gray-500 block mb-1">Description</label>
        <input type="text" name="description" placeholder="Short description"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="text-xs text-gray-500 block mb-1">Price *</label>
        <input type="number" name="price" required min="0" step="0.01" placeholder="0"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="text-xs text-gray-500 block mb-1">Currency</label>
        <input type="text" name="currency" value="BDT" maxlength="10"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="text-xs text-gray-500 block mb-1">Duration (days) *</label>
        <input type="number" name="duration_days" required min="1" placeholder="30"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="text-xs text-gray-500 block mb-1">Max Links (-1 = unlimited) *</label>
        <input type="number" name="max_links" required min="-1" placeholder="50"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="text-xs text-gray-500 block mb-1">Link Expiry (days) *</label>
        <input type="number" name="link_expiry_days" required min="1" placeholder="30"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="text-xs text-gray-500 block mb-1">Sort Order</label>
        <input type="number" name="sort_order" min="0" placeholder="0"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
</div>
<div class="grid grid-cols-2 gap-3 pt-2">
    @foreach([
        ['name' => 'show_ads',     'label' => 'Show Ads on Redirect'],
        ['name' => 'custom_alias', 'label' => 'Allow Custom Aliases'],
        ['name' => 'analytics',    'label' => 'Click Analytics'],
        ['name' => 'is_active',    'label' => 'Plan Active / Visible'],
    ] as $toggle)
    <label class="flex items-center gap-2 text-sm cursor-pointer">
        <input type="checkbox" name="{{ $toggle['name'] }}" value="1" class="rounded">
        {{ $toggle['label'] }}
    </label>
    @endforeach
</div>
