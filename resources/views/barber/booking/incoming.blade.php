@extends('layouts.barber')

@section('title', 'Booking Masuk - Barber Woi')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="mb-6 flex flex-col gap-4 border-b border-gray-200 pb-5 sm:mb-8 sm:flex-row sm:items-end sm:justify-between sm:pb-6">
            <div><h1 class="font-['Plus_Jakarta_Sans'] text-3xl font-bold text-gray-900">Booking Masuk</h1><p class="mt-1 text-sm text-gray-500">Tinjau dan kelola permintaan reservasi baru.</p></div>
            <div class="hidden items-center rounded-full border border-[#e8dcc4] bg-[#fdfaf5] px-4 py-1.5 text-sm font-medium text-[#8b6e4e] shadow-sm sm:flex"><span class="mr-2 h-1.5 w-1.5 rounded-full bg-[#8b6e4e]"></span>{{ $bookings->count() }} Permintaan Baru</div>
        </div>

        @if(session('success')) <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div> @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            @forelse($bookings as $booking)
                <article class="flex min-h-[235px] flex-col rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-start justify-between gap-3">
                        <div class="flex min-w-0 items-center gap-3"><div class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-[#1a1a1a] text-base font-semibold text-white shadow-sm">{{ strtoupper(substr($booking->customer->name, 0, 1)) }}</div><div class="min-w-0"><h2 class="truncate text-[15px] font-semibold text-gray-900">{{ $booking->customer->name }}</h2><p class="truncate text-sm text-gray-500">{{ $booking->service->service_name }}</p></div></div>
                        <span class="shrink-0 rounded bg-[#e8dcc4] px-2.5 py-1 text-xs font-semibold text-[#8b6e4e]">MENUNGGU APPROVAL</span>
                    </div>
                    <div class="mb-5 flex flex-col gap-3 rounded-lg bg-gray-50 p-4 sm:flex-row sm:justify-between sm:gap-4"><div><p class="mb-1 text-xs text-gray-500">Tanggal &amp; Waktu</p><p class="text-sm font-medium text-gray-900">{{ $booking->schedule?->date?->translatedFormat('d M Y') ?? '-' }}, {{ $booking->slot_time?->format('H:i') ?? \Carbon\Carbon::parse($booking->schedule->start_time)->format('H:i') }}</p></div><div class="sm:text-right"><p class="mb-1 text-xs text-gray-500">Kode Booking</p><p class="break-all text-sm font-medium text-gray-900">#{{ $booking->booking_code }}</p></div></div>
                    <div class="mt-auto grid grid-cols-2 gap-3"><form action="{{ route('barber.booking.accept', $booking) }}" method="POST">@csrf @method('PATCH')<button type="submit" class="flex w-full items-center justify-center rounded-lg bg-black py-2.5 text-sm font-medium text-white transition-colors hover:bg-gray-800">✓ <span class="ml-2">Terima</span></button></form><form action="{{ route('barber.booking.reject', $booking) }}" method="POST">@csrf @method('PATCH')<button type="submit" onclick="return confirm('Tolak booking ini?')" class="flex w-full items-center justify-center rounded-lg border border-[#c4a98a] bg-white py-2.5 text-sm font-medium text-[#a07c57] transition-colors hover:bg-gray-50">× <span class="ml-2">Tolak</span></button></form></div>
                </article>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-gray-300 bg-white py-14 text-center"><p class="text-sm font-semibold text-gray-600">Belum ada booking masuk.</p><p class="mt-1 text-xs text-gray-400">Permintaan reservasi baru akan muncul di sini.</p></div>
            @endforelse
        </div>
    </div>
@endsection
