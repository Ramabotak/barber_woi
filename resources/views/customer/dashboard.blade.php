@extends('layouts.customer')

@section('title', 'Beranda - Barber Woi')

@section('content')
    @php
        $statusLabels = [
            'pending' => 'Menunggu Persetujuan', 'accepted' => 'Siap Dibayar', 'paid' => 'Sudah Dibayar',
            'waiting' => 'Menunggu Giliran', 'late' => 'Terlambat', 'serving' => 'Sedang Dilayani',
            'completed' => 'Selesai', 'cancelled' => 'Dibatalkan',
        ];
    @endphp

    <section class="mb-6">
        <p class="mb-1 text-sm font-medium text-gold">BARBER WOI</p>
        <h1 class="font-heading text-2xl font-bold tracking-tight text-charcoal sm:text-3xl">Halo, {{ auth()->user()->name }} 👋</h1>
        <p class="mt-2 text-sm text-muted sm:text-base">Siap tampil lebih rapi hari ini?</p>
    </section>

    <section class="mb-8 rounded-2xl border border-gold/20 bg-white p-4 shadow-[0_6px_20px_rgba(28,28,30,0.05)] sm:p-5" aria-labelledby="search-title">
        <div class="mb-3 flex items-start gap-3">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gold/15 text-gold"><span class="material-symbols-outlined">search</span></span>
            <div>
                <h2 id="search-title" class="font-heading text-lg font-bold text-charcoal">Cari barber atau layanan</h2>
                <p class="mt-0.5 text-xs leading-5 text-muted">Coba cari nama barber, keahlian, atau jenis layanan yang Anda inginkan.</p>
            </div>
        </div>
        <form action="{{ route('customer.dashboard') }}" method="GET" class="flex flex-col gap-2 sm:flex-row">
            <label for="dashboard-search" class="sr-only">Kata kunci pencarian</label>
            <div class="relative flex-1">
                <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-muted">search</span>
                <input id="dashboard-search" name="search" type="search" value="{{ $search }}" list="search-suggestions" autocomplete="off" placeholder="Contoh: Fade, Haircut, atau nama barber" class="min-h-11 w-full rounded-lg border border-gray-200 bg-cream/40 py-3 pl-10 pr-4 text-sm text-charcoal outline-none transition placeholder:text-gray-400 focus:border-gold focus:ring-2 focus:ring-gold/20">
                <datalist id="search-suggestions">
                    @foreach($searchSuggestions as $suggestion)
                        <option value="{{ $suggestion }}"></option>
                    @endforeach
                </datalist>
            </div>
            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-charcoal px-5 py-3 text-sm font-bold text-white transition hover:bg-black">Cari</button>
            @if($search !== '')
                <a href="{{ route('customer.dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-gray-200 px-4 py-3 text-sm font-semibold text-muted transition hover:border-charcoal hover:text-charcoal">Reset</a>
            @endif
        </form>
        @if($searchSuggestions->isNotEmpty())
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <span class="text-xs font-semibold text-muted">Saran:</span>
                @foreach($searchSuggestions->take(5) as $suggestion)
                    <a href="{{ route('customer.dashboard', ['search' => $suggestion]) }}" class="rounded-full bg-cream px-3 py-1.5 text-xs font-medium text-charcoal transition hover:bg-gold/25">{{ $suggestion }}</a>
                @endforeach
            </div>
        @endif
    </section>

    @if($search !== '')
        <div class="mb-6 flex items-center gap-2 rounded-xl border border-gold/25 bg-gold/10 px-4 py-3 text-sm text-charcoal">
            <span class="material-symbols-outlined text-[19px] text-gold">manage_search</span>
            <p>Hasil pencarian untuk <span class="font-bold">“{{ $search }}”</span>: {{ $barbers->count() }} barber dan {{ $services->count() }} layanan ditemukan.</p>
        </div>
    @endif

    <section class="customer-dashboard-hero relative mb-8 flex min-h-[380px] items-center overflow-hidden rounded-xl bg-charcoal px-6 py-10 text-white sm:min-h-[420px] sm:px-9 sm:py-12">
        <img src="{{ asset('images/hero-barber-shop.jpg') }}" alt="Interior barbershop Barber Woi" class="absolute inset-0 h-full w-full object-cover object-center">
        <div class="absolute inset-y-0 left-0 w-[58%] bg-gradient-to-r from-charcoal/70 via-charcoal/35 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-black/10"></div>
        <div class="relative z-10 max-w-2xl">
            <span class="mb-4 inline-flex items-center gap-2 border border-gold/30 bg-gold/10 px-3 py-1 text-[10px] font-extrabold tracking-[0.16em] text-gold">RESERVATION DESK <span class="h-1 w-1 rounded-full bg-gold"></span> TANPA TEBAK-TEBAKAN</span>
            <h2 class="font-heading text-3xl font-extrabold leading-[1.05] tracking-[-0.04em] sm:text-4xl">Pilih waktu yang terasa tepat.</h2>
            <p class="mt-3 max-w-xl text-sm leading-6 text-cream/70 sm:text-base">Setiap slot dibuat untuk satu keputusan sederhana: barber yang Anda mau, pada waktu yang Anda pegang.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('customer.booking.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-gold px-5 py-3 text-sm font-bold text-charcoal transition-colors hover:bg-[#dbb45d]">Booking Sekarang <span class="material-symbols-outlined ml-2 text-[18px]">arrow_forward</span></a>
                <a href="#barber-pilihan" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-white/20 px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-white/10">Lihat Barber</a>
            </div>
        </div>
    </section>

    <div class="mb-12 grid grid-cols-1 gap-8 lg:grid-cols-12">
        <section class="lg:col-span-5">
            <div class="mb-4 flex items-center justify-between"><div><p class="text-[11px] font-bold uppercase tracking-[0.18em] text-gold">Perjalanan Anda</p><h2 class="mt-1 font-heading text-2xl font-bold tracking-tight text-charcoal">Booking Aktif</h2></div><span class="grid h-11 w-11 place-items-center rounded-xl bg-gold/15 text-gold"><span class="material-symbols-outlined text-[23px]">event_available</span></span></div>
            @if($activeBooking)
                <a href="{{ route('customer.booking.show', $activeBooking) }}" class="group relative block min-h-[285px] overflow-hidden rounded-2xl bg-charcoal p-6 text-white shadow-[0_16px_35px_rgba(28,28,30,0.14)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_22px_45px_rgba(28,28,30,0.22)] sm:p-7">
                    <span class="absolute -right-10 -top-10 h-40 w-40 rounded-full border border-gold/30"></span><span class="absolute -bottom-14 right-8 h-40 w-40 rounded-full bg-gold/10 blur-2xl"></span>
                    <div class="relative"><div class="mb-7 flex items-start justify-between gap-3"><div><p class="text-[11px] font-bold uppercase tracking-[0.16em] text-gold/80">{{ $activeBooking->booking_code }}</p><p class="mt-2 font-heading text-xl font-bold">{{ $activeBooking->service->service_name }}</p></div><span class="rounded-full bg-gold px-3 py-1.5 text-[10px] font-bold text-charcoal">{{ $statusLabels[$activeBooking->status] ?? ucfirst($activeBooking->status) }}</span></div>
                    <div class="flex items-center gap-3"><div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full border border-white/20 bg-white/10 text-sm font-bold text-gold">@if($activeBooking->barber->photo)<img src="{{ Storage::url($activeBooking->barber->photo) }}" alt="{{ $activeBooking->barber->user->name }}" class="h-full w-full object-cover">@else{{ strtoupper(substr($activeBooking->barber->user->name, 0, 1)) }}@endif</div><div><p class="text-sm font-bold">{{ $activeBooking->barber->user->name }}</p><p class="text-xs text-cream/60">Barber pilihan Anda</p></div></div>
                    <div class="mt-6 grid grid-cols-[1fr_auto] gap-4 border-t border-white/10 pt-5"><div><p class="text-[10px] font-bold uppercase tracking-wider text-cream/50">Jadwal</p><p class="mt-1 text-sm font-semibold">{{ $activeBooking->schedule?->date?->translatedFormat('D, d M') ?? '-' }}{{ $activeBooking->slot_time ? ' · '.$activeBooking->slot_time->format('H:i').' WIB' : '' }}</p></div><div class="border-l border-white/10 pl-4"><p class="text-[10px] font-bold uppercase tracking-wider text-cream/50">Antrean</p><p class="mt-0.5 font-heading text-2xl font-bold text-gold">#{{ str_pad($activeBooking->queue_number, 2, '0', STR_PAD_LEFT) }}</p></div></div>
                    <span class="mt-6 inline-flex items-center text-xs font-bold text-gold">Lihat Detail Booking <span class="material-symbols-outlined ml-1 text-[17px] transition-transform group-hover:translate-x-1">arrow_forward</span></span></div>
                </a>
            @else
                <div class="relative flex min-h-[285px] flex-col justify-center overflow-hidden rounded-2xl border border-gold/25 bg-gradient-to-br from-white via-white to-[#f1e4c9] p-7 text-center shadow-[0_12px_30px_rgba(28,28,30,0.06)]"><span class="absolute -right-8 -top-8 h-36 w-36 rounded-full border border-gold/20"></span><span class="absolute -bottom-12 -left-10 h-36 w-36 rounded-full bg-gold/10 blur-2xl"></span><div class="relative"><span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-charcoal text-gold shadow-lg"><span class="material-symbols-outlined text-[32px]">calendar_add_on</span></span><p class="mt-5 font-heading text-xl font-bold text-charcoal">Belum ada booking aktif</p><p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-muted">Saatnya pilih gaya baru dan amankan slot bersama barber favoritmu.</p><a href="{{ route('customer.booking.create') }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-lg bg-charcoal px-5 py-3 text-sm font-bold text-white shadow-lg transition hover:bg-black">Booking Sekarang <span class="material-symbols-outlined ml-2 text-[18px] text-gold">arrow_forward</span></a></div></div>
            @endif
        </section>

        <div class="lg:col-span-7">
            <section id="barber-pilihan">
                <div class="mb-4 flex items-end justify-between"><div><p class="text-[11px] font-bold uppercase tracking-[0.18em] text-gold">Pilih yang terbaik</p><h2 class="mt-1 font-heading text-2xl font-bold tracking-tight text-charcoal">Barber Pilihan</h2></div><a href="{{ route('customer.booking.create') }}" class="inline-flex items-center text-sm font-bold text-gold hover:text-charcoal">Lihat Semua <span class="material-symbols-outlined ml-1 text-[18px]">arrow_forward</span></a></div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @forelse($barbers->take(4) as $barber)
                        <a href="{{ route('customer.booking.create') }}" class="group overflow-hidden rounded-2xl border border-gray-200 bg-white p-2.5 shadow-[0_6px_20px_rgba(28,28,30,0.05)] transition duration-300 hover:-translate-y-1 hover:border-gold/70 hover:shadow-[0_16px_28px_rgba(28,28,30,0.12)]"><div class="relative aspect-[4/3] overflow-hidden rounded-xl bg-cream">@if($barber->photo)<img src="{{ Storage::url($barber->photo) }}" alt="{{ $barber->user->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">@else<div class="flex h-full w-full items-center justify-center bg-charcoal text-4xl font-bold text-gold">{{ strtoupper(substr($barber->user->name, 0, 1)) }}</div>@endif<div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/45 to-transparent"></div><span class="absolute right-3 top-3 inline-flex items-center rounded-full bg-white/95 px-2.5 py-1.5 text-[11px] font-bold text-charcoal shadow-sm"><span class="material-symbols-outlined mr-1 text-[15px] text-gold" style="font-variation-settings: 'FILL' 1">star</span>{{ $barber->reviews_avg_rating ? number_format($barber->reviews_avg_rating, 1) : '-' }}</span></div><div class="p-3 pb-2"><div class="flex items-start justify-between gap-2"><div class="min-w-0"><p class="truncate font-heading text-base font-bold text-charcoal">{{ $barber->user->name }}</p><p class="mt-1 truncate text-xs text-muted">{{ $barber->experience ?: 'Barber profesional' }}</p></div><span class="material-symbols-outlined mt-0.5 text-gold transition-transform group-hover:translate-x-1">arrow_forward</span></div><p class="mt-4 inline-flex items-center text-[11px] font-bold {{ $barber->schedules->isNotEmpty() ? 'text-brandsuccess' : 'text-muted' }}"><span class="mr-1.5 h-1.5 w-1.5 rounded-full {{ $barber->schedules->isNotEmpty() ? 'bg-brandsuccess' : 'bg-muted' }}"></span>{{ $barber->schedules->isNotEmpty() ? 'Tersedia '. $barber->schedules->first()->date->translatedFormat('D') : 'Jadwal menyusul' }}</p></div></a>
                    @empty
                        <p class="col-span-full rounded-xl border border-dashed border-gray-300 bg-white py-7 text-center text-sm text-muted">Belum ada barber aktif saat ini.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    {{-- Layanan --}}
    <section class="mb-12">
        <div class="mb-5 flex items-end justify-between"><div><p class="text-[11px] font-bold uppercase tracking-[0.18em] text-gold">Temukan gaya Anda</p><h2 class="mt-1 font-heading text-2xl font-bold tracking-tight text-charcoal">Layanan Populer</h2></div><a href="{{ route('customer.booking.create') }}" class="inline-flex items-center text-sm font-bold text-gold hover:text-charcoal">Pilih Layanan <span class="material-symbols-outlined ml-1 text-[18px]">arrow_forward</span></a></div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @forelse($services as $service)
            <a href="{{ route('customer.booking.create') }}" class="group overflow-hidden rounded-2xl border border-gray-200 bg-white p-3 shadow-[0_6px_20px_rgba(28,28,30,0.05)] transition duration-300 hover:-translate-y-1 hover:border-gold/70 hover:shadow-[0_16px_30px_rgba(28,28,30,0.12)]">
                <div class="relative aspect-[16/10] overflow-hidden rounded-xl bg-cream">
                    @if($service->photo)
                        <img src="{{ Storage::url($service->photo) }}" alt="{{ $service->service_name }}"
                             class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                    @else
                        <span class="flex h-full w-full items-center justify-center text-xs text-gray-400">Tidak ada foto</span>
                    @endif
                    <span class="absolute bottom-3 left-3 rounded-full bg-charcoal/90 px-2.5 py-1.5 text-[11px] font-bold text-gold backdrop-blur-sm">{{ $service->duration }} menit</span>
                </div>
                <div class="px-1 pb-1 pt-4">
                    <p class="truncate font-heading text-base font-bold text-charcoal">{{ $service->service_name }}</p>
                    <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-3"><p class="text-xs font-medium text-muted">Mulai dari</p><p class="font-heading text-base font-bold text-gold">Rp {{ number_format($service->price, 0, ',', '.') }}</p></div>
                </div>
            </a>
        @empty
            <p class="col-span-full text-gray-400 text-sm">Belum ada layanan tersedia.</p>
        @endforelse
    </div>
    </section>

    <section class="grid grid-cols-1 gap-4 border-t border-gray-200 pt-8 sm:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-[0_5px_18px_rgba(28,28,30,0.04)]"><span class="flex h-12 w-12 items-center justify-center rounded-xl bg-charcoal text-gold"><span class="material-symbols-outlined">content_cut</span></span><h3 class="mt-5 font-heading text-base font-bold text-charcoal">Barber Profesional</h3><p class="mt-2 text-sm leading-6 text-muted">Barber berpengalaman untuk hasil yang rapi dan sesuai gaya Anda.</p></div>
        <div class="rounded-2xl bg-white p-5 shadow-[0_5px_18px_rgba(28,28,30,0.04)]"><span class="flex h-12 w-12 items-center justify-center rounded-xl bg-charcoal text-gold"><span class="material-symbols-outlined">event_available</span></span><h3 class="mt-5 font-heading text-base font-bold text-charcoal">Jadwal Fleksibel</h3><p class="mt-2 text-sm leading-6 text-muted">Pilih waktu yang sesuai tanpa perlu mengantre terlalu lama.</p></div>
        <div class="rounded-2xl bg-white p-5 shadow-[0_5px_18px_rgba(28,28,30,0.04)]"><span class="flex h-12 w-12 items-center justify-center rounded-xl bg-charcoal text-gold"><span class="material-symbols-outlined">verified_user</span></span><h3 class="mt-5 font-heading text-base font-bold text-charcoal">Pembayaran Aman</h3><p class="mt-2 text-sm leading-6 text-muted">Pembayaran yang praktis dan aman untuk setiap booking Anda.</p></div>
    </section>
@endsection
