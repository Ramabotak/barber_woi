<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Barber - Barber Woi')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <aside class="hidden md:flex md:flex-col w-64 bg-brand-navy text-white">
            <div class="p-6 border-b border-white/10">
                <h2 class="text-xl font-bold">Barber Woi</h2>
                <p class="text-sm text-white/60">Panel Barber</p>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="{{ route('barber.dashboard') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('barber.dashboard') ? 'bg-white/10 font-semibold' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('barber.booking.incoming') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('barber.booking.incoming') ? 'bg-white/10 font-semibold' : '' }}">
                    Booking Masuk
                    @php $barberId = auth()->user()->barber?->id; @endphp
                    @if($barberId)
                        @php $incomingCount = \App\Models\Booking::where('barber_id', $barberId)->where('status', 'pending')->count(); @endphp
                        @if($incomingCount > 0)
                            <span class="ml-1 bg-brand-gold text-brand-navy text-xs font-bold px-2 py-0.5 rounded-full">{{ $incomingCount }}</span>
                        @endif
                    @endif
                </a>
                <a href="{{ route('barber.queue') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('barber.queue') ? 'bg-white/10 font-semibold' : '' }}">
                    Antrean Aktif
                </a>
                <a href="{{ route('barber.schedule.index') }}"
                   class="block px-4 py-2 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('barber.schedule.*') ? 'bg-white/10 font-semibold' : '' }}">
                    Jadwal Saya
                </a>
                <a href="{{ route('notifications.index') }}"
                   class="relative block px-4 py-2 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('notifications.*') ? 'bg-white/10 font-semibold' : '' }}" x-data>
                    Notifikasi
                    <span x-show="$store.notif.unreadCount > 0" x-cloak
                          x-text="$store.notif.unreadCount > 9 ? '9+' : $store.notif.unreadCount"
                          class="ml-1 bg-red-500 text-white text-[10px] rounded-full px-1.5 py-0.5 align-top"></span>
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

    @include('partials.notification-toast')

    @stack('scripts')
</body>
</html>
