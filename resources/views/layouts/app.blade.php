<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Short URL')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- favicon --}}
    <link rel="icon" href="{{ asset('fav.png') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('head')
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">

<nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <a href="{{ route('home') }}" class="text-xl font-bold text-indigo-600">⚡ Short URL</a>
        <div class="flex items-center gap-4">
            @auth
                <a href="{{ route('user.dashboard') }}" class="text-sm text-gray-600 hover:text-indigo-600">Dashboard</a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-red-600 hover:text-red-700">Admin</a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button class="text-sm text-gray-500 hover:text-red-500">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-indigo-600">Login</a>
                <a href="{{ route('register') }}" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700">Sign Up Free</a>
            @endauth
        </div>
    </div>
</nav>

@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-400 p-4 max-w-7xl mx-auto mt-4 w-full rounded">
        <p class="text-green-800 text-sm">✅ {{ session('success') }}</p>
    </div>
@endif
@if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-400 p-4 max-w-7xl mx-auto mt-4 w-full rounded">
        <p class="text-red-800 text-sm">❌ {{ session('error') }}</p>
    </div>
@endif

<main class="flex-1">@yield('content')</main>

<footer class="bg-gray-800 text-gray-400 text-center py-6 mt-16 text-sm">
    &copy; {{ date('Y') }} ShortURL. All rights reserved.
</footer>

@stack('scripts')
</body>
</html>
