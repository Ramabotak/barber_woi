<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barber Woi - Lupa Kata Sandi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .glass-panel {
            background-color: rgba(28, 28, 30, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid #C9A24B;
        }
        .bg-overlay {
            background: linear-gradient(to bottom, rgba(28, 28, 30, 0.8), rgba(28, 28, 30, 0.95));
        }
        .input-dark {
            background: rgba(28, 28, 30, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: white;
            transition: all 0.2s ease-in-out;
        }
        .input-dark:focus {
            outline: none;
            border-color: #C9A24B;
            box-shadow: 0 0 0 1px #C9A24B;
        }
        .input-dark::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="antialiased min-h-screen w-full flex items-center justify-center bg-charcoal m-0 p-0 relative overflow-hidden">

    {{-- Background image + overlay --}}
    <div class="fixed inset-0 z-0 w-full h-full">
        <div class="absolute inset-0 w-full h-full bg-cover bg-center"
             style="background-image: url('https://images.unsplash.com/photo-1585747860715-2ba37e788b70?q=80&w=1600&auto=format&fit=crop');"></div>
        <div class="absolute inset-0 bg-overlay"></div>
    </div>

    {{-- Card --}}
    <main class="relative z-10 w-full px-5 flex justify-center">
        <div class="glass-panel w-full max-w-[420px] rounded-lg p-8 md:p-10 flex flex-col items-center text-center">

            <div class="mb-6 text-gold">
                <span class="material-symbols-outlined text-[64px]">lock</span>
            </div>

            <h1 class="font-heading text-3xl font-bold text-white mb-2">Lupa Kata Sandi?</h1>
            <p class="font-body text-cream/80 mb-8">
                Masukkan email Anda, kami akan kirimkan link untuk reset password
            </p>

            @if (session('status'))
                <div class="w-full bg-brandsuccess/20 border border-brandsuccess text-white p-3 rounded-lg text-sm mb-4">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="w-full flex flex-col gap-6">
                @csrf

                <div class="flex flex-col text-left gap-1">
                    <label class="sr-only" for="email">Email</label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-4 text-white/50">mail</span>
                        <input class="input-dark w-full rounded-lg py-3 pl-[52px] pr-4 text-base"
                               id="email" name="email" type="email" value="{{ old('email') }}"
                               placeholder="Email" required autofocus>
                    </div>
                    @error('email')<p class="text-branddanger text-sm mt-1 text-left">{{ $message }}</p>@enderror
                </div>

                <button class="w-full bg-gold hover:bg-gold/90 text-charcoal font-bold py-3 rounded-lg transition-colors mt-1"
                        type="submit">
                    Kirim Link Reset
                </button>
            </form>

            <div class="mt-8">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-1 text-gold hover:text-gold/80 text-sm font-medium transition-colors">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Kembali ke halaman Masuk
                </a>
            </div>
        </div>
    </main>
</body>
</html>