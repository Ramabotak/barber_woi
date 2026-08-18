<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barber Woi - Reset Kata Sandi</title>
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

    {{-- Top back button --}}
    <header class="fixed top-0 left-0 z-20 w-full flex items-center px-5 py-4">
        <a href="{{ route('login') }}" class="text-gold hover:opacity-80 transition-opacity p-2 -ml-2 rounded-full">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
    </header>

    {{-- Card --}}
    <main class="relative z-10 w-full px-5 flex justify-center">
        <div class="glass-panel w-full max-w-[420px] rounded-xl p-8 md:p-10 flex flex-col items-center text-center">

            <div class="w-16 h-16 rounded-full bg-gold/10 flex items-center justify-center mb-6">
                <span class="material-symbols-outlined text-4xl text-gold">lock_reset</span>
            </div>

            <h1 class="font-heading text-3xl font-bold text-white mb-2">Buat Kata Sandi Baru</h1>
            <p class="font-body text-cream/80 mb-8">
                Masukkan kata sandi baru untuk akun {{ $request->email ?? '' }}
            </p>

            <form method="POST" action="{{ route('password.store') }}" class="w-full flex flex-col gap-4">
                @csrf

                {{-- Token & email dikirim otomatis dari link reset di email, tidak perlu diisi user --}}
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                {{-- Email tetap ditampilkan (readonly) supaya user tahu akun mana yang direset --}}
                <div class="flex flex-col text-left gap-1">
                    <label class="text-sm font-medium text-cream/70" for="email">Email</label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-4 text-white/50">mail</span>
                        <input class="input-dark w-full rounded-lg py-3 pl-[52px] pr-4 text-base"
                               id="email" name="email" type="email"
                               value="{{ old('email', $request->email) }}"
                               placeholder="Email" required>
                    </div>
                    @error('email')<p class="text-branddanger text-sm mt-1 text-left">{{ $message }}</p>@enderror
                </div>

                {{-- Password Baru --}}
                <div class="flex flex-col text-left gap-1">
                    <label class="text-sm font-medium text-cream/70" for="password">Kata Sandi Baru</label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-4 text-white/50">lock</span>
                        <input class="input-dark w-full rounded-lg py-3 pl-[52px] pr-[48px] text-base"
                               id="password" name="password" type="password"
                               placeholder="Buat kata sandi baru" required>
                        <button type="button" class="toggle-password absolute right-4 flex items-center justify-center text-white/50 hover:text-white transition-colors" data-target="password">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                    @error('password')<p class="text-branddanger text-sm mt-1 text-left">{{ $message }}</p>@enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="flex flex-col text-left gap-1">
                    <label class="text-sm font-medium text-cream/70" for="password_confirmation">Konfirmasi Kata Sandi</label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-4 text-white/50">lock</span>
                        <input class="input-dark w-full rounded-lg py-3 pl-[52px] pr-[48px] text-base"
                               id="password_confirmation" name="password_confirmation" type="password"
                               placeholder="Ulangi kata sandi baru" required>
                        <button type="button" class="toggle-password absolute right-4 flex items-center justify-center text-white/50 hover:text-white transition-colors" data-target="password_confirmation">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                </div>

                <button class="w-full bg-gold hover:bg-gold/90 text-charcoal font-bold py-3 rounded-lg transition-colors mt-2"
                        type="submit">
                    Reset Kata Sandi
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.toggle-password').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const input = document.getElementById(btn.dataset.target);
                    const icon = btn.querySelector('span');
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    icon.textContent = type === 'password' ? 'visibility' : 'visibility_off';
                });
            });
        });
    </script>
</body>
</html>