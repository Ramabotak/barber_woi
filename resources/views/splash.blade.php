<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barber Woi - Selamat Datang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .glass-panel {
            background: rgba(28, 28, 30, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(201, 162, 75, 0.15);
        }
        .text-shadow-gold {
            text-shadow: 0 4px 24px rgba(201, 162, 75, 0.4);
        }
        .bg-overlay {
            background: linear-gradient(180deg, rgba(28, 28, 30, 0.3) 0%, rgba(28, 28, 30, 0.8) 50%, rgba(28, 28, 30, 1) 100%);
        }
    </style>
</head>
<body class="bg-charcoal m-0 p-0 min-h-screen flex items-center justify-center font-body text-white antialiased overflow-hidden">

    {{-- Background image + overlay --}}
    <div class="fixed inset-0 z-0 w-full h-full bg-cover bg-center"
         style="background-image: url('https://images.unsplash.com/photo-1585747860715-2ba37e788b70?q=80&w=1600&auto=format&fit=crop');">
    </div>
    <div class="fixed inset-0 z-10 w-full h-full bg-overlay"></div>

    {{-- Main content --}}
    <main class="relative z-20 w-full max-w-[420px] min-h-screen md:min-h-[800px] flex flex-col justify-between px-5 py-10 md:py-12 mx-auto md:glass-panel md:rounded-xl md:shadow-2xl">

        <div class="flex-1"></div>

        {{-- Logo & tagline --}}
        <div class="flex-1 flex flex-col items-center justify-center space-y-4" id="logo-container">
            <div class="mb-2 h-40 w-40 overflow-hidden rounded-full drop-shadow-[0_4px_24px_rgba(201,162,75,0.45)] md:h-48 md:w-48"><img src="{{ asset('images/barber-woi-logo.png') }}" alt="Logo Barber Woi" class="h-full w-full scale-110 object-cover"></div>
            <h1 class="font-heading text-4xl md:text-6xl font-bold text-gold text-shadow-gold tracking-tight uppercase">
                Barber WOI
            </h1>
            <p class="font-body text-cream/80 text-center max-w-[280px]">
                Premium grooming experience, kini bisa dibooking dari mana saja.
            </p>
        </div>

        {{-- Aksi login --}}
        <div class="flex-1 flex flex-col justify-end w-full space-y-3 pb-8 md:pb-0" id="action-container">

            {{-- Masuk dengan Google --}}
            <a href="{{ route('google.login') }}"
               class="w-full h-14 bg-white text-charcoal rounded-lg flex items-center justify-center space-x-3 px-6 hover:bg-gray-50 active:scale-95 transition-all duration-200 shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
                </svg>
                <span class="font-medium text-sm">Masuk dengan Google</span>
            </a>

            {{-- Masuk dengan Email --}}
            <a href="{{ route('login') }}"
               class="w-full h-14 bg-transparent text-gold rounded-lg flex items-center justify-center space-x-3 px-6 hover:bg-white/5 active:scale-95 transition-all duration-200 border-2 border-gold">
                <span class="material-symbols-outlined text-[20px]">mail</span>
                <span class="font-medium text-sm">Masuk dengan Email</span>
            </a>

            <div class="mt-6 text-center">
                <p class="text-xs text-cream/60">
                    Dengan masuk, Anda menyetujui
                    <a href="{{ Route::has('terms') ? route('terms') : '#' }}" class="text-gold hover:underline">Syarat &amp; Ketentuan</a> kami.
                </p>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const logo = document.getElementById('logo-container');
            const actions = document.getElementById('action-container');

            logo.style.opacity = '0';
            logo.style.transform = 'translateY(20px)';
            actions.style.opacity = '0';
            actions.style.transform = 'translateY(20px)';
            logo.style.transition = 'opacity 0.7s, transform 0.7s';
            actions.style.transition = 'opacity 0.7s, transform 0.7s';

            setTimeout(() => {
                logo.style.opacity = '1';
                logo.style.transform = 'translateY(0)';
            }, 100);

            setTimeout(() => {
                actions.style.opacity = '1';
                actions.style.transform = 'translateY(0)';
            }, 400);
        });
    </script>
</body>
</html>
