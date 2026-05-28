<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — ShortURL</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-900 text-gray-300 flex flex-col fixed inset-y-0 z-40">
        <div class="px-6 py-5 border-b border-gray-800">
            <a href="{{ route('home') }}" class="text-white font-bold text-lg">⚡ ShortURL</a>
            <p class="text-gray-500 text-xs mt-0.5">Admin Panel</p>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-0.5 text-sm overflow-y-auto">
            @php $r = request()->routeIs(...[]); @endphp
            @foreach([
                ['route' => 'admin.dashboard',      'icon' => '📊', 'label' => 'Dashboard'],
                ['route' => 'admin.users.index',    'icon' => '👥', 'label' => 'Users'],
                ['route' => 'admin.plans.index',    'icon' => '💳', 'label' => 'Plans'],
                ['route' => 'admin.ads.index',      'icon' => '📢', 'label' => 'Advertisements'],
                ['route' => 'admin.links.index',    'icon' => '🔗', 'label' => 'All Links'],
                ['route' => 'admin.payments.index', 'icon' => '💰', 'label' => 'Payments'],
                ['route' => 'admin.settings.index', 'icon' => '⚙️',  'label' => 'Settings'],
            ] as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs($item['route']) ? 'bg-indigo-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                <span class="text-base">{{ $item['icon'] }}</span>
                {{ $item['label'] }}
            </a>
            @endforeach
        </nav>
        <div class="px-4 py-4 border-t border-gray-800 text-xs text-gray-500">
            <p>{{ auth()->user()->name }}</p>
            <form action="{{ route('logout') }}" method="POST" class="mt-1">
                @csrf
                <button class="text-gray-500 hover:text-red-400">Logout</button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="ml-64 flex-1 p-8 min-h-screen">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">❌ {{ session('error') }}</div>
        @endif
        @yield('content')
    </div>
</div>
</body>
</html>
