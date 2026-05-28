{{-- resources/views/errors/404.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Not Found — ShortURL</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
<div class="text-center max-w-md">
    <p class="text-8xl mb-4">🔍</p>
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Link Not Found</h1>
    <p class="text-gray-500 mb-8">This short link doesn't exist or may have been deleted.</p>
    <a href="{{ route('home') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-medium text-sm">
        Create a New Link →
    </a>
</div>
</body>
</html>
