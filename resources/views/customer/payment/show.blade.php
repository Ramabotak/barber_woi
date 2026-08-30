@extends('layouts.customer')

@section('title', 'Checkout Booking - Barber Woi')

@section('content')
    @php
        $amount = $payment?->amount ?? $booking->service->price;
        $paymentData = $payment?->payment_data ?? [];
        $vaNumber = data_get($paymentData, 'va_numbers.0.va_number') ?? data_get($paymentData, 'permata_va_number');
        $qrAction = collect($paymentData['actions'] ?? [])->firstWhere('name', 'generate-qr-code');
        $qrUrl = $qrAction['url'] ?? null;
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
                @if($payment && $payment->payment_data)
                    <form method="POST" action="{{ route('customer.payment.check', $booking) }}" class="mt-6">@csrf<button type="submit" class="inline-flex min-h-14 w-full items-center justify-center gap-2 rounded-2xl bg-charcoal px-5 py-3.5 text-sm font-extrabold text-white transition hover:bg-charcoal/90"><span class="material-symbols-outlined text-[20px]">sync</span>Cek Status Pembayaran</button></form>
                @elseif($payment && $payment->snap_token)
                    <button id="pay-button" type="button" class="mt-6 inline-flex min-h-14 w-full items-center justify-center gap-2 rounded-2xl bg-gold px-5 py-3.5 text-sm font-extrabold text-charcoal shadow-[0_8px_18px_rgba(201,162,75,.28)] transition hover:bg-[#dbb45d] focus:outline-none focus:ring-4 focus:ring-gold/30 disabled:cursor-wait disabled:opacity-70"><span class="material-symbols-outlined text-[20px]">lock</span>Lanjutkan Pembayaran<span class="material-symbols-outlined text-[18px]">arrow_forward</span></button>
                @else
                    <button id="pay-button" type="button" class="mt-6 inline-flex min-h-14 w-full items-center justify-center gap-2 rounded-2xl bg-gold px-5 py-3.5 text-sm font-extrabold text-charcoal shadow-[0_8px_18px_rgba(201,162,75,.28)] transition hover:bg-[#dbb45d] focus:outline-none focus:ring-4 focus:ring-gold/30"><span class="material-symbols-outlined text-[20px]">lock</span>Pilih Metode Bayar<span class="material-symbols-outlined text-[18px]">arrow_forward</span></button>
                @endif
                <div class="mt-4 flex gap-2.5 rounded-xl bg-white/70 p-3 text-[11px] leading-4 text-muted"><span class="material-symbols-outlined shrink-0 text-[17px] text-brandsuccess">verified_user</span><p>Data pembayaran diproses secara terenkripsi melalui Midtrans, penyedia pembayaran resmi kami.</p></div>
            </aside>
        </div>
        <div id="pay-error" role="alert" class="mt-5 hidden rounded-2xl border border-branddanger/20 bg-red-50 px-4 py-3 text-sm font-medium text-branddanger"></div>
        <form id="check-status-form" action="{{ route('customer.payment.check', $booking) }}" method="POST" class="hidden">@csrf</form>
        @if($payment && $payment->payment_data)
            <section class="mt-6 rounded-[24px] border border-brandsuccess/20 bg-white p-5 shadow-sm sm:p-7">
                <div class="flex items-center gap-3"><span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brandsuccess/10 text-brandsuccess"><span class="material-symbols-outlined">payments</span></span><div><p class="text-[11px] font-bold uppercase tracking-wider text-brandsuccess">Instruksi pembayaran</p><h2 class="font-heading text-lg font-extrabold text-charcoal">{{ $paymentData['method_label'] ?? 'Pembayaran' }}</h2></div></div>
                @if($vaNumber)<div class="mt-5 rounded-2xl bg-cream p-4"><p class="text-xs text-muted">Nomor Virtual Account</p><p class="mt-1 break-all font-heading text-2xl font-extrabold tracking-wider text-charcoal">{{ $vaNumber }}</p><p class="mt-2 text-xs text-muted">Transfer tepat sejumlah total tagihan. Status akan diperbarui otomatis setelah pembayaran diterima.</p></div>@endif
                @if($qrUrl)<div class="mt-5 flex flex-col items-center rounded-2xl bg-cream p-5 text-center"><p class="mb-3 text-sm font-bold text-charcoal">Scan QRIS untuk membayar</p><img src="{{ $qrUrl }}" alt="Kode QRIS pembayaran" class="h-56 w-56 rounded-xl bg-white p-2 shadow-sm"><p class="mt-3 text-xs text-muted">Buka aplikasi pembayaran Anda, lalu pindai kode QR di atas.</p></div>@endif
            </section>
        @endif

        <div id="payment-modal" class="fixed inset-0 z-[60] hidden items-end justify-center bg-charcoal/60 p-0 backdrop-blur-sm sm:items-center sm:p-4" role="dialog" aria-modal="true" aria-labelledby="payment-modal-title">
            <div class="w-full max-w-lg rounded-t-[28px] bg-white p-5 shadow-2xl sm:rounded-[28px] sm:p-7">
                <div class="flex items-start justify-between"><div><p class="text-[11px] font-bold uppercase tracking-[.14em] text-gold">Barber Woi</p><h2 id="payment-modal-title" class="mt-1 font-heading text-xl font-extrabold text-charcoal">Pilih metode pembayaran</h2><p class="mt-1 text-sm text-muted">Total Rp {{ number_format($amount, 0, ',', '.') }}</p></div><button type="button" data-close-payment class="rounded-xl p-2 text-muted hover:bg-cream" aria-label="Tutup"><span class="material-symbols-outlined">close</span></button></div>
                <form id="method-form" method="POST" action="{{ route('customer.payment.method', $booking) }}" class="mt-6 space-y-2.5">@csrf<input id="selected-method" type="hidden" name="method"><button type="button" data-method="qris" class="method-choice"><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gold/15 text-gold"><span class="material-symbols-outlined">qr_code_2</span></span><span class="flex-1 text-left"><span class="block font-bold">QRIS</span><span class="mt-0.5 block text-xs font-normal text-muted">Bayar dari aplikasi e-wallet atau mobile banking</span></span><span class="material-symbols-outlined text-muted">chevron_right</span></button><p class="px-1 pt-3 text-[11px] font-bold uppercase tracking-wider text-muted">Virtual Account</p>@foreach(['bca' => 'BCA Virtual Account', 'bni' => 'BNI Virtual Account', 'bri' => 'BRI Virtual Account', 'permata' => 'Permata Virtual Account'] as $key => $label)<button type="button" data-method="{{ $key }}" class="method-choice"><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-charcoal text-xs font-extrabold text-gold">{{ strtoupper($key) }}</span><span class="flex-1 text-left font-bold">{{ $label }}</span><span class="material-symbols-outlined text-muted">chevron_right</span></button>@endforeach</form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <style>.payment-checkout .payment-method{display:flex;min-height:88px;flex-direction:column;align-items:center;justify-content:center;gap:.45rem;border:1px solid rgba(28,28,30,.08);border-radius:1rem;background:#fff;color:#59595d;font-size:11px;font-weight:700;text-align:center}.payment-checkout .payment-method .material-symbols-outlined{color:#c9a24b;font-size:24px}.payment-checkout .method-choice{display:flex;width:100%;align-items:center;gap:.85rem;border:1px solid rgba(28,28,30,.09);border-radius:1rem;padding:.8rem;text-align:left;transition:background-color .18s,border-color .18s}.payment-checkout .method-choice:hover{border-color:#c9a24b;background:#fffcf5}@media (min-width:640px){.payment-checkout .payment-method{min-height:94px;font-size:12px}}</style>
    @if($payment?->snap_token)
        <script type="text/javascript" src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ $clientKey }}"></script>
    @endif
    <script>
        @if($payment?->snap_token)
        document.getElementById('pay-button').addEventListener('click', function () {
            const btn = this, label = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined text-[20px] animate-spin">progress_activity</span> Menyiapkan pembayaran...';
            snap.pay(@json($payment->snap_token), {
                onSuccess: () => document.getElementById('check-status-form').submit(),
                onPending: () => document.getElementById('check-status-form').submit(),
                onError: () => { document.getElementById('pay-error').textContent = 'Pembayaran belum dapat diproses. Silakan coba kembali dalam beberapa saat.'; document.getElementById('pay-error').classList.remove('hidden'); btn.disabled = false; btn.innerHTML = label; },
                onClose: () => { btn.disabled = false; btn.innerHTML = label; }
            });
        });
        @else
        const paymentModal = document.getElementById('payment-modal');
        const payButton = document.getElementById('pay-button');
        if (payButton) payButton.addEventListener('click', () => { paymentModal.classList.remove('hidden'); paymentModal.classList.add('flex'); });
        document.querySelectorAll('[data-close-payment]').forEach((button) => button.addEventListener('click', () => { paymentModal.classList.add('hidden'); paymentModal.classList.remove('flex'); }));
        paymentModal?.addEventListener('click', (event) => { if (event.target === paymentModal) { paymentModal.classList.add('hidden'); paymentModal.classList.remove('flex'); } });
        document.querySelectorAll('[data-method]').forEach((button) => button.addEventListener('click', () => { document.getElementById('selected-method').value = button.dataset.method; document.getElementById('method-form').submit(); }));
        @endif
    </script>
@endpush
