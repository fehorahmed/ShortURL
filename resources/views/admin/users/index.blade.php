@extends('layouts.admin')
@section('title', 'Manage Users')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Users</h1>
</div>

<form method="GET" class="bg-white rounded-xl border border-gray-100 p-4 mb-6 flex gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..."
        class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
    <select name="role" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">All Roles</option>
        <option value="user"  {{ request('role') === 'user'  ? 'selected' : '' }}>User</option>
        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
    </select>
    <button class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-indigo-700">Search</button>
    @if(request()->hasAny(['search','role']))
        <a href="{{ route('admin.users.index') }}" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
            <tr>
                <th class="px-6 py-3 text-left">Name</th>
                <th class="px-6 py-3 text-left">Email</th>
                <th class="px-6 py-3 text-left">Plan</th>
                <th class="px-6 py-3 text-left">Role</th>
                <th class="px-6 py-3 text-left">Status</th>
                <th class="px-6 py-3 text-left">Joined</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
        @foreach($users as $user)
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-3 font-medium">{{ $user->name }}</td>
            <td class="px-6 py-3 text-gray-500">{{ $user->email }}</td>
            <td class="px-6 py-3">
                <span class="text-xs {{ $user->activeSubscription ? 'text-indigo-600 font-medium' : 'text-gray-400' }}">
                    {{ $user->activeSubscription?->plan->name ?? 'Free' }}
                </span>
            </td>
            <td class="px-6 py-3">
                <span class="px-2 py-0.5 rounded-full text-xs {{ $user->isAdmin() ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ ucfirst($user->role) }}
                </span>
            </td>
            <td class="px-6 py-3">
                <span class="px-2 py-0.5 rounded-full text-xs {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $user->is_active ? 'Active' : 'Banned' }}
                </span>
            </td>
            <td class="px-6 py-3 text-gray-400 text-xs">{{ $user->created_at->format('d M Y') }}</td>
            <td class="px-6 py-3">
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:underline text-xs">View</a>
                    @if(!$user->isAdmin())
                    <form action="{{ route('admin.users.toggle', $user) }}" method="POST">
                        @csrf
                        <button class="text-xs {{ $user->is_active ? 'text-red-500 hover:text-red-700' : 'text-green-600 hover:text-green-800' }}">
                            {{ $user->is_active ? 'Ban' : 'Unban' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                          onsubmit="return confirm('Delete this user and all their data?')">
                        @csrf @method('DELETE')
                        <button class="text-red-400 hover:text-red-600 text-xs">Delete</button>
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $users->links() }}</div>
</div>
@endsection
