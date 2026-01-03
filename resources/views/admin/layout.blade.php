<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-white via-indigo-50 to-blue-100 font-sans">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-blue-950 text-white p-6 rounded-lg sticky top-0 h-screen overflow-y-auto">
            <h1 class="text-2xl font-bold mb-6">Admin Panel</h1>
            <ul>
                <li class="mb-5 font-semibold"><a href="#" class="">Team:</a></li>

                <li class="mb-3"><a href="#" class="hover:text-gray-400">Mohammed Aldebes</a></li>
                <li class="mb-3"><a href="#" class="hover:text-gray-400">Adel Sakr</a></li>
                <li class="mb-3"><a href="#" class="hover:text-gray-400">Mutaz Al-homsi</a></li>
                <li class="mb-3"><a href="#" class="hover:text-gray-400">Omar Alaa-al-dein</a></li>
                <li class="mb-3"><a href="#" class="hover:text-gray-400">Batoul Suliman</a></li>

                <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                    @csrf
                    <button type="submit" title="Log out" class="mt-90 text-sm w-32 bg-gradient-to-r from-red-500 via-red-600 to-red-700
                             text-white font-bold px-3 py-2 ml-8 rounded-full
                             hover:from-red-600 hover:to-red-800
                               transition-all duration-300 ease-in-out
                               active:translate-y-1 hover:scale-105
                               shadow-lg shadow-red-400/50 hover:shadow-2xl">
                        Log Out
                    </button>
                </form>
            </ul>
        </aside>
        <main class="flex-1 p-6 ">
            @yield('content')
        </main>
    </div>
    @yield('scripts')
</body>

</html>