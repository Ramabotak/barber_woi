@extends('layouts.customer')

@section('title', 'Detail Booking - Barber Woi')

@section('content')
    @php
        $statusLabels = [
            'pending' => 'Menunggu Pembayaran', 'paid' => 'Sudah Dibayar', 'accepted' => 'Diterima',
            'waiting' => 'Menunggu Giliran', 'late' => 'Terlambat', 'serving' => 'Sedang Dilayani',
            'completed' => 'Selesai', 'cancelled' => 'Dibatalkan',
        ];
        $currentStep = ['pending' => 1, 'paid' => 2, 'accepted' => 3, 'waiting' => 3, 'late' => 3, 'serving' => 4, 'completed' => 5][$booking->status] ?? 0;
        $steps = [
            ['label' => 'Dibuat', 'icon' => 'check'], ['label' => 'Pembayaran', 'icon' => 'payments'],
            ['label' => 'Diterima', 'icon' => 'event_available'], ['label' => 'Dilayani', 'icon' => 'content_cut'],
            ['label' => 'Selesai', 'icon' => 'flag'],
        ];
        $amount = $booking->payment?->amount ?? $booking->service->price;
    @endphp

    <div class="mb-7 flex flex-wrap items-center gap-3">
        <a href="{{ route('customer.booking.index') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-muted transition hover:border-gold hover:text-charcoal" aria-label="Kembali ke Booking Saya"><span class="material-symbols-outlined">arrow_back</span></a>
        <div><p class="text-[11px] font-bold uppercase tracking-wider text-gold">Booking Anda</p><h1 class="font-heading text-2xl font-bold tracking-tight text-charcoal">Detail Booking</h1></div>
        <span class="ml-auto rounded-full border border-gray-200 bg-white px-3 py-1.5 text-[11px] font-bold text-muted">{{ $booking->booking_code }}</span>
    </div>

    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-xl border border-brandsuccess/20 bg-brandsuccess/10 px-4 py-3 text-sm text-brandsuccess"><span class="material-symbols-outlined">task_alt</span>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-5 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-branddanger"><span class="material-symbols-outlined">error</span>{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-branddanger">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
    @endif

    @if($booking->status === 'cancelled')
        <div class="mb-6 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 p-4"><span class="material-symbols-outlined text-branddanger">cancel</span><div><p class="text-sm font-bold text-branddanger">Booking dibatalkan</p><p class="mt-0.5 text-xs text-red-700">Booking ini tidak lagi memiliki antrean aktif.</p></div></div>
    @else
        <section class="mb-7 overflow-hidden rounded-xl border border-gray-200 bg-white p-4 sm:p-6">
            <div class="mb-4 flex items-center justify-between"><h2 class="font-heading text-sm font-bold text-charcoal">Status Booking</h2><span class="rounded-full px-3 py-1 text-[10px] font-bold {{ $booking->status === 'completed' ? 'bg-gray-100 text-gray-600' : ($booking->status === 'paid' ? 'bg-brandsuccess/10 text-brandsuccess' : ($booking->status === 'serving' ? 'bg-charcoal text-white' : 'bg-gold/15 text-brandwarning')) }}">{{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}</span></div>
            <div class="relative grid grid-cols-5 gap-1">
                <div class="absolute left-[8%] right-[8%] top-4 h-px bg-gray-200"></div>
                <div class="absolute left-[8%] top-4 h-px bg-charcoal transition-all" style="width: {{ max(0, min(84, $currentStep * 21)) }}%"></div>
                @foreach($steps as $index => $step)
                    @php $state = $index < $currentStep ? 'done' : ($index === $currentStep ? 'current' : 'next'); @endphp
                    <div class="relative z-10 flex flex-col items-center text-center"><span @class(['flex h-8 w-8 items-center justify-center rounded-full border-2 text-[15px]', 'border-charcoal bg-charcoal text-white' => $state === 'done', 'border-gold bg-white text-gold' => $state === 'current', 'border-gray-200 bg-white text-muted' => $state === 'next'])><span class="material-symbols-outlined text-[15px]">{{ $state === 'done' ? 'check' : $step['icon'] }}</span></span><span @class(['mt-2 text-[9px] font-semibold leading-3 sm:text-[10px]', 'text-charcoal' => $state !== 'next', 'text-muted' => $state === 'next'])>{{ $step['label'] }}</span></div>
                @endforeach
            </div>
        </section>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white"><div class="border-b border-gray-100 px-5 py-4"><h2 class="font-heading text-base font-bold text-charcoal">Detail Layanan</h2></div><div class="grid grid-cols-1 gap-x-8 gap-y-5 p-5 sm:grid-cols-2">
                <div><p class="text-[10px] font-bold uppercase tracking-wider text-muted">Layanan</p><p class="mt-1 text-sm font-semibold text-charcoal">{{ $booking->service->service_name }}</p><p class="mt-1 text-xs text-muted">Estimasi {{ $booking->service->duration }} menit</p></div>
                <div><p class="text-[10px] font-bold uppercase tracking-wider text-muted">Barber</p><div class="mt-1 flex items-center gap-2.5"><span class="flex h-8 w-8 items-center justify-center overflow-hidden rounded-full bg-charcoal text-xs font-bold text-gold">@if($booking->barber->photo)<img src="{{ Storage::url($booking->barber->photo) }}" alt="{{ $booking->barber->user->name }}" class="h-full w-full object-cover">@else{{ strtoupper(substr($booking->barber->user->name, 0, 1)) }}@endif</span><p class="text-sm font-semibold text-charcoal">{{ $booking->barber->user->name }}</p></div></div>
                <div><p class="text-[10px] font-bold uppercase tracking-wider text-muted">Tanggal</p><p class="mt-1 flex items-center gap-1.5 text-sm font-medium text-charcoal"><span class="material-symbols-outlined text-[17px] text-gold">calendar_today</span>{{ $booking->schedule?->date?->translatedFormat('l, d F Y') ?? '-' }}</p></div>
                <div><p class="text-[10px] font-bold uppercase tracking-wider text-muted">Waktu & Antrean</p><p class="mt-1 flex items-center gap-1.5 text-sm font-medium text-charcoal"><span class="material-symbols-outlined text-[17px] text-gold">schedule</span>{{ $booking->slot_time?->format('H:i') ?? '-' }} WIB <span class="text-muted">·</span> #{{ str_pad($booking->queue_number, 2, '0', STR_PAD_LEFT) }}</p></div>
            </div></section>
            <section class="rounded-xl border border-gray-200 bg-white p-5"><h2 class="border-b border-gray-100 pb-4 font-heading text-base font-bold text-charcoal">Rincian Pembayaran</h2><div class="space-y-3 py-4 text-sm"><div class="flex justify-between gap-4"><span class="text-muted">{{ $booking->service->service_name }}</span><span class="font-medium text-charcoal">Rp {{ number_format($booking->service->price, 0, ',', '.') }}</span></div></div><div class="flex items-center justify-between border-t border-gray-100 pt-4"><span class="text-sm font-bold text-charcoal">Total Pembayaran</span><span class="font-heading text-xl font-bold text-charcoal">Rp {{ number_format($amount, 0, ',', '.') }}</span></div></section>
        </div>

        <aside class="space-y-5">
            @if(!in_array($booking->status, ['completed', 'cancelled']))
                <section class="rounded-xl border border-gray-200 bg-white p-5">
                    @if($booking->payment && $booking->payment->status === 'paid')
                        <div class="flex items-center gap-2 text-brandsuccess"><span class="material-symbols-outlined">task_alt</span><span class="text-sm font-bold">Pembayaran Berhasil</span></div><p class="mt-3 text-xs leading-5 text-muted">Pembayaran telah diterima. Booking Anda akan diproses oleh barber.</p>@if($booking->payment->paid_at)<p class="mt-3 rounded-lg bg-brandsuccess/10 px-3 py-2 text-xs font-medium text-brandsuccess">Dibayar {{ $booking->payment->paid_at->translatedFormat('d M Y, H:i') }} WIB</p>@endif
                    @else
                        <div class="flex items-center gap-2 text-brandwarning"><span class="material-symbols-outlined">info</span><span class="text-sm font-bold">Menunggu Pembayaran</span></div><p class="mt-3 text-xs leading-5 text-muted">Selesaikan pembayaran agar slot antrean Anda dapat diproses oleh barber.</p><div class="mt-4 rounded-lg border border-gold/20 bg-gold/10 px-3 py-2.5"><p class="text-[10px] font-bold uppercase tracking-wider text-brandwarning">Total yang harus dibayar</p><p class="mt-1 text-sm font-bold text-charcoal">Rp {{ number_format($amount, 0, ',', '.') }}</p></div><div class="mt-4 grid gap-2"><a href="{{ route('customer.payment.show', $booking) }}" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-gold px-4 py-3 text-sm font-bold text-charcoal transition hover:bg-[#dbb45d]"><span class="material-symbols-outlined mr-2 text-[19px]">payments</span>Bayar Sekarang</a>@if($booking->payment)<form action="{{ route('customer.payment.check', $booking) }}" method="POST">@csrf<button type="submit" class="inline-flex w-full min-h-10 items-center justify-center rounded-lg border border-gray-200 px-4 py-2.5 text-xs font-bold text-charcoal transition hover:bg-cream">Cek Status Pembayaran</button></form>@endif</div>
                    @endif
                </section>
            @endif

            @if($booking->status === 'completed')
                <section class="rounded-xl border border-gray-200 bg-white p-5"><div class="flex items-center gap-2 text-brandsuccess"><span class="material-symbols-outlined">task_alt</span><span class="text-sm font-bold">Booking Selesai</span></div><h2 class="mt-4 font-heading text-base font-bold text-charcoal">{{ $booking->review ? 'Ulasan Anda' : 'Berikan Ulasan' }}</h2>
                    @if($booking->review)
                        <div class="mt-3 flex text-gold">@for($i = 1; $i <= 5; $i++)<span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' {{ $i <= $booking->review->rating ? 1 : 0 }}">star</span>@endfor</div><p class="mt-3 text-sm leading-6 text-muted">{{ $booking->review->comment ?: 'Terima kasih sudah memberi rating.' }}</p><a href="{{ route('customer.reviews.index') }}" class="mt-4 inline-flex text-xs font-bold text-gold hover:underline">Lihat / Edit Ulasan</a>
                    @else
                        <p class="mt-1 text-xs leading-5 text-muted">Bagaimana pengalaman Anda bersama {{ $booking->barber->user->name }}?</p><form action="{{ route('customer.booking.review', $booking) }}" method="POST" class="mt-4 space-y-3" x-data="{ rating: 0 }">@csrf<input type="hidden" name="rating" x-model="rating" required><div class="flex gap-1">@for($i = 1; $i <= 5; $i++)<button type="button" @click="rating = {{ $i }}" class="text-gold"><span class="material-symbols-outlined text-[28px]" :style="rating >= {{ $i }} ? &quot;font-variation-settings: 'FILL' 1&quot; : &quot;font-variation-settings: 'FILL' 0&quot;">star</span></button>@endfor</div><textarea name="comment" rows="3" maxlength="1000" class="w-full rounded-lg border-gray-200 bg-cream px-3 py-2 text-sm text-charcoal placeholder:text-muted focus:border-gold focus:ring-gold" placeholder="Bagaimana pengalaman Anda?"></textarea><button type="submit" class="w-full rounded-lg bg-charcoal px-4 py-2.5 text-xs font-bold text-white transition hover:bg-charcoal/90">Kirim Ulasan</button></form>
                    @endif
                </section>
            @endif

            <section class="flex items-start gap-3 rounded-xl border border-gray-200 bg-white p-5"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-cream text-gold"><span class="material-symbols-outlined text-[20px]">support_agent</span></span><div><h2 class="text-sm font-bold text-charcoal">Butuh Bantuan?</h2><p class="mt-1 text-xs leading-5 text-muted">Hubungi customer service kami jika ada kendala dengan booking Anda.</p></div></section>
        </aside>
    </div>
@endsection
