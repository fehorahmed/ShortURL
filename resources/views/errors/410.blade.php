<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Expired — ShortURL</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
<div class="text-center max-w-md">
    <p class="text-8xl mb-4">⏰</p>
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Link Expired</h1>
    <p class="text-gray-500 mb-4">This short link has expired and is no longer active.</p>
    <p class="text-sm text-gray-400 mb-8">Links on the free plan expire after 30 days. Upgrade for longer expiry.</p>
    <div class="flex gap-3 justify-center">
        <a href="{{ route('home') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-medium text-sm">
            Create New Link
        </a>
        <a href="{{ route('user.plans') }}" class="border border-indigo-600 text-indigo-600 px-6 py-3 rounded-lg hover:bg-indigo-50 font-medium text-sm">
            View Plans
        </a>
    </div>
</div>
</body>
</html>
