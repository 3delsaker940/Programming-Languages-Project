<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

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
            <h1 class="text-2xl font-bold mb-6">{{ __('admin.admin_panel') }}</h1>
            <ul>
                <li class="mb-5 font-semibold"><a href="#" class="">{{ __('admin.team') }}</a></li>

                <li class="mb-3"><a href="#" class="hover:text-gray-400">{{ __('admin.mohammed_aldebes') }}</a></li>
                <li class="mb-3"><a href="#" class="hover:text-gray-400">{{ __('admin.mhd_adel_saker') }}</a></li>
                <li class="mb-3"><a href="#" class="hover:text-gray-400">{{ __('admin.mutaz') }}</a></li>
                <li class="mb-3"><a href="#" class="hover:text-gray-400">{{ __('admin.omar') }}</a></li>
                <li class="mb-3"><a href="#" class="hover:text-gray-400">{{ __('admin.batoul') }}</a></li>
            </ul>

            <div class="absolute top-80" {{ app()->getLocale() === 'ar' ? 'right-4' : 'left-4' }} >
                <a href="{{ route('lang.switch', app()->getLocale() === 'en' ? 'ar' : 'en') }}" class="flex items-center gap-2 px-4 py-2 bg-white/20 text-white font-semibold rounded-full
                 hover:bg-white/30 hover:scale-105 transition transform shadow-md">
                    <span class="text-sm">
                        {{ app()->getLocale() === 'en' ? 'العربية' : 'English' }}
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                @csrf
                <button type="submit" title="Log out" class="mt-87 mr-5 text-sm w-32 bg-gradient-to-r from-red-500 via-red-600 to-red-700
                             text-white font-bold px-3 py-2 ml-8 rounded-full
                             hover:from-red-600 hover:to-red-800
                               transition-all duration-300 ease-in-out
                               active:translate-y-1 hover:scale-105
                               shadow-lg shadow-red-400/50 hover:shadow-2xl">
                    {{ __('admin.logout') }}
                </button>
            </form>

        </aside>
        <main class="flex-1 p-6 ">
            @yield('content')
        </main>
    </div>
    @yield('scripts')
</body>

</html>
