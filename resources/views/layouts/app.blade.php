<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel 12 + Alpine js | @yield('title')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-200 text-gray-800">

    <div class="w-7xl mx-auto px-4 sm:px-6 lg:px-8 bg-white mt-8">
        <h1 class="text-3xl font-bold border-b-2 border-gray-300 mb-6 pb-3">
            Laravel 12 + Alpine js
        </h1>

        @yield('content')
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        lucide.createIcons();
    </script>

</body>
</html>