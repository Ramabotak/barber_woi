<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Barber - Barber Woi')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f9f9f9] font-['Inter'] antialiased text-[#1a1c1c]" x-data="{ mobileMenu: false }">
    <div class="flex min-h-screen md:h-screen md:overflow-hidden">
        <aside class="hidden w-64 shrink-0 flex-col border-r border-[#c7c6ca] bg-[#1c1c1e] p-6 text-white md:flex">
            <div class="mb-6 flex items-center gap-2">
                <div class="grid h-10 w-10 place-items-center rounded-full bg-[#eeeeee] text-base text-[#1b1b1d]">✂</div>
                <div><h2 class="font-['Plus_Jakarta_Sans'] text-base font-bold leading-tight">Barber Woi</h2><p class="text-xs text-[#c8c6c8]">Staff Portal</p></div>
            </div>
            <a href="{{ route('barber.booking.incoming') }}" class="mb-4 flex items-center justify-center rounded-lg bg-[#fdd275] py-3 text-sm font-semibold text-[#775800] shadow-sm transition-colors hover:bg-[#ffdf9e]">+ Booking Baru</a>
            <nav class="flex-1 space-y-2 text-sm">
                <a href="{{ route('barber.dashboard') }}" @class(['flex items-center gap-3 rounded-lg px-4 py-3 transition-colors', 'bg-[#795902] font-semibold text-white' => request()->routeIs('barber.dashboard'), 'text-[#c8c6c8] hover:bg-[#e2e2e2] hover:text-[#1b1b1d]' => !request()->routeIs('barber.dashboard')])><span class="w-4 text-center">⊞</span>Dashboard</a>
                <a href="{{ route('barber.booking.incoming') }}" @class(['flex items-center gap-3 rounded-lg px-4 py-3 transition-colors', 'bg-[#795902] font-semibold text-white' => request()->routeIs('barber.booking.incoming'), 'text-[#c8c6c8] hover:bg-[#e2e2e2] hover:text-[#1b1b1d]' => !request()->routeIs('barber.booking.incoming')])><span class="w-4 text-center">▣</span>Booking Masuk</a>
                <a href="{{ route('barber.queue') }}" @class(['flex items-center gap-3 rounded-lg px-4 py-3 transition-colors', 'bg-[#795902] font-semibold text-white' => request()->routeIs('barber.queue'), 'text-[#c8c6c8] hover:bg-[#e2e2e2] hover:text-[#1b1b1d]' => !request()->routeIs('barber.queue')])><span class="w-4 text-center">♧</span>Antrean Aktif</a>
                <a href="{{ route('barber.schedule.index') }}" @class(['flex items-center gap-3 rounded-lg px-4 py-3 transition-colors', 'bg-[#795902] font-semibold text-white' => request()->routeIs('barber.schedule.*'), 'text-[#c8c6c8] hover:bg-[#e2e2e2] hover:text-[#1b1b1d]' => !request()->routeIs('barber.schedule.*')])><span class="w-4 text-center">◷</span>Jadwal Saya</a>
                <a href="{{ route('notifications.index') }}" x-data @class(['relative flex items-center gap-3 rounded-lg px-4 py-3 transition-colors', 'bg-[#795902] font-semibold text-white' => request()->routeIs('notifications.*'), 'text-[#c8c6c8] hover:bg-[#e2e2e2] hover:text-[#1b1b1d]' => !request()->routeIs('notifications.*')])><span class="w-4 text-center">♧</span>Notifikasi<span x-show="$store.notif.unreadCount > 0" x-cloak x-text="$store.notif.unreadCount > 9 ? '9+' : $store.notif.unreadCount" class="ml-auto rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] text-white"></span></a>
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="pt-4">@csrf<button type="submit" class="w-full rounded-lg px-4 py-2 text-left text-sm text-[#c8c6c8] transition-colors hover:bg-red-500/20">← Keluar</button></form>
        </aside>

        <div x-show="mobileMenu" x-cloak @click.self="mobileMenu = false" class="fixed inset-0 z-50 bg-black/45 p-4 md:hidden"><aside x-show="mobileMenu" x-transition class="ml-auto flex h-full w-full max-w-xs flex-col rounded-xl bg-[#1c1c1e] p-5 text-white shadow-xl"><div class="flex items-center justify-between border-b border-white/10 pb-4"><div><h2 class="font-['Plus_Jakarta_Sans'] font-bold">Barber Woi</h2><p class="text-xs text-[#c8c6c8]">Staff Portal</p></div><button @click="mobileMenu=false" class="p-2"><span class="material-symbols-outlined">close</span></button></div><nav class="flex-1 space-y-1 py-4 text-sm"><a href="{{ route('barber.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-white/10">Dashboard</a><a href="{{ route('barber.booking.incoming') }}" class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-white/10">Booking Masuk</a><a href="{{ route('barber.queue') }}" class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-white/10">Antrean Aktif</a><a href="{{ route('barber.schedule.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-white/10">Jadwal Saya</a><a href="{{ route('notifications.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-white/10">Notifikasi</a></nav><form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full rounded-lg bg-red-500/15 px-3 py-3 text-left text-sm text-red-200">Keluar</button></form></aside></div>

        <main class="flex min-w-0 flex-1 flex-col overflow-hidden">
            <header class="flex h-16 items-center justify-between border-b border-[#c7c6ca] bg-[#f9f9f9] px-4 sm:px-6"><div class="flex items-center gap-2"><button @click="mobileMenu=true" class="p-2 md:hidden"><span class="material-symbols-outlined">menu</span></button><p class="font-['Plus_Jakarta_Sans'] text-sm font-semibold italic text-[#010102]">Executive Grooming Standard</p></div><div class="flex items-center gap-3"><a href="{{ route('notifications.index') }}" class="text-[#46464a] hover:text-[#795902]"><span class="material-symbols-outlined">notifications</span></a><div class="grid h-8 w-8 place-items-center rounded-full border border-[#c7c6ca] bg-[#e2e2e2] text-xs font-bold text-[#46464a]">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div></div></header>
            <section class="flex-1 overflow-y-auto bg-[#f9f9f9] p-4 sm:p-6">@yield('content')</section>
        </main>
    </div>
    @include('partials.notification-toast')
    @stack('scripts')
</body>
</html>
