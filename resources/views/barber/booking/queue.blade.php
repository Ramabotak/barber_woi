@extends('layouts.barber')

@section('title', 'Antrean Aktif - Barber Woi')

@section('content')
    @php
        $servingCount = $bookings->where('status', 'serving')->count();
        $waitingCount = $bookings->whereIn('status', ['accepted', 'waiting'])->count();
    @endphp
    <div class="mx-auto max-w-[1200px]">
        <header class="mb-6"><h1 class="font-['Plus_Jakarta_Sans'] text-[28px] font-semibold tracking-tight text-[#1a1c1c]">Antrean Aktif</h1><p class="mt-1 text-sm text-[#46464a]">Manajemen antrean pelanggan hari ini.</p></header>
        @if(session('success')) <div class="mb-5 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div> @endif
        @if(session('error') || $errors->any()) <div class="mb-5 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }} @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div> @endif

        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="flex items-center justify-between rounded-lg border border-[#c7c6ca] bg-white p-4 shadow-sm"><div><p class="text-xs font-medium uppercase tracking-wide text-[#46464a]">Total Antrean</p><p class="font-['Plus_Jakarta_Sans'] text-2xl font-semibold">{{ $bookings->count() }}</p></div><span class="text-3xl text-[#77767b]">♧</span></div>
            <div class="flex items-center justify-between rounded-lg border border-[#c7c6ca] bg-white p-4 shadow-sm"><div><p class="text-xs font-medium uppercase tracking-wide text-[#46464a]">Sedang Dilayani</p><p class="font-['Plus_Jakarta_Sans'] text-2xl font-semibold text-[#795902]">{{ $servingCount }}</p></div><span class="text-3xl text-[#795902]">✂</span></div>
            <div class="flex items-center justify-between rounded-lg border border-[#c7c6ca] bg-white p-4 shadow-sm"><div><p class="text-xs font-medium uppercase tracking-wide text-[#46464a]">Menunggu</p><p class="font-['Plus_Jakarta_Sans'] text-2xl font-semibold text-[#d97706]">{{ $waitingCount }}</p></div><span class="text-3xl text-[#d97706]">⌛</span></div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            @forelse($bookings as $booking)
                @php
                    $accent = match($booking->status) { 'serving' => '#2563eb', 'late' => '#dc2626', default => '#d97706' };
                    $label = match($booking->status) { 'serving' => 'Serving', 'late' => 'Late', default => 'Waiting' };
                    $isServing = $booking->status === 'serving';
                    $isLate = $booking->status === 'late';
                @endphp
                <article class="overflow-hidden rounded-lg border border-[#c7c6ca] border-l-4 bg-white p-4 shadow-sm" style="border-left-color: {{ $accent }}">
                    <div class="mb-4 flex items-start justify-between gap-3"><div class="flex min-w-0 items-center gap-3 sm:gap-4"><div class="grid h-14 w-14 shrink-0 place-items-center rounded-lg bg-[#e2e2e2] font-['Plus_Jakarta_Sans'] text-2xl font-bold text-[#1a1c1c]">{{ $booking->queue_number }}</div><div class="min-w-0"><h2 class="truncate font-['Plus_Jakarta_Sans'] text-lg font-semibold">{{ $booking->customer->name }}</h2><p class="truncate text-sm text-[#46464a]">{{ $booking->customer->phone_number ?? 'Tidak ada nomor telepon' }}</p></div></div><span class="shrink-0 rounded-full border px-2 py-1 text-xs font-semibold" style="background-color: {{ $accent }}1A; color: {{ $accent }}; border-color: {{ $accent }}33">{{ $label }}</span></div>
                    <div class="mb-4 rounded-lg border border-[#c7c6ca] bg-[#f9f9f9] p-3"><div class="flex items-center justify-between gap-3"><span class="min-w-0 truncate text-sm font-semibold text-[#1a1c1c]">{{ $booking->service->service_name }}</span><span class="shrink-0 text-sm text-[#46464a]">{{ $booking->service->duration ?? '-' }} min</span></div></div>
                    <div class="flex flex-col gap-3 border-t border-[#c7c6ca] pt-3 sm:flex-row sm:items-center sm:justify-between"><p @class(['flex items-center gap-1 text-xs', 'text-red-600' => $isLate, 'text-[#46464a]' => !$isLate])>@if($isLate) ⚠ Terlambat @else ◷ {{ $isServing ? 'Mulai' : 'Est.' }}: {{ $booking->check_in_time?->format('H:i') ?? $booking->slot_time?->format('H:i') ?? '-' }} WIB @endif</p><div class="flex flex-wrap gap-2">
                        @if($isServing)<form action="{{ route('barber.booking.finish', $booking) }}" method="POST">@csrf @method('PATCH')<button class="rounded-lg bg-[#fdd275] px-4 py-2 text-sm font-semibold text-[#775800] hover:bg-[#ffdf9e]">Selesaikan</button></form>
                        @elseif(in_array($booking->status, ['accepted', 'waiting', 'late']))
                            @if($booking->status === 'waiting')<form action="{{ route('barber.booking.late', $booking) }}" method="POST">@csrf @method('PATCH')<button class="rounded-lg border border-[#c7c6ca] px-3 py-2 text-sm text-[#1a1c1c] hover:bg-[#e2e2e2]">Tandai Telat</button></form>@endif
                            <form action="{{ route('barber.booking.start', $booking) }}" method="POST">@csrf @method('PATCH')<button class="rounded-lg bg-[#1c1c1e] px-4 py-2 text-sm font-semibold text-white hover:bg-black">Mulai Layanan</button></form>
                        @endif
                    </div></div>
                </article>
            @empty
                <div class="col-span-full rounded-lg border border-dashed border-gray-300 bg-white py-14 text-center text-sm text-gray-400">Tidak ada antrean aktif saat ini.</div>
            @endforelse
        </div>
    </div>
@endsection
