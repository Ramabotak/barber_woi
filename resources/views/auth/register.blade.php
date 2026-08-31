<x-auth-shell
    title="Buat akun"
    eyebrow="New member"
    heading="Mulai dari kursi yang tepat."
    description="Buat akun untuk memilih barber, menentukan jadwal, dan mengikuti status booking.">
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div><label class="mb-2 block text-xs font-bold text-charcoal" for="name">Nama lengkap</label><div class="relative"><span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[19px] text-muted">person</span><input class="auth-input h-12 pl-11 pr-4" id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Nama Anda" required autofocus autocomplete="name"></div>@error('name')<p class="mt-2 text-xs font-medium text-branddanger">{{ $message }}</p>@enderror</div>
        <div><label class="mb-2 block text-xs font-bold text-charcoal" for="email">Alamat email</label><div class="relative"><span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[19px] text-muted">mail</span><input class="auth-input h-12 pl-11 pr-4" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@email.com" required autocomplete="username"></div>@error('email')<p class="mt-2 text-xs font-medium text-branddanger">{{ $message }}</p>@enderror</div>
        <div><label class="mb-2 block text-xs font-bold text-charcoal" for="phone">Nomor WhatsApp <span class="font-medium text-muted">(opsional)</span></label><div class="relative"><span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[19px] text-muted">phone</span><input class="auth-input h-12 pl-11 pr-4" id="phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" autocomplete="tel"></div>@error('phone')<p class="mt-2 text-xs font-medium text-branddanger">{{ $message }}</p>@enderror</div>
        <div><label class="mb-2 block text-xs font-bold text-charcoal" for="password">Kata sandi</label><div class="relative"><span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[19px] text-muted">lock</span><input class="auth-input h-12 pl-11 pr-12" id="password" name="password" type="password" placeholder="Minimal 8 karakter" required autocomplete="new-password"><button type="button" class="toggle-password absolute right-2 top-1/2 grid h-8 w-8 -translate-y-1/2 place-items-center rounded-lg text-muted transition hover:bg-cream hover:text-charcoal" data-target="password" aria-label="Tampilkan kata sandi"><span class="material-symbols-outlined text-[19px]">visibility</span></button></div>@error('password')<p class="mt-2 text-xs font-medium text-branddanger">{{ $message }}</p>@enderror</div>
        <div><label class="mb-2 block text-xs font-bold text-charcoal" for="password_confirmation">Konfirmasi kata sandi</label><div class="relative"><span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[19px] text-muted">lock</span><input class="auth-input h-12 pl-11 pr-12" id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi kata sandi" required autocomplete="new-password"><button type="button" class="toggle-password absolute right-2 top-1/2 grid h-8 w-8 -translate-y-1/2 place-items-center rounded-lg text-muted transition hover:bg-cream hover:text-charcoal" data-target="password_confirmation" aria-label="Tampilkan kata sandi"><span class="material-symbols-outlined text-[19px]">visibility</span></button></div></div>
        <button class="mt-2 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-charcoal px-5 py-3 text-sm font-extrabold text-white shadow-[0_10px_20px_rgba(23,23,22,0.15)] transition hover:-translate-y-0.5 hover:bg-black" type="submit">Buat akun<span class="material-symbols-outlined text-[18px] text-gold">arrow_forward</span></button>
    </form>
    <p class="mt-7 text-center text-sm text-muted">Sudah punya akun? <a href="{{ route('login') }}" class="font-extrabold text-charcoal underline decoration-gold decoration-2 underline-offset-4 transition hover:text-brandwarning">Masuk</a></p>
    <script>
        document.querySelectorAll('.toggle-password').forEach((button) => button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.target);
            const icon = button.querySelector('span');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.textContent = isHidden ? 'visibility_off' : 'visibility';
        }));
    </script>
</x-auth-shell>
