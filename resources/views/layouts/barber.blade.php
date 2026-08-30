<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Barber - Barber Woi')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; line-height: 1; }
    </style>
</head>
<body class="min-h-screen bg-[#f9f9f9] font-['Inter'] antialiased text-[#1a1c1c]" x-data="{ mobileMenu: false }" @keydown.escape.window="mobileMenu = false">
    <div class="flex min-h-screen md:h-screen md:overflow-hidden">
        <aside class="hidden h-screen w-64 shrink-0 flex-col bg-charcoal text-cream/80 md:flex">
            <div class="flex items-center gap-3 border-b border-white/10 px-6 py-6">
                <span class="flex h-12 w-12 flex-shrink-0 items-center justify-center overflow-hidden rounded-full border border-gold/30 bg-gold/10"><img src="{{ asset('images/barber-woi-logo.png') }}" alt="Logo Barber Woi" class="h-full w-full scale-110 object-cover"></span>
                <div><h2 class="font-heading text-base font-bold leading-tight text-white">Barber Woi</h2><p class="text-[11px] uppercase tracking-wider text-cream/50">Staff Portal</p></div>
            </div>
            <div class="px-3 pt-5"><a href="{{ route('barber.booking.incoming') }}" class="flex items-center justify-center gap-2 rounded-lg bg-gold px-4 py-3 text-sm font-bold text-charcoal shadow-sm transition hover:bg-[#dbb45d]"><span class="material-symbols-outlined text-[18px]">add_circle</span>Booking Baru</a></div>
            <nav class="flex-1 space-y-1 px-3 py-5 text-sm">
                <a href="{{ route('barber.dashboard') }}" @class(['flex items-center gap-3 rounded-lg border-l-2 px-3 py-3 font-medium transition-colors', 'border-gold bg-white/10 text-white' => request()->routeIs('barber.dashboard'), 'border-transparent text-cream/60 hover:bg-white/5 hover:text-white' => !request()->routeIs('barber.dashboard')])><span class="material-symbols-outlined">dashboard</span>Dashboard</a>
                <a href="{{ route('barber.booking.incoming') }}" @class(['flex items-center gap-3 rounded-lg border-l-2 px-3 py-3 font-medium transition-colors', 'border-gold bg-white/10 text-white' => request()->routeIs('barber.booking.incoming'), 'border-transparent text-cream/60 hover:bg-white/5 hover:text-white' => !request()->routeIs('barber.booking.incoming')])><span class="material-symbols-outlined">event_available</span>Booking Masuk</a>
                <a href="{{ route('barber.queue') }}" @class(['flex items-center gap-3 rounded-lg border-l-2 px-3 py-3 font-medium transition-colors', 'border-gold bg-white/10 text-white' => request()->routeIs('barber.queue'), 'border-transparent text-cream/60 hover:bg-white/5 hover:text-white' => !request()->routeIs('barber.queue')])><span class="material-symbols-outlined">format_list_numbered</span>Antrean Aktif</a>
                <a href="{{ route('barber.schedule.index') }}" @class(['flex items-center gap-3 rounded-lg border-l-2 px-3 py-3 font-medium transition-colors', 'border-gold bg-white/10 text-white' => request()->routeIs('barber.schedule.*'), 'border-transparent text-cream/60 hover:bg-white/5 hover:text-white' => !request()->routeIs('barber.schedule.*')])><span class="material-symbols-outlined">calendar_month</span>Jadwal Saya</a>
                <a href="{{ route('notifications.index') }}" x-data @class(['relative flex items-center gap-3 rounded-lg border-l-2 px-3 py-3 font-medium transition-colors', 'border-gold bg-white/10 text-white' => request()->routeIs('notifications.*'), 'border-transparent text-cream/60 hover:bg-white/5 hover:text-white' => !request()->routeIs('notifications.*')])><span class="material-symbols-outlined">notifications</span>Notifikasi<span x-show="$store.notif.unreadCount > 0" x-cloak x-text="$store.notif.unreadCount > 9 ? '9+' : $store.notif.unreadCount" class="ml-auto rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] text-white"></span></a>
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="border-t border-white/10 p-3">@csrf<button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-3 text-left text-sm font-medium text-red-300/80 transition hover:bg-red-500/10 hover:text-red-200"><span class="material-symbols-outlined">logout</span>Keluar</button></form>
        </aside>

        <div x-show="mobileMenu" x-cloak @click.self="mobileMenu = false" class="fixed inset-0 z-50 bg-charcoal/45 p-4 md:hidden"><aside x-show="mobileMenu" x-transition class="ml-auto flex h-full w-full max-w-xs flex-col rounded-xl bg-charcoal text-cream shadow-xl" role="dialog" aria-modal="true" aria-label="Menu navigasi"><div class="flex items-center justify-between border-b border-white/10 px-5 py-5"><div class="flex items-center gap-3"><span class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-full border border-gold/30 bg-gold/10"><img src="{{ asset('images/barber-woi-logo.png') }}" alt="Logo Barber Woi" class="h-full w-full scale-110 object-cover"></span><div><h2 class="font-heading font-bold text-white">Barber Woi</h2><p class="text-[11px] uppercase tracking-wider text-cream/50">Staff Portal</p></div></div><button @click="mobileMenu=false" class="rounded-lg p-2 hover:bg-white/10" aria-label="Tutup menu"><span class="material-symbols-outlined">close</span></button></div><nav class="flex-1 space-y-1 p-3 text-sm"><a href="{{ route('barber.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-white/10"><span class="material-symbols-outlined">dashboard</span>Dashboard</a><a href="{{ route('barber.booking.incoming') }}" class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-white/10"><span class="material-symbols-outlined">event_available</span>Booking Masuk</a><a href="{{ route('barber.queue') }}" class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-white/10"><span class="material-symbols-outlined">format_list_numbered</span>Antrean Aktif</a><a href="{{ route('barber.schedule.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-white/10"><span class="material-symbols-outlined">calendar_month</span>Jadwal Saya</a><a href="{{ route('notifications.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-white/10"><span class="material-symbols-outlined">notifications</span>Notifikasi</a></nav><form method="POST" action="{{ route('logout') }}" class="border-t border-white/10 p-3">@csrf<button class="flex w-full items-center gap-3 rounded-lg px-3 py-3 text-left text-sm font-semibold text-red-300 hover:bg-red-500/10"><span class="material-symbols-outlined">logout</span>Keluar</button></form></aside></div>

        <main class="flex min-w-0 flex-1 flex-col overflow-hidden">
            <header class="flex h-16 items-center justify-between border-b border-[#c7c6ca] bg-[#f9f9f9] px-3 sm:px-6"><div class="flex min-w-0 items-center gap-2"><button @click="mobileMenu=true" class="rounded-lg p-2 hover:bg-[#e2e2e2] md:hidden" aria-label="Buka menu"><span class="material-symbols-outlined">menu</span></button><p class="truncate font-['Plus_Jakarta_Sans'] text-sm font-semibold italic text-[#010102]">Executive Grooming Standard</p></div><div class="flex shrink-0 items-center gap-2 sm:gap-3"><a href="{{ route('notifications.index') }}" class="rounded-lg p-2 text-[#46464a] hover:bg-[#e2e2e2] hover:text-[#795902]" aria-label="Notifikasi"><span class="material-symbols-outlined">notifications</span></a><div class="grid h-8 w-8 place-items-center rounded-full border border-[#c7c6ca] bg-[#e2e2e2] text-xs font-bold text-[#46464a]">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div></div></header>
            <section class="flex-1 overflow-y-auto bg-[#f9f9f9] p-4 sm:p-6">@yield('content')</section>
        </main>
    </div>
    @include('partials.notification-toast')
    @stack('scripts')
</body>
</html>
