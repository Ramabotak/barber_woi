@extends('layouts.customer')

@section('title', 'Booking Saya - Barber Woi')

@section('content')
    @php
        $activeTab = request('tab') === 'riwayat' ? 'riwayat' : 'aktif';
    @endphp

    <section class="mb-8 border-b border-charcoal/10 pb-7 sm:flex sm:items-end sm:justify-between">
        <div><p class="text-[10px] font-extrabold uppercase tracking-[0.2em] text-brandwarning">Reservation desk</p><h1 class="mt-2 font-heading text-3xl font-extrabold tracking-[-0.045em] text-charcoal">Booking saya</h1><p class="mt-2 max-w-lg text-sm leading-6 text-muted">Semua detail janji temu Anda, tersusun dari yang perlu diperhatikan lebih dahulu.</p></div>
        <a href="{{ route('customer.booking.create') }}" class="mt-5 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-charcoal px-4 py-3 text-sm font-extrabold text-white transition hover:-translate-y-0.5 hover:bg-black sm:mt-0"><span class="material-symbols-outlined text-[19px] text-gold">add</span>Buat booking</a>
    </section>

    @if(session('success'))<div class="mb-6 flex items-start gap-3 rounded-xl border border-brandsuccess/20 bg-brandsuccess/10 px-4 py-3 text-sm leading-6 text-brandsuccess"><span class="material-symbols-outlined mt-0.5 text-[19px]">check_circle</span><p>{{ session('success') }}</p></div>@endif

    <div>
        <div class="mb-6 flex w-full border-b border-charcoal/10" role="tablist" aria-label="Status booking">
            <a href="{{ route('customer.booking.index') }}" @class(['-mb-px inline-flex items-center gap-2 border-b-2 px-1 py-3 pr-5 text-sm font-bold transition', 'border-charcoal text-charcoal' => $activeTab === 'aktif', 'border-transparent text-muted hover:text-charcoal' => $activeTab !== 'aktif']) aria-current="{{ $activeTab === 'aktif' ? 'page' : 'false' }}"><span>Aktif</span><span @class(['rounded-full px-2 py-0.5 text-[10px]', 'bg-charcoal text-white' => $activeTab === 'aktif', 'bg-charcoal/[0.07]' => $activeTab !== 'aktif'])>{{ $activeBookings->count() }}</span></a>
            <a href="{{ route('customer.booking.index', ['tab' => 'riwayat']) }}" @class(['-mb-px inline-flex items-center gap-2 border-b-2 px-1 py-3 pl-5 text-sm font-bold transition', 'border-charcoal text-charcoal' => $activeTab === 'riwayat', 'border-transparent text-muted hover:text-charcoal' => $activeTab !== 'riwayat']) aria-current="{{ $activeTab === 'riwayat' ? 'page' : 'false' }}"><span>Riwayat</span><span @class(['rounded-full px-2 py-0.5 text-[10px]', 'bg-charcoal text-white' => $activeTab === 'riwayat', 'bg-charcoal/[0.07]' => $activeTab !== 'riwayat'])>{{ $historyBookings->count() }}</span></a>
        </div>

        @if($activeTab === 'aktif')
        <div class="grid gap-3 lg:grid-cols-2" role="tabpanel">
            @forelse($activeBookings as $booking) @include('customer.booking._card', ['booking' => $booking])
            @empty
                <div class="col-span-full border border-dashed border-charcoal/20 bg-white/60 px-6 py-14 text-center"><span class="material-symbols-outlined text-4xl text-gold">event_available</span><h2 class="mt-4 font-heading text-lg font-bold text-charcoal">Belum ada booking aktif</h2><p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-muted">Pilih barber dan waktu yang paling nyaman—detail antrean akan muncul di sini.</p><a href="{{ route('customer.booking.create') }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-xl bg-charcoal px-4 text-sm font-bold text-white transition hover:bg-black">Pilih jadwal</a>
            @endforelse
        </div>
        @endif

        @if($activeTab === 'riwayat')
        <div class="grid gap-3 lg:grid-cols-2" role="tabpanel">
            @forelse($historyBookings as $booking) @include('customer.booking._card', ['booking' => $booking])
            @empty
                <div class="col-span-full border border-dashed border-charcoal/20 bg-white/60 px-6 py-14 text-center"><span class="material-symbols-outlined text-4xl text-muted">history</span><h2 class="mt-4 font-heading text-lg font-bold text-charcoal">Belum ada riwayat</h2><p class="mt-2 text-sm text-muted">Booking yang selesai atau dibatalkan akan tercatat di sini.</p>
            @endforelse
        </div>
        @endif
    </div>
@endsection
