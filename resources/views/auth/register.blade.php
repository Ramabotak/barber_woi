<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barber Woi - Daftar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .form-input:focus {
            border-color: #1a365d;
            border-width: 2px;
            outline: none;
            box-shadow: none;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94) both;
        }
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-background text-on-surface antialiased min-h-screen w-full flex items-center justify-center p-margin-mobile md:p-0 relative">

    {{-- Background Image (desktop) --}}
    <div class="absolute inset-0 z-0 hidden md:block">
        <div class="w-full h-full bg-cover bg-center" 
             style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuD3SHX-2H-bkI0PEWKbjMqcos_iD60dmKn1Wly7_ayExKWlAgu8yVA9jRCuufo7T26_bAa4fzxkmPlSzr_ep4r0X6DZ4jHsG09iltinONcfMFjbS8ogY_BziJjS8xAJKuqYr3tGz9vo8ehoy8tmTVWFWlS9StktJGXgxYmXEp2IT0OcGT4EmIP66Yn88Ei1GJJQcpNlnkJvYDr3K4hoKmL16OxbgL4ajOTBOdS4Y7IeGbMoCVQis9Mf')">
        </div>
        <div class="absolute inset-0 bg-primary/70 backdrop-blur-sm mix-blend-multiply"></div>
    </div>

    {{-- Card Register --}}
    <main class="relative z-10 w-full max-w-md md:max-w-[480px]">
        {{-- Mobile Card --}}
        <div class="bg-surface-container-lowest rounded-xl p-stack-lg shadow-sm border border-outline-variant/30 flex flex-col gap-stack-lg md:hidden animate-fade-in-up">
            <div class="flex justify-center mb-4">
                <img alt="Barber Woi Logo" class="w-32 h-32 object-contain rounded-full shadow-sm" 
                     src="https://lh3.googleusercontent.com/aida-public/AB6AXuD7Nura0KkuJucNw0OuenfKq8H28FXi1sBkts348ya91kKKUSJBsOCkRz63YmLU6yo4DZBg5PmyfNsmsReXneO_ukN5PAO3TT46ugpzPBOTHQxVNa9_wxHcXr-hGY7UzADg4iSa11W5pcNtiTefLCwSe2fq88d3slHtiQKW07dc3pOiDv46-fsA0QhjVwW51e8HooTnqw4IDGIezl5O50s2KvAiYk246Xpzw4G1RuKNlvsrOTLGHJkU">
            </div>
            <h1 class="font-headline-lg-mobile text-headline-lg-mobile text-primary text-center">Daftar Akun Baru</h1>
            <p class="font-body-sm text-body-sm text-on-surface-variant text-center -mt-stack-sm mb-stack-md">Lengkapi data diri Anda untuk mulai memesan.</p>

            <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-stack-md">
                @csrf
                @if (session('status'))
                    <div class="bg-green-50 text-green-700 p-3 rounded-lg text-sm">{{ session('status') }}</div>
                @endif

                {{-- Nama Lengkap --}}
                <div class="flex flex-col gap-1">
                    <label class="font-label-md text-label-md text-on-surface-variant" for="name-mobile">Nama Lengkap</label>
                    <input type="text" name="name" id="name-mobile" value="{{ old('name') }}" required autofocus
                           class="w-full border border-outline-variant rounded-lg px-4 py-3 font-body-md text-body-md bg-surface-bright focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-on-surface"
                           placeholder="Masukkan nama lengkap">
                    @error('name')<p class="text-error text-body-sm">{{ $message }}</p>@enderror
                </div>

                {{-- Email --}}
                <div class="flex flex-col gap-1">
                    <label class="font-label-md text-label-md text-on-surface-variant" for="email-mobile">Email</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">mail</span>
                        <input type="email" name="email" id="email-mobile" value="{{ old('email') }}" required
                               class="w-full border border-outline-variant rounded-lg px-4 py-3 pl-10 font-body-md text-body-md bg-surface-bright focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-on-surface"
                               placeholder="Masukkan email">
                    </div>
                    @error('email')<p class="text-error text-body-sm">{{ $message }}</p>@enderror
                </div>

                {{-- Nomor Telepon --}}
                <div class="flex flex-col gap-1">
                    <label class="font-label-md text-label-md text-on-surface-variant" for="phone-mobile">Nomor Telepon</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">call</span>
                        <input type="tel" name="phone" id="phone-mobile" value="{{ old('phone') }}" required
                               class="w-full border border-outline-variant rounded-lg px-4 py-3 pl-10 font-body-md text-body-md bg-surface-bright focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-on-surface"
                               placeholder="Masukkan nomor telepon">
                    </div>
                    @error('phone')<p class="text-error text-body-sm">{{ $message }}</p>@enderror
                </div>

                {{-- Kata Sandi --}}
                <div class="flex flex-col gap-1">
                    <label class="font-label-md text-label-md text-on-surface-variant" for="password-mobile">Kata Sandi</label>
                    <div class="relative">
                        <input type="password" name="password" id="password-mobile" required
                               class="w-full border border-outline-variant rounded-lg px-4 py-3 pr-10 font-body-md text-body-md bg-surface-bright focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-on-surface"
                               placeholder="Masukkan kata sandi (min. 8 karakter)">
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors"
                                onclick="const input = document.getElementById('password-mobile'); const icon = this.querySelector('span'); if(input.type === 'password'){ input.type = 'text'; icon.textContent = 'visibility_off'; } else { input.type = 'password'; icon.textContent = 'visibility'; }">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                    </div>
                    @error('password')<p class="text-error text-body-sm">{{ $message }}</p>@enderror
                </div>

                {{-- Konfirmasi Kata Sandi --}}
                <div class="flex flex-col gap-1">
                    <label class="font-label-md text-label-md text-on-surface-variant" for="password_confirmation-mobile">Konfirmasi Kata Sandi</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation-mobile" required
                               class="w-full border border-outline-variant rounded-lg px-4 py-3 pr-10 font-body-md text-body-md bg-surface-bright focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-on-surface"
                               placeholder="Ulangi kata sandi">
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors"
                                onclick="const input = document.getElementById('password_confirmation-mobile'); const icon = this.querySelector('span'); if(input.type === 'password'){ input.type = 'text'; icon.textContent = 'visibility_off'; } else { input.type = 'password'; icon.textContent = 'visibility'; }">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                    </div>
                    @error('password_confirmation')<p class="text-error text-body-sm">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="w-full bg-secondary-container text-on-secondary-container font-headline-sm text-headline-sm py-3 rounded-lg shadow-sm hover:opacity-90 transition-opacity flex items-center justify-center gap-2 mt-stack-md">
                    Daftar
                </button>
                
                 {{-- Divider "Atau" --}}
                <div class="flex items-center gap-base my-stack-md">
                    <div class="h-[1px] flex-1 bg-outline-variant"></div>
                    <span class="text-label-md text-outline font-label-md">Atau</span>
                    <div class="h-[1px] flex-1 bg-outline-variant"></div>
                </div>

                {{-- Tombol Masuk dengan Google --}}
                <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center gap-base bg-surface border border-outline-variant py-3 rounded-lg hover:bg-surface-container-low transition-colors shadow-[0px_2px_4px_rgba(0,0,0,0.05)]">
                    <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" alt="Google Logo" class="w-5 h-5">
                    <span class="font-headline-sm text-headline-sm text-on-surface">Masuk dengan Google</span>
                </a>



                <p class="text-center font-body-sm text-body-sm text-on-surface-variant mt-4">
                    Sudah punya akun? <a href="{{ route('login') }}" class="text-primary font-label-md hover:underline">Masuk di sini</a>
                </p>
            </form>
        </div>

        {{-- Desktop Card --}}
        <div class="hidden md:flex flex-col bg-surface rounded-xl shadow-[0px_4px_20px_rgba(26,54,93,0.08)] border border-outline-variant overflow-hidden animate-fade-in-up">
            <div class="p-stack-lg border-b border-outline-variant text-center bg-surface-bright">
                <h1 class="font-display text-display-lg text-primary mb-stack-sm">Barber Woi</h1>
                <p class="font-body text-body-md text-on-surface-variant">Daftar Akun Baru</p>
            </div>

            <div class="p-stack-lg">
                <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-stack-md">
                    @csrf
                    @if (session('status'))
                        <div class="bg-green-50 text-green-700 p-3 rounded-lg text-sm">{{ session('status') }}</div>
                    @endif

                    {{-- Nama Lengkap --}}
                    <div class="flex flex-col gap-1">
                        <label class="font-label-md text-label-md text-on-surface" for="name-desktop">Nama Lengkap</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">badge</span>
                            <input type="text" name="name" id="name-desktop" value="{{ old('name') }}" required autofocus
                                   class="form-input w-full pl-10 pr-4 py-3 rounded-lg border border-outline-variant bg-surface text-on-surface font-body-md text-body-md"
                                   placeholder="Masukkan nama lengkap">
                        </div>
                        @error('name')<p class="text-error text-body-sm">{{ $message }}</p>@enderror
                    </div>

                    {{-- Email --}}
                    <div class="flex flex-col gap-1">
                        <label class="font-label-md text-label-md text-on-surface" for="email-desktop">Email</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">mail</span>
                            <input type="email" name="email" id="email-desktop" value="{{ old('email') }}" required
                                   class="form-input w-full pl-10 pr-4 py-3 rounded-lg border border-outline-variant bg-surface text-on-surface font-body-md text-body-md"
                                   placeholder="Masukkan email">
                        </div>
                        @error('email')<p class="text-error text-body-sm">{{ $message }}</p>@enderror
                    </div>

                    {{-- Nomor Telepon --}}
                    <div class="flex flex-col gap-1">
                        <label class="font-label-md text-label-md text-on-surface" for="phone-desktop">Nomor Telepon</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">call</span>
                            <input type="tel" name="phone" id="phone-desktop" value="{{ old('phone') }}" required
                                   class="form-input w-full pl-10 pr-4 py-3 rounded-lg border border-outline-variant bg-surface text-on-surface font-body-md text-body-md"
                                   placeholder="Masukkan nomor telepon">
                        </div>
                        @error('phone')<p class="text-error text-body-sm">{{ $message }}</p>@enderror
                    </div>

                    {{-- Kata Sandi --}}
                    <div class="flex flex-col gap-1">
                        <label class="font-label-md text-label-md text-on-surface" for="password-desktop">Kata Sandi</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">lock</span>
                            <input type="password" name="password" id="password-desktop" required
                                   class="form-input w-full pl-10 pr-10 py-3 rounded-lg border border-outline-variant bg-surface text-on-surface font-body-md text-body-md"
                                   placeholder="Masukkan kata sandi (min. 8 karakter)">
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors"
                                    onclick="const input = document.getElementById('password-desktop'); const icon = this.querySelector('span'); if(input.type === 'password'){ input.type = 'text'; icon.textContent = 'visibility_off'; } else { input.type = 'password'; icon.textContent = 'visibility'; }">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                        </div>
                        @error('password')<p class="text-error text-body-sm">{{ $message }}</p>@enderror
                    </div>

                    {{-- Konfirmasi Kata Sandi --}}
                    <div class="flex flex-col gap-1">
                        <label class="font-label-md text-label-md text-on-surface" for="password_confirmation-desktop">Konfirmasi Kata Sandi</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">lock</span>
                            <input type="password" name="password_confirmation" id="password_confirmation-desktop" required
                                   class="form-input w-full pl-10 pr-10 py-3 rounded-lg border border-outline-variant bg-surface text-on-surface font-body-md text-body-md"
                                   placeholder="Ulangi kata sandi">
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors"
                                    onclick="const input = document.getElementById('password_confirmation-desktop'); const icon = this.querySelector('span'); if(input.type === 'password'){ input.type = 'text'; icon.textContent = 'visibility_off'; } else { input.type = 'password'; icon.textContent = 'visibility'; }">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>
                        </div>
                        @error('password_confirmation')<p class="text-error text-body-sm">{{ $message }}</p>@enderror
                    </div>


                    <button type="submit" class="mt-stack-md w-full bg-[#D4AF37] hover:bg-[#b8952d] text-[#1A365D] font-headline-sm text-headline-sm py-3 rounded-lg transition-colors shadow-md">
                        Daftar
                    </button>

                    
                     {{-- Divider "Atau" --}}
                <div class="flex items-center gap-base my-stack-md">
                    <div class="h-[1px] flex-1 bg-outline-variant"></div>
                    <span class="text-label-md text-outline font-label-md">Atau</span>
                    <div class="h-[1px] flex-1 bg-outline-variant"></div>
                </div>

                {{-- Tombol Masuk dengan Google --}}
                <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center gap-base bg-surface border border-outline-variant py-3 rounded-lg hover:bg-surface-container-low transition-colors shadow-[0px_2px_4px_rgba(0,0,0,0.05)]">
                    <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" alt="Google Logo" class="w-5 h-5">
                    <span class="font-headline-sm text-headline-sm text-on-surface">Masuk dengan Google</span>
                </a>


                    <div class="text-center mt-stack-md">
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="text-secondary hover:underline font-label-md">Masuk di sini</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </main>

</body>
</html>