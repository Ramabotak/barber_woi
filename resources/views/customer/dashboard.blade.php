@extends('layouts.customer')

@section('title', 'Beranda - Barber Woi')

@section('content')
    @php
        $statusLabels = [
            'pending' => 'Menunggu Pembayaran', 'paid' => 'Sudah Dibayar', 'accepted' => 'Diterima',
            'waiting' => 'Menunggu Giliran', 'late' => 'Terlambat', 'serving' => 'Sedang Dilayani',
            'completed' => 'Selesai', 'cancelled' => 'Dibatalkan',
        ];
    @endphp

    <section class="mb-6">
        <p class="mb-1 text-sm font-medium text-gold">BARBER WOI</p>
        <h1 class="font-heading text-2xl font-bold tracking-tight text-charcoal sm:text-3xl">Halo, {{ auth()->user()->name }} 👋</h1>
        <p class="mt-2 text-sm text-muted sm:text-base">Siap tampil lebih rapi hari ini?</p>
    </section>

    <section class="relative mb-8 overflow-hidden rounded-xl bg-charcoal px-6 py-7 text-white sm:px-9 sm:py-10">
        <div class="absolute -right-12 -top-16 h-56 w-56 rounded-full border border-gold/20 bg-gold/5"></div>
        <div class="absolute bottom-0 right-20 h-32 w-32 rounded-full bg-gold/10 blur-2xl"></div>
        <div class="relative max-w-2xl">
            <span class="mb-4 inline-flex items-center gap-2 rounded-full border border-gold/30 bg-gold/10 px-3 py-1 text-[11px] font-bold tracking-wider text-gold">PREMIUM GROOMING <span class="h-1 w-1 rounded-full bg-gold"></span> MUDAH & CEPAT</span>
            <h2 class="font-heading text-3xl font-bold leading-tight tracking-tight sm:text-4xl">Waktunya tampil lebih percaya diri.</h2>
            <p class="mt-3 max-w-xl text-sm leading-6 text-cream/70 sm:text-base">Pilih barber favoritmu, tentukan jadwal, dan booking dalam hitungan menit.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('customer.booking.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-gold px-5 py-3 text-sm font-bold text-charcoal transition-colors hover:bg-[#dbb45d]">Booking Sekarang <span class="material-symbols-outlined ml-2 text-[18px]">arrow_forward</span></a>
                <a href="#barber-pilihan" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-white/20 px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-white/10">Lihat Barber</a>
            </div>
        </div>
        <span class="material-symbols-outlined absolute bottom-[-40px] right-3 text-[190px] text-gold/10 sm:right-10">content_cut</span>
    </section>

    <div class="mb-9 grid grid-cols-1 gap-7 lg:grid-cols-12">
        <section class="lg:col-span-4">
            <div class="mb-3 flex items-center justify-between"><h2 class="font-heading text-lg font-bold text-charcoal">Booking Aktif</h2><span class="material-symbols-outlined text-gold">event_available</span></div>
            @if($activeBooking)
                <a href="{{ route('customer.booking.show', $activeBooking) }}" class="block overflow-hidden rounded-xl border border-gray-200 bg-white p-5 shadow-[0_1px_2px_rgba(28,28,30,0.04)] transition hover:border-gold/60 hover:shadow-md">
                    <div class="mb-5 flex items-start justify-between gap-3"><div><p class="text-[11px] font-bold uppercase tracking-wider text-muted">{{ $activeBooking->booking_code }}</p><p class="mt-1 text-sm font-semibold text-charcoal">{{ $activeBooking->service->service_name }}</p></div><span class="rounded-md bg-gold/15 px-2.5 py-1 text-[10px] font-bold text-brandwarning">{{ $statusLabels[$activeBooking->status] ?? ucfirst($activeBooking->status) }}</span></div>
                    <div class="flex items-center gap-3"><div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full bg-charcoal text-sm font-bold text-gold">@if($activeBooking->barber->photo)<img src="{{ Storage::url($activeBooking->barber->photo) }}" alt="{{ $activeBooking->barber->user->name }}" class="h-full w-full object-cover">@else{{ strtoupper(substr($activeBooking->barber->user->name, 0, 1)) }}@endif</div><div><p class="text-sm font-bold text-charcoal">{{ $activeBooking->barber->user->name }}</p><p class="text-xs text-muted">Barber pilihan Anda</p></div></div>
                    <div class="mt-5 grid grid-cols-[1fr_auto] gap-3 rounded-lg border border-gray-100 bg-cream p-3"><div><p class="text-[10px] font-bold uppercase tracking-wider text-muted">Jadwal</p><p class="mt-1 text-xs font-semibold text-charcoal">{{ $activeBooking->schedule?->date?->translatedFormat('D, d M') ?? '-' }}{{ $activeBooking->slot_time ? ' · '.$activeBooking->slot_time->format('H:i').' WIB' : '' }}</p></div><div class="border-l border-gray-200 pl-3"><p class="text-[10px] font-bold uppercase tracking-wider text-muted">Antrean</p><p class="mt-0.5 font-heading text-xl font-bold text-charcoal">#{{ str_pad($activeBooking->queue_number, 2, '0', STR_PAD_LEFT) }}</p></div></div>
                    <span class="mt-4 inline-flex items-center text-xs font-bold text-charcoal">Lihat Detail <span class="material-symbols-outlined ml-1 text-[16px] text-gold">arrow_forward</span></span>
                </a>
            @else
                <div class="rounded-xl border border-dashed border-gold/40 bg-white p-6 text-center"><span class="material-symbols-outlined text-3xl text-gold">calendar_add_on</span><p class="mt-3 font-heading text-sm font-bold text-charcoal">Belum ada booking aktif</p><p class="mt-1 text-xs leading-5 text-muted">Temukan waktu terbaik bersama barber favoritmu.</p><a href="{{ route('customer.booking.create') }}" class="mt-4 inline-flex text-xs font-bold text-gold hover:underline">Booking Sekarang</a></div>
            @endif
        </section>

        <div class="space-y-8 lg:col-span-8">
            <section id="barber-pilihan">
                <div class="mb-3 flex items-center justify-between"><h2 class="font-heading text-lg font-bold text-charcoal">Barber Pilihan</h2><a href="{{ route('customer.booking.create') }}" class="inline-flex items-center text-xs font-bold text-gold hover:underline">Lihat Semua <span class="material-symbols-outlined ml-1 text-[15px]">arrow_forward</span></a></div>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @forelse($barbers->take(4) as $barber)
                        <a href="{{ route('customer.booking.create') }}" class="group overflow-hidden rounded-xl border border-gray-200 bg-white p-2.5 transition hover:border-gold/60 hover:shadow-md"><div class="relative aspect-square overflow-hidden rounded-lg bg-cream">@if($barber->photo)<img src="{{ Storage::url($barber->photo) }}" alt="{{ $barber->user->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">@else<div class="flex h-full w-full items-center justify-center bg-charcoal text-3xl font-bold text-gold">{{ strtoupper(substr($barber->user->name, 0, 1)) }}</div>@endif<span class="absolute right-2 top-2 inline-flex items-center rounded bg-white/95 px-1.5 py-1 text-[10px] font-bold text-charcoal"><span class="material-symbols-outlined mr-0.5 text-[13px] text-gold" style="font-variation-settings: 'FILL' 1">star</span>{{ $barber->reviews_avg_rating ? number_format($barber->reviews_avg_rating, 1) : '-' }}</span></div><div class="pt-3 text-center"><p class="truncate text-xs font-bold text-charcoal">{{ $barber->user->name }}</p><p class="mt-0.5 truncate text-[10px] text-muted">{{ $barber->experience ?: 'Barber profesional' }}</p><p class="mt-1.5 text-[10px] font-semibold {{ $barber->schedules->isNotEmpty() ? 'text-brandsuccess' : 'text-muted' }}">{{ $barber->schedules->isNotEmpty() ? 'Tersedia '. $barber->schedules->first()->date->translatedFormat('D') : 'Jadwal menyusul' }}</p></div></a>
                    @empty
                        <p class="col-span-full rounded-xl border border-dashed border-gray-300 bg-white py-7 text-center text-sm text-muted">Belum ada barber aktif saat ini.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    {{-- Layanan --}}
    <section class="mb-10">
        <div class="mb-3 flex items-center justify-between"><h2 class="font-heading text-lg font-bold text-charcoal">Layanan Populer</h2><a href="{{ route('customer.booking.create') }}" class="text-xs font-bold text-gold hover:underline">Pilih Layanan</a></div>
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        @forelse($services as $service)
            <a href="{{ route('customer.booking.create') }}" class="group overflow-hidden rounded-xl border border-gray-200 bg-white p-2.5 transition hover:border-gold/60 hover:shadow-md">
                <div class="h-28 overflow-hidden rounded-lg bg-cream flex items-center justify-center">
                    @if($service->photo)
                        <img src="{{ Storage::url($service->photo) }}" alt="{{ $service->service_name }}"
                             class="w-full h-full object-cover">
                    @else
                        <span class="text-gray-300 text-xs">Tidak ada foto</span>
                    @endif
                </div>
                <div class="pt-3">
                    <p class="truncate text-xs font-bold text-charcoal">{{ $service->service_name }}</p>
                    <div class="mt-1 flex items-center justify-between gap-1"><p class="text-[10px] text-muted">{{ $service->duration }} menit</p><p class="text-xs font-bold text-gold">Rp {{ number_format($service->price, 0, ',', '.') }}</p></div>
                </div>
            </a>
        @empty
            <p class="col-span-full text-gray-400 text-sm">Belum ada layanan tersedia.</p>
        @endforelse
    </div>
    </section>

    <section class="grid grid-cols-1 gap-5 border-t border-gray-200 pt-8 text-center sm:grid-cols-3">
        <div><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-charcoal text-gold"><span class="material-symbols-outlined">content_cut</span></span><h3 class="mt-3 text-sm font-bold text-charcoal">Barber Profesional</h3><p class="mx-auto mt-1 max-w-xs text-xs leading-5 text-muted">Barber berpengalaman untuk hasil yang rapi dan sesuai gaya Anda.</p></div>
        <div><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-charcoal text-gold"><span class="material-symbols-outlined">event_available</span></span><h3 class="mt-3 text-sm font-bold text-charcoal">Jadwal Fleksibel</h3><p class="mx-auto mt-1 max-w-xs text-xs leading-5 text-muted">Pilih waktu yang sesuai tanpa perlu mengantre terlalu lama.</p></div>
        <div><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-charcoal text-gold"><span class="material-symbols-outlined">verified_user</span></span><h3 class="mt-3 text-sm font-bold text-charcoal">Pembayaran Aman</h3><p class="mx-auto mt-1 max-w-xs text-xs leading-5 text-muted">Pembayaran yang praktis dan aman untuk setiap booking Anda.</p></div>
    </section>
@endsection
