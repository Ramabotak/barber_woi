@extends('layouts.customer')

@section('title', 'Checkout Booking - Barber Woi')

@section('content')
    @php
        $amount = $payment?->amount ?? $booking->service->price;
        $paymentData = $payment?->payment_data ?? [];
        $vaNumber = data_get($paymentData, 'instruction.va_number') ?? data_get($paymentData, 'va_numbers.0.va_number') ?? data_get($paymentData, 'permata_va_number');
        $qrAction = collect($paymentData['actions'] ?? [])->firstWhere('name', 'generate-qr-code');
        $qrUrl = $qrAction['url'] ?? null;
        $walletAction = collect($paymentData['actions'] ?? [])->first(fn ($action) => in_array($action['name'] ?? '', ['deeplink-redirect', 'get-redirect-url', 'mobile_web_checkout_url'], true));
        $walletUrl = $walletAction['url'] ?? null;
        $expiryAt = $payment?->expires_at ?? data_get($paymentData, 'expiry_time');
        $gatewayError = (int) data_get($paymentData, 'status_code', 200) >= 400;
        $gatewayErrorMessage = data_get($paymentData, 'status_message') ?? 'Midtrans belum dapat membuat instruksi pembayaran untuk metode ini.';
    @endphp
    <div class="payment-checkout page-enter">
        <div class="mb-6 flex items-center justify-between sm:mb-8">
            <a href="{{ route('customer.booking.show', $booking) }}" class="inline-flex items-center gap-2 text-sm font-bold text-muted transition hover:text-charcoal">
                <span class="material-symbols-outlined text-[19px]">arrow_back</span>
                Kembali ke booking
            </a>
            <div class="hidden items-center gap-2 text-xs font-semibold text-brandsuccess sm:flex">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-brandsuccess/10"><span class="material-symbols-outlined text-[16px]">verified_user</span></span>
                Checkout aman
            </div>
        </div>

        <div class="overflow-hidden rounded-[28px] border border-charcoal/10 bg-charcoal px-6 py-7 text-white shadow-[0_22px_55px_rgba(28,28,30,.16)] sm:px-9 sm:py-9">
            <div class="relative flex flex-col justify-between gap-7 sm:flex-row sm:items-end">
                <div>
                    <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-gold/30 bg-white/5 px-3 py-1.5 text-[11px] font-bold uppercase tracking-[.16em] text-gold"><span class="h-1.5 w-1.5 rounded-full bg-gold"></span> Checkout Barber Woi</div>
                    <h1 class="font-heading text-2xl font-extrabold tracking-tight sm:text-3xl">Satu langkah menuju<br class="hidden sm:block"> gaya terbaikmu.</h1>
                    <p class="mt-3 max-w-md text-sm leading-6 text-white/60">Konfirmasi booking dan lanjutkan pembayaran lewat kanal yang paling nyaman untukmu.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/[.07] px-4 py-3 sm:min-w-[190px]">
                    <p class="text-[10px] font-bold uppercase tracking-[.14em] text-white/45">Total pembayaran</p>
                    <p class="mt-1 font-heading text-2xl font-extrabold text-gold">Rp {{ number_format($amount, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1.28fr)_minmax(300px,.72fr)] lg:items-start">
            <section class="rounded-[24px] border border-charcoal/10 bg-white p-5 shadow-sm sm:p-7">
                <div class="flex items-start justify-between gap-4 border-b border-gray-100 pb-5">
                    <div><p class="text-[11px] font-bold uppercase tracking-[.14em] text-gold">Detail reservasi</p><h2 class="mt-1 font-heading text-lg font-extrabold text-charcoal">{{ $booking->service->service_name }}</h2></div>
                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-brandwarning/10 px-3 py-1.5 text-[11px] font-bold text-brandwarning"><span class="h-1.5 w-1.5 rounded-full bg-brandwarning"></span> Menunggu bayar</span>
                </div>
                <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="flex items-center gap-3 rounded-2xl bg-cream p-3.5"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-gold shadow-sm"><span class="material-symbols-outlined text-[20px]">content_cut</span></span><div class="min-w-0"><dt class="text-[10px] font-bold uppercase tracking-wider text-muted">Barber</dt><dd class="mt-0.5 truncate text-sm font-bold text-charcoal">{{ $booking->barber->user->name }}</dd></div></div>
                    <div class="flex items-center gap-3 rounded-2xl bg-cream p-3.5"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-gold shadow-sm"><span class="material-symbols-outlined text-[20px]">calendar_month</span></span><div class="min-w-0"><dt class="text-[10px] font-bold uppercase tracking-wider text-muted">Jadwal</dt><dd class="mt-0.5 text-sm font-bold text-charcoal">{{ $booking->schedule?->date?->translatedFormat('d M Y') ?? '-' }}</dd></div></div>
                    <div class="flex items-center gap-3 rounded-2xl bg-cream p-3.5 sm:col-span-2"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-gold shadow-sm"><span class="material-symbols-outlined text-[20px]">schedule</span></span><div><dt class="text-[10px] font-bold uppercase tracking-wider text-muted">Jam booking</dt><dd class="mt-0.5 text-sm font-bold text-charcoal">{{ $booking->slot_time?->format('H:i') ?? '-' }} WIB</dd></div></div>
                </dl>
                <div class="mt-7"><div class="flex items-center justify-between"><div><p class="text-sm font-extrabold text-charcoal">Pembayaran fleksibel</p><p class="mt-0.5 text-xs text-muted">Pilih metode pada tahap pembayaran aman.</p></div><span class="material-symbols-outlined text-gold">account_balance_wallet</span></div><div class="mt-4 grid grid-cols-3 gap-2.5"><div class="payment-method"><span class="material-symbols-outlined">qr_code_2</span><span>QRIS</span></div><div class="payment-method"><span class="material-symbols-outlined">account_balance</span><span>Transfer bank</span></div><div class="payment-method"><span class="material-symbols-outlined">smartphone</span><span>E-wallet</span></div></div></div>
            </section>

            <aside class="rounded-[24px] border border-gold/25 bg-[#fffcf5] p-5 shadow-sm sm:p-6 lg:sticky lg:top-24">
                <div class="flex items-center justify-between"><p class="font-heading text-base font-extrabold text-charcoal">Ringkasan tagihan</p><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gold/15 text-gold"><span class="material-symbols-outlined text-[20px]">receipt_long</span></span></div>
                <div class="mt-5 space-y-3 border-b border-gold/20 pb-5 text-sm"><div class="flex justify-between gap-4"><span class="text-muted">{{ $booking->service->service_name }}</span><span class="font-semibold text-charcoal">Rp {{ number_format($amount, 0, ',', '.') }}</span></div><div class="flex justify-between"><span class="text-muted">Biaya layanan</span><span class="font-semibold text-brandsuccess">Gratis</span></div></div>
                <div class="mt-5 flex items-end justify-between"><span class="text-sm font-bold text-charcoal">Total</span><span class="font-heading text-2xl font-extrabold text-charcoal">Rp {{ number_format($amount, 0, ',', '.') }}</span></div>
                @if($payment && $payment->payment_data && !$gatewayError)
                    <form method="POST" action="{{ route('customer.payment.check', $booking) }}" class="mt-6">@csrf<button type="submit" class="inline-flex min-h-14 w-full items-center justify-center gap-2 rounded-2xl bg-charcoal px-5 py-3.5 text-sm font-extrabold text-white transition hover:bg-charcoal/90"><span class="material-symbols-outlined text-[20px]">sync</span>Cek Status Pembayaran</button></form>
                @elseif($payment && $payment->snap_token && $paymentDriver === 'snap')
                    <button id="pay-button" type="button" class="mt-6 inline-flex min-h-14 w-full items-center justify-center gap-2 rounded-2xl bg-gold px-5 py-3.5 text-sm font-extrabold text-charcoal shadow-[0_8px_18px_rgba(201,162,75,.28)] transition hover:bg-[#dbb45d] focus:outline-none focus:ring-4 focus:ring-gold/30 disabled:cursor-wait disabled:opacity-70"><span class="material-symbols-outlined text-[20px]">lock</span>Lanjutkan Pembayaran<span class="material-symbols-outlined text-[18px]">arrow_forward</span></button>
                    <button id="change-method" type="button" class="mt-2 inline-flex w-full items-center justify-center gap-2 rounded-xl py-2 text-xs font-bold text-muted transition hover:bg-gold/10 hover:text-charcoal"><span class="material-symbols-outlined text-[16px]">swap_horiz</span>Ganti metode pembayaran</button>
                @else
                    <button id="pay-button" type="button" class="mt-6 inline-flex min-h-14 w-full items-center justify-center gap-2 rounded-2xl bg-gold px-5 py-3.5 text-sm font-extrabold text-charcoal shadow-[0_8px_18px_rgba(201,162,75,.28)] transition hover:bg-[#dbb45d] focus:outline-none focus:ring-4 focus:ring-gold/30"><span class="material-symbols-outlined text-[20px]">lock</span>Pilih Metode Bayar<span class="material-symbols-outlined text-[18px]">arrow_forward</span></button>
                @endif
                <div class="mt-4 flex gap-2.5 rounded-xl bg-white/70 p-3 text-[11px] leading-4 text-muted"><span class="material-symbols-outlined shrink-0 text-[17px] text-brandsuccess">verified_user</span><p>Data pembayaran diproses secara terenkripsi melalui Midtrans, penyedia pembayaran resmi kami.</p></div>
            </aside>
        </div>
        <div id="pay-error" role="alert" class="mt-5 hidden rounded-2xl border border-branddanger/20 bg-red-50 px-4 py-3 text-sm font-medium text-branddanger"></div>
        <form id="check-status-form" action="{{ route('customer.payment.check', $booking) }}" method="POST" class="hidden">@csrf</form>
      @if($payment && $payment->payment_data && !$gatewayError && $paymentDriver === 'core')
            <section class="mt-6 rounded-[24px] border border-brandsuccess/20 bg-white p-5 shadow-sm sm:p-7">
                <div class="flex flex-wrap items-start justify-between gap-4"><div class="flex items-center gap-3"><span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brandsuccess/10 text-brandsuccess"><span class="material-symbols-outlined">payments</span></span><div><p class="text-[11px] font-bold uppercase tracking-wider text-brandsuccess">Instruksi pembayaran</p><h2 class="font-heading text-lg font-extrabold text-charcoal">{{ $paymentData['method_label'] ?? 'Pembayaran' }}</h2></div></div><div class="text-right"><p class="text-[10px] font-bold uppercase tracking-wider text-muted">Selesaikan sebelum</p><p class="mt-0.5 text-sm font-extrabold text-brandwarning" data-expiry="{{ $expiryAt }}">Memuat waktu…</p></div></div>
                <div class="mt-5 flex items-center justify-between rounded-2xl bg-cream p-4"><div><p class="text-[10px] font-bold uppercase tracking-wider text-muted">Order ID</p><p class="mt-1 font-mono text-xs font-bold text-charcoal">{{ $payment->transaction_id }}</p></div><button type="button" data-copy="{{ $payment->transaction_id }}" class="inline-flex items-center gap-1.5 rounded-xl bg-white px-3 py-2 text-xs font-bold text-charcoal shadow-sm"><span class="material-symbols-outlined text-[16px]">content_copy</span>Salin</button></div>
                @if($vaNumber)<div class="mt-5 rounded-2xl bg-cream p-4"><p class="text-xs text-muted">Nomor Virtual Account {{ strtoupper(data_get($paymentData, 'instruction.bank') ?? data_get($paymentData, 'va_numbers.0.bank', $payment->payment_method)) }}</p><div class="mt-1 flex flex-wrap items-center justify-between gap-3"><p class="break-all font-heading text-2xl font-extrabold tracking-wider text-charcoal">{{ $vaNumber }}</p><button type="button" data-copy="{{ $vaNumber }}" class="rounded-xl border border-charcoal/10 bg-white px-3 py-2 text-xs font-bold text-charcoal">Salin nomor</button></div><ol class="mt-4 list-decimal space-y-1.5 pl-4 text-xs leading-5 text-muted"><li>Buka ATM, mobile banking, atau internet banking bank Anda.</li><li>Pilih menu transfer atau pembayaran Virtual Account.</li><li>Masukkan nomor VA di atas, lalu periksa nama dan nominal pembayaran.</li><li>Selesaikan pembayaran sebelum waktu habis, kemudian tekan Cek Status.</li></ol></div>@endif
                @if(data_get($paymentData, 'payment_type') === 'echannel')<div class="mt-5 rounded-2xl bg-cream p-4"><p class="text-xs text-muted">Kode pembayaran Mandiri Bill</p><p class="mt-1 font-heading text-2xl font-extrabold tracking-wider text-charcoal">{{ data_get($paymentData, 'instruction.bill_key') ?? data_get($paymentData, 'bill_key') }}</p><p class="mt-2 text-xs text-muted">Biller code: <strong>{{ data_get($paymentData, 'instruction.biller_code') ?? data_get($paymentData, 'biller_code') }}</strong></p><ol class="mt-4 list-decimal space-y-1.5 pl-4 text-xs leading-5 text-muted"><li>Buka Livin', ATM, atau internet banking Mandiri.</li><li>Pilih menu pembayaran atau Mandiri Bill Payment.</li><li>Masukkan biller code dan bill key di atas.</li><li>Konfirmasi nominal, lalu tekan Cek Status setelah pembayaran selesai.</li></ol></div>@endif
                @if(!$isProduction && in_array(data_get($paymentData, 'payment_type'), ['bank_transfer', 'echannel'], true))<form method="POST" action="{{ route('customer.payment.sandbox-simulator', $booking) }}" class="mt-5">@csrf<button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-gold/35 bg-gold/10 px-4 text-sm font-extrabold text-charcoal transition hover:bg-gold/20"><span class="material-symbols-outlined text-[19px]">science</span>Buka Simulator VA Sandbox</button></form><p class="mt-2 text-center text-[11px] leading-4 text-muted">Khusus testing: masukkan nomor VA, atau biller code dan bill key Mandiri, pada simulator resmi Midtrans.</p>@endif
                @if($qrUrl)<div class="mt-5 flex flex-col items-center rounded-2xl bg-cream p-5 text-center"><p class="mb-3 text-sm font-bold text-charcoal">Scan QRIS untuk membayar</p><img src="{{ $qrUrl }}" alt="Kode QRIS pembayaran" class="h-56 w-56 rounded-xl bg-white p-2 shadow-sm"><a href="{{ $qrUrl }}" download class="mt-4 inline-flex items-center gap-2 rounded-xl border border-charcoal/15 bg-white px-4 py-2.5 text-xs font-bold text-charcoal"><span class="material-symbols-outlined text-[17px]">download</span>Download QRIS</a><p class="mt-3 text-xs text-muted">Buka aplikasi pembayaran Anda, lalu pindai kode QR di atas.</p></div>@endif
                @if($walletUrl)<div class="mt-5 rounded-2xl bg-cream p-5 text-center"><span class="material-symbols-outlined text-4xl text-gold">smartphone</span><p class="mt-2 text-sm font-extrabold text-charcoal">Lanjutkan di aplikasi {{ $paymentData['method_label'] ?? 'e-wallet' }}</p><p class="mt-1 text-xs text-muted">Buka aplikasi untuk menyelesaikan pembayaran dengan aman.</p><a href="{{ $walletUrl }}" class="mt-4 inline-flex min-h-12 items-center justify-center rounded-xl bg-charcoal px-5 text-sm font-extrabold text-white">Buka aplikasi pembayaran</a></div>@endif
                @if(data_get($paymentData, 'redirect_url'))<div class="mt-5 rounded-2xl bg-cream p-5 text-center"><span class="material-symbols-outlined text-4xl text-gold">credit_card</span><p class="mt-2 text-sm font-extrabold text-charcoal">Verifikasi 3D Secure diperlukan</p><p class="mt-1 text-xs text-muted">Lanjutkan verifikasi OTP dari bank penerbit kartu Anda.</p><button type="button" id="authenticate-card" data-url="{{ data_get($paymentData, 'redirect_url') }}" class="mt-4 min-h-12 rounded-xl bg-charcoal px-5 text-sm font-extrabold text-white">Verifikasi kartu</button></div>@endif
            </section>
        @endif
        @if($gatewayError)
            <section class="mt-6 rounded-[24px] border border-brandwarning/25 bg-[#fffcf5] p-5 shadow-sm sm:p-7">
                <div class="flex items-start gap-3"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-brandwarning/10 text-brandwarning"><span class="material-symbols-outlined">info</span></span><div><p class="text-[11px] font-bold uppercase tracking-wider text-brandwarning">Metode belum tersedia</p><h2 class="mt-1 font-heading text-lg font-extrabold text-charcoal">Instruksi Virtual Account belum dibuat</h2><p class="mt-2 text-sm leading-6 text-muted">{{ $gatewayErrorMessage }} Pilih metode lain atau coba kembali setelah kanal pembayaran diaktifkan.</p></div></div>
                <button id="retry-payment" type="button" class="mt-5 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-charcoal px-4 text-sm font-extrabold text-white transition hover:bg-charcoal/90"><span class="material-symbols-outlined text-[19px]">restart_alt</span>Pilih metode lain</button>
            </section>
        @endif

        <div id="payment-modal" class="fixed inset-0 z-[60] hidden items-end justify-center bg-charcoal/70 p-0 backdrop-blur-[3px] sm:items-center sm:p-4" role="dialog" aria-modal="true" aria-labelledby="payment-modal-title">
            <div class="payment-modal-panel w-full max-w-[540px] overflow-hidden rounded-t-[30px] bg-[#faf9f6] shadow-[0_24px_80px_rgba(0,0,0,.42)] sm:max-h-[calc(100vh-2rem)] sm:rounded-[30px]">
                <div class="relative overflow-hidden bg-charcoal px-5 pb-5 pt-6 text-white sm:px-7 sm:pb-6 sm:pt-7">
                    <div class="absolute -right-12 -top-16 h-40 w-40 rounded-full border-[28px] border-gold/15"></div><div class="absolute -bottom-10 left-8 h-20 w-20 rounded-full bg-gold/10 blur-2xl"></div>
                    <div class="relative flex items-start justify-between"><div class="flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-xl border border-gold/30 bg-gold/10"><x-application-logo class="h-full w-full scale-110" /></span><div><p class="font-heading text-base font-extrabold tracking-tight">Barber Woi</p><p class="mt-0.5 text-[10px] font-bold uppercase tracking-[.15em] text-gold">Secure checkout</p></div></div><button type="button" data-close-payment class="rounded-xl border border-white/10 bg-white/5 p-2 text-white/70 transition hover:bg-white/10 hover:text-white" aria-label="Tutup"><span class="material-symbols-outlined text-[20px]">close</span></button></div>
                    <div class="relative mt-6 flex items-end justify-between gap-4"><div><p class="text-[10px] font-bold uppercase tracking-[.15em] text-white/45">Total pembayaran</p><p class="mt-1 font-heading text-3xl font-extrabold tracking-tight text-gold">Rp {{ number_format($amount, 0, ',', '.') }}</p></div><span class="rounded-full border border-gold/25 bg-gold/10 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-gold">{{ $isProduction ? 'Pembayaran aman' : 'Sandbox test' }}</span></div>
                </div>
                <div class="max-h-[calc(100vh-246px)] overflow-y-auto px-5 pb-6 pt-5 sm:max-h-[calc(100vh-270px)] sm:px-7 sm:pb-7">
                    <div class="flex items-center justify-between rounded-2xl border border-charcoal/8 bg-white px-4 py-3"><div><p class="text-[10px] font-bold uppercase tracking-wider text-muted">Kode booking</p><p class="mt-0.5 font-mono text-xs font-bold text-charcoal">{{ $booking->booking_code }}</p></div><div class="text-right"><p class="text-[10px] font-bold uppercase tracking-wider text-muted">Selesaikan dalam</p><p id="payment-countdown" class="mt-0.5 font-mono text-sm font-extrabold text-brandwarning">24:00:00</p></div></div>
                    <div class="mt-6 flex items-center justify-between"><div><p class="text-[11px] font-bold uppercase tracking-[.14em] text-gold">Pilih cara bayar</p><h2 id="payment-modal-title" class="mt-1 font-heading text-lg font-extrabold text-charcoal">Metode yang nyaman untukmu</h2></div><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gold/10 text-gold"><span class="material-symbols-outlined">account_balance_wallet</span></span></div>
                    <form id="method-form" method="POST" action="{{ route('customer.payment.method', $booking) }}" class="mt-4 space-y-2.5">@csrf<input id="selected-method" type="hidden" name="method">
                        <button type="button" data-method="qris" class="method-choice group"><span class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#1c1c1e] text-gold shadow-sm"><span class="material-symbols-outlined text-[27px]">qr_code_2</span></span><span class="flex-1 text-left"><span class="flex items-center gap-2 font-extrabold text-charcoal">QRIS <span class="rounded bg-gold/15 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-brandwarning">Praktis</span></span><span class="mt-1 block text-xs font-normal leading-4 text-muted">Scan dari GoPay, DANA, ShopeePay, atau mobile banking.</span></span><span class="material-symbols-outlined text-muted transition group-hover:translate-x-0.5 group-hover:text-gold">arrow_forward</span></button>
                        <div class="flex items-center gap-3 py-2"><span class="h-px flex-1 bg-charcoal/8"></span><p class="text-[10px] font-bold uppercase tracking-[.14em] text-muted">Virtual Account</p><span class="h-px flex-1 bg-charcoal/8"></span></div>
                        @foreach(['bca' => ['BCA', 'BCA Virtual Account', 'bg-[#00529b]'], 'bni' => ['BNI', 'BNI Virtual Account', 'bg-[#ec6b23]'], 'bri' => ['BRI', 'BRI Virtual Account', 'bg-[#00529c]'], 'permata' => ['PMT', 'Permata Virtual Account', 'bg-[#df242d]']] as $key => [$bank, $label, $color])
                            <button type="button" data-method="{{ $key }}" class="method-choice group"><span class="flex h-12 w-12 items-center justify-center rounded-xl {{ $color }} text-[11px] font-extrabold text-white shadow-sm">{{ $bank }}</span><span class="flex-1 text-left"><span class="font-extrabold text-charcoal">{{ $label }}</span><span class="mt-1 block text-xs font-normal text-muted">Nomor VA dibuat otomatis untuk booking ini.</span></span><span class="material-symbols-outlined text-muted transition group-hover:translate-x-0.5 group-hover:text-gold">arrow_forward</span></button>
                        @endforeach
                        <div class="flex items-center gap-3 py-2"><span class="h-px flex-1 bg-charcoal/8"></span><p class="text-[10px] font-bold uppercase tracking-[.14em] text-muted">E-wallet &amp; pembayaran lain</p><span class="h-px flex-1 bg-charcoal/8"></span></div>
                        <button type="button" data-method="gopay" class="method-choice group"><span class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#00aad8] text-[11px] font-extrabold text-white shadow-sm">GO</span><span class="flex-1 text-left"><span class="font-extrabold text-charcoal">GoPay</span><span class="mt-1 block text-xs font-normal text-muted">Lanjutkan pembayaran secara aman di aplikasi GoPay.</span></span><span class="material-symbols-outlined text-muted transition group-hover:translate-x-0.5 group-hover:text-gold">arrow_forward</span></button>
                        <button type="button" data-method="shopeepay" class="method-choice group"><span class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#ee4d2d] text-[11px] font-extrabold text-white shadow-sm">SP</span><span class="flex-1 text-left"><span class="font-extrabold text-charcoal">ShopeePay</span><span class="mt-1 block text-xs font-normal text-muted">Lanjutkan pembayaran secara aman di aplikasi ShopeePay.</span></span><span class="material-symbols-outlined text-muted transition group-hover:translate-x-0.5 group-hover:text-gold">arrow_forward</span></button>
                        <button type="button" data-method="mandiri_bill" class="method-choice group"><span class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#003d79] text-[10px] font-extrabold text-white shadow-sm">MDR</span><span class="flex-1 text-left"><span class="font-extrabold text-charcoal">Mandiri Bill</span><span class="mt-1 block text-xs font-normal text-muted">Gunakan biller code dan bill key di Livin' atau ATM Mandiri.</span></span><span class="material-symbols-outlined text-muted transition group-hover:translate-x-0.5 group-hover:text-gold">arrow_forward</span></button>
                    </form>
                    <div class="mt-5 flex items-start gap-2.5 rounded-xl bg-brandsuccess/8 px-3 py-2.5 text-[11px] leading-4 text-muted"><span class="material-symbols-outlined mt-0.5 shrink-0 text-[17px] text-brandsuccess">verified_user</span><p>Transaksi dienkripsi dan booking hanya diproses setelah status pembayaran terverifikasi.</p></div>
                </div>
            </div>
        </div>
        <div id="payment-confirm-modal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-charcoal/70 p-4 backdrop-blur-[3px]" role="dialog" aria-modal="true" aria-labelledby="payment-confirm-title">
            <div class="w-full max-w-md overflow-hidden rounded-[28px] bg-white shadow-2xl">
                <div class="bg-charcoal px-6 py-6 text-white"><div class="flex items-center justify-between"><span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gold/15 text-gold"><span class="material-symbols-outlined">lock</span></span><button type="button" data-close-confirm class="rounded-xl p-2 text-white/60 hover:bg-white/10 hover:text-white"><span class="material-symbols-outlined">close</span></button></div><p class="mt-5 text-[11px] font-bold uppercase tracking-[.15em] text-gold">Konfirmasi pembayaran</p><h2 id="payment-confirm-title" class="mt-1 font-heading text-xl font-extrabold">Lanjutkan dengan <span id="confirm-method-label">metode ini</span>?</h2></div>
                <div class="p-6"><div class="rounded-2xl bg-cream p-4"><div class="flex justify-between text-xs text-muted"><span>Total tagihan</span><span>{{ $booking->booking_code }}</span></div><p class="mt-1 font-heading text-2xl font-extrabold text-charcoal">Rp {{ number_format($amount, 0, ',', '.') }}</p></div><div id="qris-sandbox-note" class="mt-4 hidden rounded-xl border border-gold/20 bg-gold/10 p-3 text-xs leading-5 text-charcoal"><span class="font-bold">Mode Sandbox:</span> jangan pindai QR dengan aplikasi pembayaran asli. Setelah QR tampil, uji dengan <a class="font-bold underline" href="https://simulator.sandbox.midtrans.com/openapi/qris/index" target="_blank" rel="noopener">QRIS Simulator Midtrans</a>.</div><p class="mt-4 text-xs leading-5 text-muted">Anda akan diarahkan ke instruksi pembayaran aman sesuai metode yang dipilih.</p><div class="mt-6 grid grid-cols-2 gap-3"><button type="button" data-back-to-method class="min-h-12 rounded-xl border border-charcoal/10 px-4 text-sm font-bold text-charcoal transition hover:bg-cream">Kembali</button><button id="confirm-payment" type="button" class="min-h-12 rounded-xl bg-gold px-4 text-sm font-extrabold text-charcoal shadow-[0_8px_18px_rgba(201,162,75,.25)] transition hover:bg-[#dbb45d]">Ya, lanjutkan</button></div></div>
            </div>
        </div>
        <div id="payment-status-modal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-charcoal/70 p-4 backdrop-blur-[3px]" role="dialog" aria-modal="true" aria-labelledby="payment-status-title">
            <div class="w-full max-w-sm overflow-hidden rounded-[28px] bg-white p-6 text-center shadow-2xl"><span id="payment-status-icon" class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-brandsuccess/10 text-brandsuccess"><span class="material-symbols-outlined text-[34px]">check_circle</span></span><p id="payment-status-eyebrow" class="mt-5 text-[11px] font-bold uppercase tracking-[.15em] text-brandsuccess">Status pembayaran</p><h2 id="payment-status-title" class="mt-1 font-heading text-xl font-extrabold text-charcoal">Pembayaran diterima</h2><p id="payment-status-message" class="mt-3 text-sm leading-6 text-muted"></p><button id="payment-status-action" type="button" class="mt-6 inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-charcoal px-4 text-sm font-extrabold text-white transition hover:bg-charcoal/90">Cek status booking</button></div>
        </div>
    </div>
@endsection

@push('scripts')
    <style>.payment-checkout .payment-method{display:flex;min-height:88px;flex-direction:column;align-items:center;justify-content:center;gap:.45rem;border:1px solid rgba(28,28,30,.08);border-radius:1rem;background:#fff;color:#59595d;font-size:11px;font-weight:700;text-align:center}.payment-checkout .payment-method .material-symbols-outlined{color:#c9a24b;font-size:24px}.payment-checkout .method-choice{display:flex;width:100%;align-items:center;gap:.85rem;border:1px solid rgba(28,28,30,.09);border-radius:1rem;background:#fff;padding:.8rem;text-align:left;transition:transform .18s,background-color .18s,border-color .18s,box-shadow .18s}.payment-checkout .method-choice:hover{transform:translateY(-1px);border-color:#c9a24b;background:#fffcf5;box-shadow:0 8px 18px rgba(28,28,30,.06)}@media (min-width:640px){.payment-checkout .payment-method{min-height:94px;font-size:12px}}</style>
    @if($payment?->snap_token && $paymentDriver === 'snap')
        <script type="text/javascript" src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ $clientKey }}"></script>
    @endif
    <script>
        const paymentModal = document.getElementById('payment-modal');
        const confirmModal = document.getElementById('payment-confirm-modal');
        const statusModal = document.getElementById('payment-status-modal');
        const methodLabels = { qris: 'QRIS', bca: 'BCA Virtual Account', bni: 'BNI Virtual Account', bri: 'BRI Virtual Account', permata: 'Permata Virtual Account', gopay: 'GoPay', shopeepay: 'ShopeePay', mandiri_bill: 'Mandiri Bill' };
        const closeModal = (element) => { element.classList.add('hidden'); element.classList.remove('flex'); };
        const openModal = (element) => { element.classList.remove('hidden'); element.classList.add('flex'); };
        document.querySelectorAll('[data-close-payment]').forEach((button) => button.addEventListener('click', () => closeModal(paymentModal)));
        document.querySelectorAll('[data-close-confirm]').forEach((button) => button.addEventListener('click', () => closeModal(confirmModal)));
        document.querySelectorAll('[data-back-to-method]').forEach((button) => button.addEventListener('click', () => { closeModal(confirmModal); openModal(paymentModal); }));
        document.querySelectorAll('[data-method]').forEach((button) => button.addEventListener('click', () => {
            const method = button.dataset.method;
            document.getElementById('selected-method').value = method;
            document.getElementById('confirm-method-label').textContent = methodLabels[method];
            document.getElementById('qris-sandbox-note').classList.toggle('hidden', method !== 'qris');
            closeModal(paymentModal); openModal(confirmModal);
        }));
        document.getElementById('confirm-payment').addEventListener('click', () => document.getElementById('method-form').submit());
        const showPaymentStatus = (type, title, message) => {
            const success = type === 'success';
            const icon = document.getElementById('payment-status-icon');
            const eyebrow = document.getElementById('payment-status-eyebrow');
            icon.className = `mx-auto flex h-16 w-16 items-center justify-center rounded-full ${success ? 'bg-brandsuccess/10 text-brandsuccess' : 'bg-brandwarning/10 text-brandwarning'}`;
            icon.innerHTML = `<span class="material-symbols-outlined text-[34px]">${success ? 'check_circle' : 'schedule'}</span>`;
            eyebrow.className = `mt-5 text-[11px] font-bold uppercase tracking-[.15em] ${success ? 'text-brandsuccess' : 'text-brandwarning'}`;
            document.getElementById('payment-status-title').textContent = title;
            document.getElementById('payment-status-message').textContent = message;
            openModal(statusModal);
        };
        document.getElementById('payment-status-action').addEventListener('click', () => document.getElementById('check-status-form').submit());
        @if($payment?->snap_token && $paymentDriver === 'snap')
        document.getElementById('change-method')?.addEventListener('click', () => { paymentModal.classList.remove('hidden'); paymentModal.classList.add('flex'); });
        document.getElementById('pay-button').addEventListener('click', function () {
            const btn = this, label = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined text-[20px] animate-spin">progress_activity</span> Menyiapkan pembayaran...';
            snap.pay(@json($payment->snap_token), {
                onSuccess: () => showPaymentStatus('success', 'Pembayaran berhasil', 'Pembayaran telah diterima. Tekan tombol di bawah untuk menyinkronkan status booking.'),
                onPending: () => showPaymentStatus('pending', 'Menunggu pembayaran', 'Instruksi pembayaran sudah dibuat. Selesaikan pembayaran lalu cek status booking.'),
                onError: () => { document.getElementById('pay-error').textContent = 'Pembayaran belum dapat diproses. Silakan coba kembali dalam beberapa saat.'; document.getElementById('pay-error').classList.remove('hidden'); btn.disabled = false; btn.innerHTML = label; },
                onClose: () => { btn.disabled = false; btn.innerHTML = label; }
            });
        });
        @else
        const payButton = document.getElementById('pay-button');
        if (payButton) payButton.addEventListener('click', () => { paymentModal.classList.remove('hidden'); paymentModal.classList.add('flex'); });
        document.getElementById('retry-payment')?.addEventListener('click', () => { paymentModal.classList.remove('hidden'); paymentModal.classList.add('flex'); });
        paymentModal?.addEventListener('click', (event) => { if (event.target === paymentModal) { paymentModal.classList.add('hidden'); paymentModal.classList.remove('flex'); } });
        @endif
        const countdown = document.getElementById('payment-countdown');
        if (countdown) { let seconds = 24 * 60 * 60; setInterval(() => { seconds = Math.max(0, seconds - 1); const h = String(Math.floor(seconds / 3600)).padStart(2, '0'), m = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0'), s = String(seconds % 60).padStart(2, '0'); countdown.textContent = `${h}:${m}:${s}`; }, 1000); }
    </script>
@endpush
