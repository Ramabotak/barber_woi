@extends('layouts.barber')

@section('title', 'Dashboard Barber - Barber Woi')

@section('content')
    @php
        $maxChartValue = max($chartValues->all()) ?: 1;
        $workProgress = min(100, round(($remainingWorkMinutes / 480) * 100));
    @endphp
    <div class="mx-auto flex max-w-[1200px] flex-col gap-6">
        <header>
            <h1 class="font-['Plus_Jakarta_Sans'] text-[28px] font-semibold tracking-tight text-[#1a1c1c]">Ringkasan Hari Ini</h1>
            <p class="mt-1 text-sm text-[#46464a]">{{ now()->translatedFormat('l, d F Y') }}</p>
        </header>

        @if(session('success')) <div class="rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div> @endif

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <section class="flex min-h-44 flex-col gap-4 rounded-xl border border-[#c7c6ca] bg-white p-4 transition-colors hover:border-[#77767b]"><div class="grid h-10 w-10 place-items-center rounded-lg bg-[#eeeeee] text-lg text-[#010102]">▣</div><div><p class="text-sm font-semibold text-[#46464a]">Booking Hari Ini</p><h2 class="mt-1 font-['Plus_Jakarta_Sans'] text-[28px] font-semibold">{{ $todayBookings }} <span class="font-['Inter'] text-base font-normal text-[#46464a]">/ {{ $availableSlots }} slot</span></h2></div></section>
            <section class="flex min-h-44 flex-col gap-4 rounded-xl border border-[#1c1c1e] bg-[#010102] p-4 text-white"><div class="flex items-start justify-between"><div class="grid h-10 w-10 place-items-center rounded-lg bg-[#795902]/30 text-[#ffdf9e]">Rp</div>@if($revenueChange !== null)<span class="rounded-full border border-[#ffdf9e]/20 bg-[#ffdf9e]/10 px-2 py-1 text-[10px] font-semibold text-[#ffdf9e]">↗ {{ $revenueChange >= 0 ? '+' : '' }}{{ $revenueChange }}%</span>@endif</div><div><p class="text-sm font-semibold text-[#c8c6c8]">Pendapatan Hari Ini</p><h2 class="mt-1 font-['Plus_Jakarta_Sans'] text-[28px] font-semibold">Rp {{ number_format($revenueToday, 0, ',', '.') }}</h2></div></section>
            <section class="flex min-h-44 flex-col gap-4 rounded-xl border border-[#c7c6ca] bg-white p-4 transition-colors hover:border-[#77767b]"><div class="grid h-10 w-10 place-items-center rounded-lg bg-[#eeeeee] text-lg text-[#010102]">◷</div><div><p class="text-sm font-semibold text-[#46464a]">Jam Kerja Tersisa</p><h2 class="mt-1 font-['Plus_Jakarta_Sans'] text-[28px] font-semibold">{{ intdiv($remainingWorkMinutes, 60) }}j {{ $remainingWorkMinutes % 60 }}m</h2></div><div class="mt-auto h-1.5 w-full rounded-full bg-[#e2e2e2]"><div class="h-1.5 rounded-full bg-[#795902]" style="width: {{ $workProgress }}%"></div></div></section>
        </div>

        <section class="rounded-xl border border-[#c7c6ca] bg-white p-4 sm:p-6">
            <div class="mb-5 flex flex-col gap-2 sm:mb-6 sm:flex-row sm:items-center sm:justify-between"><h2 class="font-['Plus_Jakarta_Sans'] text-lg font-semibold sm:text-xl">Statistik Performa</h2><span class="w-fit rounded-lg border border-[#c7c6ca] bg-[#f9f9f9] px-3 py-1.5 text-xs font-semibold text-[#46464a] sm:px-4 sm:py-2 sm:text-sm">7 Hari Terakhir</span></div>
            <div class="relative flex h-52 items-end border-b border-[#c7c6ca] pb-7 pl-7 sm:h-64 sm:pl-8">
                <div class="absolute inset-y-0 left-0 flex flex-col justify-between pb-7 text-[10px] text-[#46464a]/60"><span>100%</span><span>75%</span><span>50%</span><span>25%</span><span>0</span></div>
                <div class="flex h-full w-full items-end justify-between gap-1.5 sm:gap-3 md:gap-6">
                    @foreach($chartValues as $index => $value)
                        @php $height = $value > 0 ? max(8, round(($value / $maxChartValue) * 100)) : 4; @endphp
                        <div class="group flex h-full flex-1 flex-col items-center justify-end"><div title="Rp {{ number_format($value, 0, ',', '.') }}" class="w-full max-w-12 rounded-t-sm bg-[#e2e2e2] transition-colors duration-200 group-hover:bg-[#010102] {{ $value == $maxChartValue && $value > 0 ? '!bg-[#795902]' : '' }}" style="height: {{ $height }}%"></div><span class="mt-2 text-xs {{ $value == $maxChartValue && $value > 0 ? 'font-bold text-[#1a1c1c]' : 'text-[#46464a]' }}">{{ $chartLabels[$index] }}</span></div>
                    @endforeach
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <section class="overflow-hidden rounded-xl border border-[#c7c6ca] bg-white"><header class="flex items-center justify-between border-b border-[#c7c6ca] px-4 py-4"><h2 class="flex items-center gap-2 text-sm font-semibold"><span class="h-2 w-2 animate-pulse rounded-full bg-[#795902]"></span>Antrean Aktif</h2><a href="{{ route('barber.queue') }}" class="text-xs font-medium text-[#46464a] hover:text-black">Lihat Semua</a></header><div>
                @forelse($servingBookings->take(3) as $booking)
                    <div class="flex items-center justify-between gap-3 border-b border-[#e2e2e2] p-4 last:border-0"><div class="flex min-w-0 items-center gap-3 sm:gap-4"><div class="grid h-10 w-10 shrink-0 place-items-center rounded {{ $booking->status === 'serving' ? 'bg-[#010102] text-white' : 'bg-[#eeeeee] text-[#46464a]' }} text-sm font-bold">{{ $booking->queue_number }}</div><div class="min-w-0"><p class="truncate text-sm font-semibold">{{ $booking->customer->name }}</p><p class="truncate text-xs text-[#46464a]">{{ $booking->service->service_name }}</p></div></div><div class="shrink-0 text-right"><span @class(['inline-block rounded px-2 py-1 text-[10px] font-semibold', 'bg-[#fdd275]/30 text-[#795902]' => $booking->status === 'serving', 'bg-[#eeeeee] text-[#46464a]' => $booking->status !== 'serving'])>{{ $booking->status === 'serving' ? 'Sedang Dilayani' : ucfirst($booking->status) }}</span><p class="mt-1 text-[10px] text-[#46464a]">{{ $booking->slot_time?->format('H:i') ?? '-' }} WIB</p></div></div>
                @empty <p class="py-10 text-center text-sm text-gray-400">Tidak ada antrean aktif.</p> @endforelse
            </div></section>
            <section class="overflow-hidden rounded-xl border border-[#c7c6ca] bg-white"><header class="flex items-center justify-between border-b border-[#c7c6ca] px-4 py-4"><h2 class="flex items-center gap-2 text-sm font-semibold"><span>♧</span>Booking Masuk</h2><span class="rounded-full bg-[#ba1a1a] px-2 py-0.5 text-[10px] font-semibold text-white">{{ $pendingBookings->count() }} Baru</span></header><div class="flex flex-col gap-2 p-2">
                @forelse($pendingBookings->take(3) as $booking)
                    <div class="flex items-start justify-between rounded-lg border border-[#c7c6ca] bg-[#f9f9f9]/50 p-3"><div><p class="text-sm font-semibold">{{ $booking->customer->name }}</p><p class="mt-1 text-xs text-[#46464a]">◷ {{ $booking->schedule?->date?->isToday() ? 'Hari ini' : ($booking->schedule?->date?->translatedFormat('d M') ?? '-') }}, {{ $booking->slot_time?->format('H:i') ?? '-' }} WIB</p><p class="text-xs text-[#46464a]">{{ $booking->service->service_name }}</p></div><div class="flex gap-2"><form action="{{ route('barber.booking.reject', $booking) }}" method="POST">@csrf @method('PATCH')<button onclick="return confirm('Tolak booking ini?')" class="grid h-8 w-8 place-items-center rounded-full border border-[#c7c6ca] text-sm text-[#46464a] hover:border-red-600 hover:text-red-600">×</button></form><form action="{{ route('barber.booking.accept', $booking) }}" method="POST">@csrf @method('PATCH')<button class="grid h-8 w-8 place-items-center rounded-full bg-[#010102] text-sm text-white hover:bg-black">✓</button></form></div></div>
                @empty <p class="py-10 text-center text-sm text-gray-400">Tidak ada booking baru.</p> @endforelse
            </div></section>
        </div>
    </div>
@endsection
