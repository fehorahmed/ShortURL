<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — ShortURL</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 to-purple-50 min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <a href="{{ route('home') }}" class="text-3xl font-extrabold text-indigo-600">⚡ ShortURL</a>
        <p class="text-gray-500 mt-1 text-sm">Sign in to your account</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-8">

        @if(session('status'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs text-indigo-600 hover:underline">Forgot password?</a>
                    @endif
                </div>
                <input type="password" name="password" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('password') border-red-400 @enderror">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" class="rounded">
                <label for="remember" class="text-sm text-gray-600">Remember me</label>
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 font-semibold text-sm transition-colors">
                Sign In
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-indigo-600 hover:underline font-medium">Sign up free</a>
        </p>
    </div>
</div>
</body>
</html>