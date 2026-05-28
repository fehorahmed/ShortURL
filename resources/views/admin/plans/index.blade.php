@extends('layouts.admin')
@section('title', 'Manage Plans')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Plans</h1>
    <button onclick="document.getElementById('create-plan-modal').classList.remove('hidden')"
        class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">+ New Plan</button>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-6 py-3 text-left">Name</th>
                <th class="px-6 py-3 text-left">Price</th>
                <th class="px-6 py-3 text-left">Duration</th>
                <th class="px-6 py-3 text-left">Max Links</th>
                <th class="px-6 py-3 text-left">Link Expiry</th>
                <th class="px-6 py-3 text-left">Ads</th>
                <th class="px-6 py-3 text-left">Alias</th>
                <th class="px-6 py-3 text-left">Status</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
        @foreach($plans as $plan)
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-3 font-medium">{{ $plan->name }}</td>
            <td class="px-6 py-3">{{ $plan->formattedPrice() }}</td>
            <td class="px-6 py-3 text-gray-500">{{ $plan->duration_days }}d</td>
            <td class="px-6 py-3">{{ $plan->max_links == -1 ? '∞' : $plan->max_links }}</td>
            <td class="px-6 py-3 text-gray-500">{{ $plan->link_expiry_days }}d</td>
            <td class="px-6 py-3">{{ $plan->show_ads ? '✅' : '❌' }}</td>
            <td class="px-6 py-3">{{ $plan->custom_alias ? '✅' : '❌' }}</td>
            <td class="px-6 py-3">
                <span class="px-2 py-0.5 rounded-full text-xs {{ $plan->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $plan->is_active ? 'Active' : 'Hidden' }}
                </span>
            </td>
            <td class="px-6 py-3">
                <div class="flex gap-3">
                    <button onclick="openEdit({{ $plan->toJson() }})"
                        class="text-indigo-600 hover:underline text-xs">Edit</button>
                    <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST"
                          onsubmit="return confirm('Delete plan {{ $plan->name }}?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-600 text-xs">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

{{-- Create Modal --}}
<div id="create-plan-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-bold">New Plan</h2>
            <button onclick="document.getElementById('create-plan-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>
        <form action="{{ route('admin.plans.store') }}" method="POST" class="space-y-4">
            @csrf
            @include('admin.plans._form')
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700">Create Plan</button>
                <button type="button" onclick="document.getElementById('create-plan-modal').classList.add('hidden')"
                    class="flex-1 border border-gray-300 text-gray-600 py-2.5 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="edit-plan-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-lg font-bold">Edit Plan</h2>
            <button onclick="document.getElementById('edit-plan-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>
        <form id="edit-form" method="POST" class="space-y-4">
            @csrf @method('PUT')
            @include('admin.plans._form', ['edit' => true])
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700">Save Changes</button>
                <button type="button" onclick="document.getElementById('edit-plan-modal').classList.add('hidden')"
                    class="flex-1 border border-gray-300 text-gray-600 py-2.5 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(plan) {
    const form = document.getElementById('edit-form');
    form.action = '/admin/plans/' + plan.id;
    form.querySelector('[name="name"]').value          = plan.name;
    form.querySelector('[name="description"]').value   = plan.description ?? '';
    form.querySelector('[name="price"]').value         = plan.price;
    form.querySelector('[name="currency"]').value      = plan.currency;
    form.querySelector('[name="duration_days"]').value = plan.duration_days;
    form.querySelector('[name="max_links"]').value     = plan.max_links;
    form.querySelector('[name="link_expiry_days"]').value = plan.link_expiry_days;
    form.querySelector('[name="sort_order"]').value    = plan.sort_order;
    form.querySelector('[name="show_ads"]').checked    = !!plan.show_ads;
    form.querySelector('[name="custom_alias"]').checked= !!plan.custom_alias;
    form.querySelector('[name="analytics"]').checked   = !!plan.analytics;
    form.querySelector('[name="is_active"]').checked   = !!plan.is_active;
    document.getElementById('edit-plan-modal').classList.remove('hidden');
}
</script>
@endsection
