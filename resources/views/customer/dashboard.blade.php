<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Customer - Barber Woi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-[#1A365D]">Selamat Datang, {{ Auth::user()->name }}!</h1>
            <p class="text-gray-600 mt-2">Ini adalah halaman dashboard customer.</p>
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="mt-4 inline-block text-[#D4AF37] hover:underline">
                Keluar
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </div>
</body>
</html>