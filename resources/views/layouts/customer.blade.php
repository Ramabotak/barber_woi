<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Barber Woi')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; line-height: 1; }
    </style>
</head>
<body class="min-h-screen bg-cream font-body text-charcoal antialiased" x-data="{ menuOpen: false }">
    <header class="sticky top-0 z-40 border-b border-gray-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-[1200px] items-center justify-between px-4 sm:px-6">
            <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-charcoal text-gold">
                    <x-application-logo class="h-5 w-5 fill-current" />
                </span>
                <span class="font-heading text-base font-bold tracking-tight text-charcoal">Barber Woi</span>
            </a>

            <nav class="hidden h-full items-center gap-7 md:flex">
                <a href="{{ route('customer.dashboard') }}" @class(['inline-flex h-full items-center border-b-2 text-sm font-semibold transition-colors', 'border-gold text-charcoal' => request()->routeIs('customer.dashboard'), 'border-transparent text-muted hover:border-gold/50 hover:text-charcoal' => !request()->routeIs('customer.dashboard')])>Beranda</a>
                <a href="{{ route('customer.booking.index') }}" @class(['inline-flex h-full items-center border-b-2 text-sm font-semibold transition-colors', 'border-gold text-charcoal' => request()->routeIs('customer.booking.*'), 'border-transparent text-muted hover:border-gold/50 hover:text-charcoal' => !request()->routeIs('customer.booking.*')])>Booking Saya</a>
                <a href="{{ route('customer.reviews.index') }}" @class(['inline-flex h-full items-center border-b-2 text-sm font-semibold transition-colors', 'border-gold text-charcoal' => request()->routeIs('customer.reviews.*'), 'border-transparent text-muted hover:border-gold/50 hover:text-charcoal' => !request()->routeIs('customer.reviews.*')])>Ulasan Saya</a>
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ route('notifications.index') }}" class="relative rounded-full p-2 text-muted transition-colors hover:bg-gray-100 hover:text-charcoal" aria-label="Notifikasi" x-data>
                    <span class="material-symbols-outlined text-[21px]">notifications</span>
                    <span x-show="$store.notif.unreadCount > 0" x-cloak x-text="$store.notif.unreadCount > 9 ? '9+' : $store.notif.unreadCount" class="absolute right-0 top-0 min-w-[15px] rounded-full bg-red-500 px-1 py-0.5 text-center text-[9px] font-bold leading-none text-white"></span>
                </a>
                <a href="{{ route('profile.edit') }}" class="flex h-9 w-9 items-center justify-center rounded-full bg-charcoal text-xs font-bold text-gold transition hover:ring-2 hover:ring-gold/50" aria-label="Profil dan pengaturan">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </a>
                <button type="button" @click="menuOpen = !menuOpen" class="rounded-lg p-2 text-muted hover:bg-gray-100 md:hidden" aria-label="Buka menu"><span class="material-symbols-outlined">menu</span></button>
            </div>
        </div>
    </header>

    <div x-show="menuOpen" x-cloak @click.self="menuOpen = false" class="fixed inset-0 z-50 bg-charcoal/40 p-4 md:hidden">
        <div x-show="menuOpen" x-transition class="ml-auto flex h-full w-full max-w-xs flex-col rounded-xl bg-white p-5 shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div><p class="font-heading font-bold text-charcoal">{{ auth()->user()->name }}</p><p class="mt-0.5 text-xs text-muted">Customer Barber Woi</p></div>
                <button type="button" @click="menuOpen = false" class="rounded-lg p-2 text-muted hover:bg-gray-100"><span class="material-symbols-outlined">close</span></button>
            </div>
            <nav class="mt-4 space-y-1 text-sm font-semibold">
                <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-cream"><span class="material-symbols-outlined">home</span>Beranda</a>
                <a href="{{ route('customer.booking.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-cream"><span class="material-symbols-outlined">calendar_month</span>Booking Saya</a>
                <a href="{{ route('customer.reviews.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-cream"><span class="material-symbols-outlined">rate_review</span>Ulasan Saya</a>
                <a href="{{ route('notifications.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-cream"><span class="material-symbols-outlined">notifications</span>Notifikasi</a>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-cream"><span class="material-symbols-outlined">manage_accounts</span>Profil &amp; Pengaturan</a>
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="mt-auto border-t border-gray-100 pt-4">@csrf<button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-branddanger hover:bg-red-50"><span class="material-symbols-outlined">logout</span>Keluar</button></form>
        </div>
    </div>

    <main class="mx-auto w-full max-w-[1200px] px-4 py-7 sm:px-6 sm:py-9">
        @yield('content')
    </main>

    @include('partials.notification-toast')

    @stack('scripts')
</body>
</html>
