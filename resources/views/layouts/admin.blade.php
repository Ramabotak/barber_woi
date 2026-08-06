<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Barber Woi')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <aside class="hidden md:flex md:flex-col w-64 bg-brand-navy text-white">
            <div class="p-6 border-b border-white/10">
                <h2 class="text-xl font-bold">Barber Woi</h2>
                <p class="text-sm text-white/60">Panel Admin</p>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" 
                   class="block px-4 py-2 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 font-semibold' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.barbers.index') }}" 
                   class="block px-4 py-2 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('admin.barbers.*') ? 'bg-white/10 font-semibold' : '' }}">
                    Kelola Barber
                </a>
                <a href="{{ route('admin.services.index') }}" 
                   class="block px-4 py-2 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('admin.services.*') ? 'bg-white/10 font-semibold' : '' }}">
                    Kelola Layanan
                </a>
                <a href="#" 
                   class="block px-4 py-2 rounded-lg hover:bg-white/10 transition-colors opacity-50 cursor-not-allowed">
                    Kelola Booking (segera)
                </a>
                <a href="#" 
                   class="block px-4 py-2 rounded-lg hover:bg-white/10 transition-colors opacity-50 cursor-not-allowed">
                    Laporan (segera)
                </a>
                <a href="#" 
                   class="block px-4 py-2 rounded-lg hover:bg-white/10 transition-colors opacity-50 cursor-not-allowed">
                    Pengaturan (segera)
                </a>
            </nav>
            <div class="p-4 border-t border-white/10">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 rounded-lg hover:bg-red-500/20 transition-colors text-red-300">
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</body>
</html>