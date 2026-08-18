<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barber Woi - Masuk</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .glass-panel {
            background: rgba(28, 28, 30, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(201, 162, 75, 0.3);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .bg-overlay {
            background: linear-gradient(to bottom, rgba(28, 28, 30, 0.7), rgba(28, 28, 30, 0.95));
        }
        .custom-input {
            background: rgba(28, 28, 30, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.2s ease-in-out;
        }
        .custom-input:focus {
            border-color: #C9A24B;
            outline: none;
            box-shadow: 0 0 0 1px #C9A24B;
        }
        .custom-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }
        .input-icon {
            color: rgba(255, 255, 255, 0.5);
            transition: color 0.2s;
        }
        .custom-input:focus ~ .input-icon {
            color: #C9A24B;
        }
    </style>
</head>
{{-- Centering dipasang langsung di class body (bukan lewat <style> terpisah) supaya nggak gampang hilang saat integrasi --}}
<body class="antialiased min-h-screen w-full flex items-center justify-center bg-charcoal m-0 p-0 relative overflow-hidden">

    {{-- Background image + overlay --}}
    <div class="fixed inset-0 z-0 w-full h-full">
        <div class="absolute inset-0 w-full h-full bg-cover bg-center"
             style="background-image: url('https://images.unsplash.com/photo-1585747860715-2ba37e788b70?q=80&w=1600&auto=format&fit=crop');"></div>
        <div class="absolute inset-0 bg-overlay"></div>
    </div>

    {{-- Login Card --}}
    <main class="relative z-10 w-full max-w-[480px] px-5">
        <div class="glass-panel rounded-xl p-8 md:p-10 flex flex-col gap-6">

            <div class="flex flex-col gap-2 text-center mb-2">
                <h1 class="font-heading text-3xl md:text-4xl font-bold text-white">Masuk ke Akun</h1>
                <p class="font-body text-cream/80">Masuk untuk memesan jadwal cukur Anda</p>
            </div>

            @if (session('status'))
                <div class="bg-brandsuccess/20 border border-brandsuccess text-white p-3 rounded-lg text-sm text-center">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
                @csrf

                {{-- Email --}}
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-cream/70" for="email">Email</label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined input-icon absolute left-4 z-10">mail</span>
                        <input class="custom-input w-full rounded-lg h-12 pl-[48px] pr-4 text-base"
                               id="email" name="email" type="email" value="{{ old('email') }}"
                               placeholder="contoh@email.com" required autofocus>
                    </div>
                    @error('email')<p class="text-branddanger text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Password --}}
                <div class="flex flex-col gap-1">
                    <div class="flex justify-between items-center w-full">
                        <label class="text-sm font-medium text-cream/70" for="password">Kata Sandi</label>
                        @if (Route::has('password.request'))
                            <a class="text-xs text-gold hover:text-white transition-colors" href="{{ route('password.request') }}">Lupa Kata Sandi?</a>
                        @endif
                    </div>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined input-icon absolute left-4 z-10">lock</span>
                        <input class="custom-input w-full rounded-lg h-12 pl-[48px] pr-[48px] text-base"
                               id="password" name="password" type="password" placeholder="••••••••" required>
                        <button class="absolute right-4 z-10 flex items-center justify-center text-cream/70 hover:text-white transition-colors"
                                id="togglePassword" type="button">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                    </div>
                    @error('password')<p class="text-branddanger text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <label class="flex items-center gap-2 text-sm text-cream/70">
                    <input type="checkbox" name="remember" class="rounded border-white/20 bg-transparent text-gold focus:ring-gold">
                    <span>Ingat saya</span>
                </label>

                <button class="w-full h-12 bg-gold hover:bg-gold/80 text-charcoal font-bold rounded-lg mt-1 transition-colors flex items-center justify-center"
                        type="submit">
                    Masuk
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-4 opacity-60">
                <div class="flex-grow h-px bg-white/20"></div>
                <span class="text-xs text-cream/80">Atau</span>
                <div class="flex-grow h-px bg-white/20"></div>
            </div>

            {{-- Google Login --}}
            <a href="{{ route('google.login') }}"
               class="w-full h-12 bg-white hover:bg-gray-100 text-charcoal font-semibold rounded-lg transition-colors flex items-center justify-center gap-2 border border-transparent">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25C22.56 11.47 22.49 10.72 22.36 10H12V14.26H17.92C17.67 15.63 16.86 16.79 15.69 17.57V20.34H19.26C21.35 18.42 22.56 15.6 22.56 12.25Z" fill="#4285F4"></path>
                    <path d="M12 23C14.97 23 17.46 22.02 19.26 20.34L15.69 17.57C14.71 18.23 13.46 18.63 12 18.63C9.18 18.63 6.79 16.73 5.92 14.16H2.23V17.02C4.03 20.6 7.73 23 12 23Z" fill="#34A853"></path>
                    <path d="M5.92 14.16C5.69 13.49 5.57 12.76 5.57 12C5.57 11.24 5.69 10.51 5.92 9.84V6.98H2.23C1.49 8.46 1.06 10.18 1.06 12C1.06 13.82 1.49 15.54 2.23 17.02L5.92 14.16Z" fill="#FBBC05"></path>
                    <path d="M12 5.38C13.62 5.38 15.07 5.94 16.22 7.03L19.34 3.91C17.45 2.15 14.97 1 12 1C7.73 1 4.03 3.4 2.23 6.98L5.92 9.84C6.79 7.27 9.18 5.38 12 5.38Z" fill="#EA4335"></path>
                </svg>
                <span>Masuk dengan Google</span>
            </a>

            {{-- Footer link --}}
            <div class="text-center mt-1">
                <p class="text-sm text-cream/80">
                    Belum punya akun?
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-gold font-semibold hover:text-white transition-colors">Daftar di sini</a>
                    @endif
                </p>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const iconSpan = togglePassword.querySelector('span');

            togglePassword.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                iconSpan.textContent = type === 'password' ? 'visibility' : 'visibility_off';
            });
        });
    </script>
</body>
</html>