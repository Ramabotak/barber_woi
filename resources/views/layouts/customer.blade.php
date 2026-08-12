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
                <a href="{{ route('customer.reviews.index') }}" class="hover:text-brand-gold {{ request()->routeIs('customer.reviews.*') ? 'text-brand-gold font-semibold' : '' }}">Ulasan Saya</a>
                <a href="{{ route('notifications.index') }}" class="relative hover:text-brand-gold {{ request()->routeIs('notifications.*') ? 'text-brand-gold font-semibold' : '' }}" x-data>
                    Notifikasi
                    <span x-show="$store.notif.unreadCount > 0" x-cloak
                          x-text="$store.notif.unreadCount > 9 ? '9+' : $store.notif.unreadCount"
                          class="ml-1 bg-red-500 text-white text-[10px] rounded-full px-1.5 py-0.5 align-top"></span>
                </a>
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

    @include('partials.notification-toast')

    @stack('scripts')
</body>
</html>
