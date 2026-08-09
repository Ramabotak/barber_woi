<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Barber Woi')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">
    <nav class="bg-brand-navy text-white">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ route('customer.dashboard') }}" class="font-bold text-lg">Barber Woi</a>
            <div class="flex items-center gap-4 text-sm">
                <a href="{{ route('customer.dashboard') }}" class="hover:text-brand-gold {{ request()->routeIs('customer.dashboard') ? 'text-brand-gold font-semibold' : '' }}">Beranda</a>
                <a href="{{ route('customer.booking.index') }}" class="hover:text-brand-gold {{ request()->routeIs('customer.booking.*') ? 'text-brand-gold font-semibold' : '' }}">Booking Saya</a>
                <a href="{{ route('notifications.index') }}" class="hover:text-brand-gold {{ request()->routeIs('notifications.*') ? 'text-brand-gold font-semibold' : '' }}">Notifikasi</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="hover:text-red-300">Keluar</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
