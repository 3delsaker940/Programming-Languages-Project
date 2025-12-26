<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-blue-950 text-white p-6">
            <h1 class="text-2xl font-bold mb-6">Admin Panel</h1>
            <ul>
                <li class="mb-3"><a href="#" class="hover:text-gray-400">Dashboard</a></li>
                <li class="mb-3"><a href="#" class="hover:text-gray-400">Users</a></li>
                <li class="mb-3"><a href="#" class="hover:text-gray-400">Settings</a></li>
            </ul>
        </aside>
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
    @yield('scripts')
</body>
</html>