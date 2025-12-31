<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gradient-to-br from-[#0b0b0f] via-[#111827] to-[#020617] flex items-center justify-center min-h-screen">

    <form method="POST" action="{{ route('admin.login.post') }}" class="w-96 p-8 space-y-5 rounded-2xl bg-white/15 border 
        border-white/25 backdrop-blur-md
        shadow-2xl transition-transform duration-300 hover:scale-[1.02] text-white ">
        @csrf

        <h2 class="text-2xl font-bold text-center">Admin Login</h2>

        @error('number')
            <p class="text-red-400 text-m">{{ $message }}</p>
        @enderror

        @error('password')
            <p class="text-red-400 text-m">{{ $message }}</p>
        @enderror

        @error('login')
            <p class="text-red-400 text-m">{{ $message }}</p>
        @enderror

        <input type="text" name="number" placeholder="Phone Number" 
            class="w-full p-3 rounded-full bg-white/15 border border-white/25
            transition-shadow duration-300
            hover:shadow-[0_0_40px_rgba(255,255,255,0.3)]" required>

        <input type="password" name="password" placeholder="Password" 
            class="w-full p-3 rounded-full placeholder-gray-400 focus:placeholder-gray-500 bg-white/15 border border-white/25
            transition-shadow duration-300
            hover:shadow-[0_0_40px_rgba(255,255,255,0.3)]" required>

        <button
            class="w-full mt-3 text-white py-2 rounded-full cursor-pointer transform hover:scale-105 shadow-md bg-blue-600 border border-blue-500
            transition-shadow duration-300
            hover:shadow-[0_0_40px_rgba(255,255,255,0.3)]">
            Login
        </button>

    </form>

</body>

</html>