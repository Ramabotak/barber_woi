@props([
    'title' => 'Barber Woi',
    'eyebrow' => 'Member access',
    'heading' => 'Selamat datang.',
    'description' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · Barber Woi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;0,700;1,600&family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>.material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; line-height: 1; }</style>
</head>
<body class="surface-grain min-h-screen font-body antialiased">
    <div class="mx-auto grid min-h-screen max-w-[1600px] lg:grid-cols-[minmax(0,0.94fr)_minmax(0,1.06fr)]">
        <aside class="relative hidden overflow-hidden bg-charcoal px-10 py-10 text-white lg:flex lg:flex-col xl:px-16 xl:py-14">
            <img src="{{ asset('images/hero-barber-shop.jpg') }}" alt="Interior Barber Woi" class="absolute inset-0 h-full w-full object-cover opacity-70">
            <div class="absolute inset-0 bg-gradient-to-br from-[#10100f]/80 via-[#171716]/50 to-[#171716]/90"></div>
            <div class="absolute -bottom-24 -right-16 h-80 w-80 rounded-full border border-gold/35"></div>
            <div class="relative flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center overflow-hidden rounded-full border border-gold/40 bg-black/20"><x-application-logo class="h-full w-full scale-110" /></span>
                <div><p class="font-heading text-sm font-bold tracking-tight">Barber Woi</p><p class="mt-0.5 text-[10px] font-bold uppercase tracking-[0.2em] text-gold">Est. 1980</p></div>
            </div>
            <div class="relative my-auto max-w-md pb-12 pt-24">
                <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-gold">A proper appointment</p>
                <h1 class="mt-5 font-['Cormorant_Garamond'] text-6xl font-semibold leading-[0.9] tracking-[-0.045em] text-white xl:text-7xl">Gaya yang terasa tepat, sejak awal.</h1>
                <div class="editorial-rule mt-8 w-24"></div>
                <p class="mt-6 max-w-sm text-sm leading-7 text-cream/75">Pilih waktu, datang tanpa ragu, dan biarkan detail kecil mengurus sisanya.</p>
            </div>
            <p class="relative text-[10px] font-medium uppercase tracking-[0.18em] text-white/45">Booking online · antrean yang jelas</p>
        </aside>

        <main class="flex min-h-screen flex-col bg-[#fcfaf6] px-5 py-5 sm:px-8 sm:py-8 lg:px-12 xl:px-20 xl:py-12">
            <div class="flex items-center justify-between">
                <a href="{{ route('splash') }}" class="inline-flex items-center gap-2 rounded-lg py-2 text-xs font-bold text-muted transition hover:text-charcoal">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>Kembali
                </a>
                <a href="{{ route('splash') }}" class="flex items-center gap-2 lg:hidden">
                    <span class="grid h-9 w-9 place-items-center overflow-hidden rounded-full bg-charcoal"><x-application-logo class="h-full w-full scale-110" /></span>
                    <span class="font-heading text-sm font-bold tracking-tight">Barber Woi</span>
                </a>
            </div>

            <div class="mx-auto flex w-full max-w-[430px] flex-1 flex-col justify-center py-10">
                <p class="text-[10px] font-extrabold uppercase tracking-[0.2em] text-brandwarning">{{ $eyebrow }}</p>
                <h1 class="mt-3 font-heading text-3xl font-extrabold tracking-[-0.04em] text-charcoal sm:text-4xl">{{ $heading }}</h1>
                @if($description)<p class="mt-3 text-sm leading-6 text-muted">{{ $description }}</p>@endif
                <div class="mt-8">{{ $slot }}</div>
            </div>

            <p class="text-center text-[11px] leading-5 text-muted">Dengan melanjutkan, Anda menyetujui penggunaan akun untuk pengelolaan booking Barber Woi.</p>
        </main>
    </div>
</body>
</html>
