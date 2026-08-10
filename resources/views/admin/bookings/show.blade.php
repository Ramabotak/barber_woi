@extends('layouts.admin')

@section('title', 'Detail Booking - Barber Woi')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Detail Booking {{ $booking->booking_code }}</h1>
        <a href="{{ route('admin.bookings.index') }}" class="text-blue-600 hover:underline text-sm">&larr; Kembali</a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-semibold mb-3 text-brand-navy">Informasi Booking</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Kode</dt><dd>{{ $booking->booking_code }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Customer</dt><dd>{{ $booking->customer->name }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Barber</dt><dd>{{ $booking->barber->user->name }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Layanan</dt><dd>{{ $booking->service->service_name }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Jadwal</dt><dd>{{ $booking->schedule?->date?->format('d M Y') }}, {{ $booking->schedule?->start_time }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">No. Antrean</dt><dd>{{ $booking->queue_number }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Status</dt><dd class="font-semibold">{{ $booking->status }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Check-in</dt><dd>{{ $booking->check_in_time?->format('d M Y H:i') ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Selesai</dt><dd>{{ $booking->finished_at?->format('d M Y H:i') ?? '-' }}</dd></div>
            </dl>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-semibold mb-3 text-brand-navy">Pembayaran</h2>
            @if($booking->payment)
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Jumlah</dt><dd>Rp {{ number_format($booking->payment->amount, 0, ',', '.') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Metode</dt><dd>{{ $booking->payment->payment_method ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Status</dt><dd class="font-semibold">{{ $booking->payment->status }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Dibayar</dt><dd>{{ $booking->payment->paid_at?->format('d M Y H:i') ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Direfund</dt><dd>{{ $booking->payment->refunded_at?->format('d M Y H:i') ?? '-' }}</dd></div>
                </dl>
            @else
                <p class="text-gray-400 text-sm">Belum ada data pembayaran.</p>
            @endif

            @if($booking->review)
                <h2 class="font-semibold mt-6 mb-3 text-brand-navy">Ulasan Customer</h2>
                <p class="text-sm">Rating: {{ $booking->review->rating }}/5</p>
                <p class="text-sm text-gray-600 mt-1">{{ $booking->review->comment ?? '-' }}</p>
            @endif
        </div>
    </div>

    <div class="mt-6 flex gap-2 flex-wrap">
        @if(!in_array($booking->status, ['completed', 'cancelled']))
            <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold"
                        onclick="return confirm('Batalkan booking ini?')">Batalkan Booking</button>
            </form>
            <form action="{{ route('admin.bookings.force-complete', $booking) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold"
                        onclick="return confirm('Langsung tandai booking ini selesai? Aksi ini melewati urutan status normal (accepted/waiting/serving).')">
                    Selesaikan Langsung
                </button>
            </form>
        @endif
        @if($booking->status === 'cancelled' && $booking->payment && $booking->payment->status !== 'refunded')
            <form action="{{ route('admin.bookings.refund', $booking) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-semibold"
                        onclick="return confirm('Proses refund untuk booking ini?')">Proses Refund</button>
            </form>
        @endif
    </div>
@endsection
