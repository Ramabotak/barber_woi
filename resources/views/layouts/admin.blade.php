<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Barber Woi')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            font-size: 20px;
            line-height: 1;
        }
    </style>
</head>
<body class="bg-gray-50 font-body text-charcoal antialiased h-screen overflow-hidden flex">

    @php
        $navItems = [
            ['route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['route' => 'admin.barbers.index', 'pattern' => 'admin.barbers.*', 'icon' => 'content_cut', 'label' => 'Kelola Barber'],
            ['route' => 'admin.services.index', 'pattern' => 'admin.services.*', 'icon' => 'dry_cleaning', 'label' => 'Kelola Layanan'],
            ['route' => 'admin.bookings.index', 'pattern' => 'admin.bookings.*', 'icon' => 'event_available', 'label' => 'Kelola Booking'],
            ['route' => 'admin.reviews.index', 'pattern' => 'admin.reviews.*', 'icon' => 'reviews', 'label' => 'Ulasan Customer'],
            ['route' => 'admin.reports.index', 'pattern' => 'admin.reports.*', 'icon' => 'analytics', 'label' => 'Laporan'],
            ['route' => 'admin.settings.edit', 'pattern' => 'admin.settings.*', 'icon' => 'settings', 'label' => 'Pengaturan'],
        ];
    @endphp

    {{-- Sidebar --}}
    <aside class="hidden md:flex md:flex-col w-64 bg-charcoal text-cream/80 h-screen flex-shrink-0">
        <div class="px-6 py-6 flex items-center gap-3 border-b border-white/10">
            <span class="w-10 h-10 rounded-full bg-gold/10 border border-gold/30 flex items-center justify-center flex-shrink-0">
                <x-application-logo class="w-5 h-5 fill-gold" />
            </span>
            <div>
                <h1 class="font-heading text-base font-bold text-white leading-tight">Barber Woi</h1>
                <p class="text-[11px] uppercase tracking-wider text-cream/50">Panel Admin</p>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            @foreach ($navItems as $item)
                @php $active = request()->routeIs($item['pattern']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150
                          {{ $active ? 'bg-white/10 text-white border-l-2 border-gold' : 'text-cream/60 hover:text-white hover:bg-white/5 border-l-2 border-transparent' }}">
                    <span class="material-symbols-outlined {{ $active ? 'fill' : '' }}">{{ $item['icon'] }}</span>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="p-3 border-t border-white/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-red-300/80 hover:text-red-200 hover:bg-red-500/10 transition-colors">
                    <span class="material-symbols-outlined">logout</span>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content Wrapper --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top App Bar --}}
        <header class="h-16 w-full flex-shrink-0 bg-white border-b border-gray-200 flex items-center justify-between px-6">
            <p class="font-heading text-sm font-semibold text-charcoal">@yield('title', 'Admin Barber Woi')</p>

            <div class="flex items-center gap-2">
                <a href="{{ route('notifications.index') }}"
                   class="relative text-gray-500 hover:text-charcoal hover:bg-gray-100 rounded-full p-2 transition-colors"
                   x-data>
                    <span class="material-symbols-outlined">notifications</span>
                    <span x-show="$store.notif.unreadCount > 0" x-cloak
                          x-text="$store.notif.unreadCount > 9 ? '9+' : $store.notif.unreadCount"
                          class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] leading-none rounded-full px-1.5 py-1 min-w-[16px] text-center"></span>
                </a>

                <div class="w-8 h-8 rounded-full bg-charcoal text-gold flex items-center justify-center text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
            </div>
        </header>

        {{-- Main Canvas --}}
        <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
            <div class="max-w-[1200px] mx-auto space-y-6">
                @yield('content')
            </div>
        </main>
    </div>

    @include('partials.notification-toast')

    @stack('scripts')
</body>
</html>