<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#171716">
    <title>Barber Woi · Booking yang tepat waktu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;0,700;1,600&family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>.material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; line-height: 1; }</style>
</head>
<body class="min-h-screen bg-charcoal font-body text-white antialiased">
    <main class="relative isolate flex min-h-screen overflow-hidden">
        <img src="{{ asset('images/hero-barber-shop.jpg') }}" alt="Kursi dan interior Barber Woi" class="absolute inset-0 -z-20 h-full w-full object-cover object-center">
        <div class="absolute inset-0 -z-10 bg-[linear-gradient(90deg,rgba(15,15,14,.94)_0%,rgba(15,15,14,.74)_42%,rgba(15,15,14,.3)_100%)]"></div>
        <div class="absolute inset-0 -z-10 bg-[linear-gradient(0deg,rgba(15,15,14,.68)_0%,transparent_45%)]"></div>
        <div class="absolute -right-32 top-24 -z-10 h-80 w-80 rounded-full border border-gold/25"></div>

        <div class="mx-auto flex w-full max-w-[1360px] flex-col px-5 py-5 sm:px-8 sm:py-8 lg:px-12 lg:py-10">
            <header class="flex items-center justify-between">
                <a href="{{ route('splash') }}" class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center overflow-hidden rounded-full border border-gold/40 bg-black/20 shadow-lg"><x-application-logo class="h-full w-full scale-110" /></span>
                    <span><span class="block font-heading text-sm font-bold tracking-tight">Barber Woi</span><span class="mt-0.5 block text-[9px] font-bold uppercase tracking-[0.22em] text-gold">Est. 1980</span></span>
                </a>
                <a href="{{ route('login') }}" class="hidden items-center gap-2 rounded-full border border-white/20 bg-black/10 px-4 py-2 text-xs font-bold text-white transition hover:border-gold/70 hover:bg-white/10 sm:inline-flex">Sudah punya akun?<span class="text-gold">Masuk</span></a>
            </header>

            <section class="flex flex-1 flex-col justify-end pb-8 pt-16 sm:pb-14 lg:pb-8 lg:pt-24">
                <div class="grid items-end gap-10 lg:grid-cols-[minmax(0,1fr)_380px] lg:gap-16">
                    <div class="max-w-2xl">
                        <p class="text-[10px] font-extrabold uppercase tracking-[0.28em] text-gold">Bukan sekadar potong rambut</p>
                        <h1 class="mt-5 max-w-xl font-['Cormorant_Garamond'] text-5xl font-semibold leading-[0.88] tracking-[-0.055em] text-white sm:text-7xl lg:text-8xl">Tampil rapi, <em class="font-normal text-gold">tanpa</em> menunggu.</h1>
                        <p class="mt-6 max-w-md text-sm leading-7 text-cream/75 sm:text-base">Tentukan barber dan waktu Anda. Kami menjaga antrean tetap jelas, sehingga Anda cukup datang saat giliran tiba.</p>
                        <div class="mt-8 flex items-center gap-4 text-[10px] font-bold uppercase tracking-[0.15em] text-white/55"><span class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-gold"></span>Pilih waktu</span><span class="h-px w-6 bg-white/25"></span><span>Konfirmasi</span><span class="h-px w-6 bg-white/25"></span><span>Datang siap</span></div>
                    </div>

                    <div class="border border-white/15 bg-[#181816]/85 p-5 shadow-[0_20px_60px_rgba(0,0,0,.28)] backdrop-blur-md sm:p-6">
                        <p class="text-[10px] font-extrabold uppercase tracking-[0.2em] text-gold">Mulai booking</p>
                        <p class="mt-2 font-heading text-xl font-bold tracking-tight">Ambil slot Anda hari ini.</p>
                        <p class="mt-2 text-xs leading-5 text-cream/65">Masuk untuk melihat barber yang tersedia dan memilih layanan.</p>
                        <div class="mt-6 grid gap-2.5">
                            <a href="{{ route('login') }}" class="inline-flex min-h-12 items-center justify-center gap-2 bg-gold px-5 text-sm font-extrabold text-charcoal transition hover:bg-[#dbb45d]">Masuk dengan email<span class="material-symbols-outlined text-[18px]">arrow_forward</span></a>
                            <a href="{{ route('google.login') }}" class="inline-flex min-h-12 items-center justify-center gap-3 border border-white/20 bg-white/5 px-5 text-sm font-bold text-white transition hover:bg-white/10"><svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09A7.1 7.1 0 0 1 5.49 12c0-.73.13-1.43.35-2.09V7.07H2.18A10.98 10.98 0 0 0 1 12c0 1.78.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>Lanjutkan dengan Google</a>
                        </div>
                        <p class="mt-5 text-center text-[11px] text-cream/50">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-gold hover:text-white">Daftar sekarang</a></p>
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
