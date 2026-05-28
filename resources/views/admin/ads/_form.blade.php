<div>
    <label class="text-xs text-gray-500 block mb-1">Ad Name *</label>
    <input type="text" name="name" required placeholder="e.g. Adsterra Redirect Top"
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="text-xs text-gray-500 block mb-1">Type *</label>
        <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="adsterra">Adsterra</option>
            <option value="google_adsense">Google AdSense</option>
            <option value="custom_html">Custom HTML</option>
            <option value="image_banner">Image Banner</option>
        </select>
    </div>
    <div>
        <label class="text-xs text-gray-500 block mb-1">Placement *</label>
        <select name="placement" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="redirect_page">Redirect Page (countdown)</option>
            <option value="homepage">Homepage</option>
            <option value="dashboard_top">Dashboard Top</option>
            <option value="dashboard_side">Dashboard Side</option>
        </select>
    </div>
</div>
<div>
    <label class="text-xs text-gray-500 block mb-1">Ad Code / Script</label>
    <textarea name="ad_code" rows="6" placeholder="Paste your Adsterra / Google AdSense script tag here..."
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
    <p class="text-gray-400 text-xs mt-1">For image banners, leave blank and fill Image URL below.</p>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="text-xs text-gray-500 block mb-1">Image URL (for image banner)</label>
        <input type="url" name="image_url" placeholder="https://..."
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="text-xs text-gray-500 block mb-1">Link URL (for image banner)</label>
        <input type="url" name="link_url" placeholder="https://..."
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="text-xs text-gray-500 block mb-1">Sort Order</label>
        <input type="number" name="sort_order" min="0" placeholder="0"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div class="flex items-end pb-2">
        <label class="flex items-center gap-2 text-sm cursor-pointer">
            <input type="checkbox" name="is_active" value="1" class="rounded" checked>
            Active (visible on site)
        </label>
    </div>
</div>
