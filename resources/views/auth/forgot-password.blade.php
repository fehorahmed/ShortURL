{{-- resources/views/auth/forgot-password.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — ShortURL</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 to-purple-50 min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <a href="{{ route('home') }}" class="text-3xl font-extrabold text-indigo-600">⚡ ShortURL</a>
        <p class="text-gray-500 mt-1 text-sm">Reset your password</p>
    </div>
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <p class="text-sm text-gray-500 mb-5">Enter your email and we will send you a password reset link.</p>

        @if(session('status'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 font-semibold text-sm">
                Send Reset Link
            </button>
        </form>
        <p class="text-center text-sm text-gray-500 mt-5">
            <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">← Back to login</a>
        </p>
    </div>
</div>
</body>
</html>