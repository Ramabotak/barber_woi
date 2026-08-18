<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barber Woi - Buat Akun Baru</title>
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
    </style>
</head>
<body class="antialiased min-h-screen w-full flex items-center justify-center bg-charcoal m-0 p-0 relative overflow-hidden">

    {{-- Background image + overlay --}}
    <div class="fixed inset-0 z-0 w-full h-full">
        <div class="absolute inset-0 w-full h-full bg-cover bg-center"
             style="background-image: url('https://images.unsplash.com/photo-1585747860715-2ba37e788b70?q=80&w=1600&auto=format&fit=crop');"></div>
        <div class="absolute inset-0 bg-overlay"></div>
    </div>

    {{-- Register Card --}}
    <main class="relative z-10 w-full max-w-[480px] px-5 py-8">
        <div class="glass-panel rounded-xl p-8 md:p-10 flex flex-col gap-6">

            <div class="flex flex-col gap-2 text-center mb-2">
                <h1 class="font-heading text-3xl md:text-4xl font-bold text-white">Buat Akun Baru</h1>
                <p class="font-body text-cream/80">Daftar untuk mulai booking jadwal cukur</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
                @csrf

                {{-- Nama Lengkap --}}
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-cream/70" for="name">Nama Lengkap</label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined input-icon absolute left-4 z-10">person</span>
                        <input class="custom-input w-full rounded-lg h-12 pl-[48px] pr-4 text-base"
                               id="name" name="name" type="text" value="{{ old('name') }}"
                               placeholder="Masukkan nama lengkap" required autofocus>
                    </div>
                    @error('name')<p class="text-branddanger text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Email --}}
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-cream/70" for="email">Email</label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined input-icon absolute left-4 z-10">mail</span>
                        <input class="custom-input w-full rounded-lg h-12 pl-[48px] pr-4 text-base"
                               id="email" name="email" type="email" value="{{ old('email') }}"
                               placeholder="contoh@email.com" required>
                    </div>
                    @error('email')<p class="text-branddanger text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Nomor Handphone --}}
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-cream/70" for="phone">Nomor Handphone</label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined input-icon absolute left-4 z-10">phone</span>
                        <input class="custom-input w-full rounded-lg h-12 pl-[48px] pr-4 text-base"
                               id="phone" name="phone" type="tel" value="{{ old('phone') }}"
                               placeholder="08xxxxxxxxxx">
                    </div>
                    @error('phone')<p class="text-branddanger text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Password --}}
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-cream/70" for="password">Kata Sandi</label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined input-icon absolute left-4 z-10">lock</span>
                        <input class="custom-input w-full rounded-lg h-12 pl-[48px] pr-[48px] text-base"
                               id="password" name="password" type="password" placeholder="Buat kata sandi" required>
                        <button type="button" class="toggle-password absolute right-4 z-10 flex items-center justify-center text-cream/70 hover:text-white transition-colors" data-target="password">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                    </div>
                    @error('password')<p class="text-branddanger text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-cream/70" for="password_confirmation">Konfirmasi Kata Sandi</label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined input-icon absolute left-4 z-10">lock</span>
                        <input class="custom-input w-full rounded-lg h-12 pl-[48px] pr-[48px] text-base"
                               id="password_confirmation" name="password_confirmation" type="password"
                               placeholder="Ulangi kata sandi" required>
                        <button type="button" class="toggle-password absolute right-4 z-10 flex items-center justify-center text-cream/70 hover:text-white transition-colors" data-target="password_confirmation">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                    </div>
                </div>

                <button class="w-full h-12 bg-gold hover:bg-gold/80 text-charcoal font-bold rounded-lg mt-2 transition-colors flex items-center justify-center"
                        type="submit">
                    Daftar
                </button>
            </form>

            <div class="text-center text-sm text-cream/80">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-gold font-semibold hover:text-white transition-colors ml-1">Masuk di sini</a>
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