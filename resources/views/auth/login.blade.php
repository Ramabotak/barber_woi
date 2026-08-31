<x-auth-shell
    title="Masuk"
    eyebrow="Member access"
    heading="Senang melihatmu lagi."
    description="Masuk untuk melihat jadwal, antrean, dan booking Anda.">
    @if (session('status'))
        <div class="mb-5 flex gap-3 rounded-xl border border-brandsuccess/20 bg-brandsuccess/10 px-4 py-3 text-sm leading-5 text-brandsuccess">
            <span class="material-symbols-outlined mt-0.5 text-[18px]">check_circle</span>{{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <div>
            <label class="mb-2 block text-xs font-bold text-charcoal" for="email">Alamat email</label>
            <div class="relative"><span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[19px] text-muted">mail</span><input class="auth-input h-12 pl-11 pr-4" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus autocomplete="username"></div>
            @error('email')<p class="mt-2 text-xs font-medium text-branddanger">{{ $message }}</p>@enderror
        </div>

        <div>
            <div class="mb-2 flex items-center justify-between gap-4"><label class="text-xs font-bold text-charcoal" for="password">Kata sandi</label>@if (Route::has('password.request'))<a class="text-xs font-bold text-brandwarning transition hover:text-charcoal" href="{{ route('password.request') }}">Lupa kata sandi?</a>@endif</div>
            <div class="relative"><span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[19px] text-muted">lock</span><input class="auth-input h-12 pl-11 pr-12" id="password" name="password" type="password" placeholder="Masukkan kata sandi" required autocomplete="current-password"><button class="absolute right-2 top-1/2 grid h-8 w-8 -translate-y-1/2 place-items-center rounded-lg text-muted transition hover:bg-cream hover:text-charcoal" id="toggle-password" type="button" aria-label="Tampilkan kata sandi"><span class="material-symbols-outlined text-[19px]">visibility</span></button></div>
            @error('password')<p class="mt-2 text-xs font-medium text-branddanger">{{ $message }}</p>@enderror
        </div>

        <label class="flex cursor-pointer items-center gap-2.5 text-xs text-muted"><input type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-brandwarning focus:ring-brandwarning"><span>Ingat saya di perangkat ini</span></label>

        <button class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-charcoal px-5 py-3 text-sm font-extrabold text-white shadow-[0_10px_20px_rgba(23,23,22,0.15)] transition hover:-translate-y-0.5 hover:bg-black hover:shadow-[0_14px_25px_rgba(23,23,22,0.2)]" type="submit">Masuk ke akun<span class="material-symbols-outlined text-[18px] text-gold">arrow_forward</span></button>
    </form>

    <div class="my-7 flex items-center gap-3"><span class="h-px flex-1 bg-charcoal/10"></span><span class="text-[10px] font-bold uppercase tracking-[0.14em] text-muted">atau</span><span class="h-px flex-1 bg-charcoal/10"></span></div>

    <a href="{{ route('google.login') }}" class="inline-flex min-h-12 w-full items-center justify-center gap-3 rounded-xl border border-charcoal/12 bg-white px-5 py-3 text-sm font-bold text-charcoal transition hover:border-charcoal/30 hover:bg-cream">
        <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09A7.1 7.1 0 0 1 5.49 12c0-.73.13-1.43.35-2.09V7.07H2.18A10.98 10.98 0 0 0 1 12c0 1.78.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
        Lanjutkan dengan Google
    </a>

    @if (Route::has('register'))<p class="mt-7 text-center text-sm text-muted">Belum punya akun? <a href="{{ route('register') }}" class="font-extrabold text-charcoal underline decoration-gold decoration-2 underline-offset-4 transition hover:text-brandwarning">Buat akun</a></p>@endif

    <script>
        document.getElementById('toggle-password')?.addEventListener('click', () => {
            const input = document.getElementById('password');
            const icon = document.querySelector('#toggle-password span');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.textContent = isHidden ? 'visibility_off' : 'visibility';
        });
    </script>
</x-auth-shell>
