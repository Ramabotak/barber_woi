@extends('layouts.barber')

@section('title', 'Booking Masuk - Barber Woi')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Booking Masuk</h1>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left p-3">Kode</th>
                    <th class="text-left p-3">Customer</th>
                    <th class="text-left p-3">Layanan</th>
                    <th class="text-left p-3">Jadwal</th>
                    <th class="text-left p-3">Diajukan</th>
                    <th class="text-left p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $booking->booking_code }}</td>
                        <td class="p-3">
                            {{ $booking->customer->name }}
                            <div class="text-xs text-gray-400">{{ $booking->customer->phone_number ?? '-' }}</div>
                        </td>
                        <td class="p-3">{{ $booking->service->service_name }}</td>
                        <td class="p-3">
                            {{ $booking->schedule?->date?->format('d M Y') }}
                            <div class="text-xs text-gray-400">{{ $booking->slot_time?->format('H:i') ?? \Carbon\Carbon::parse($booking->schedule->start_time)->format('H:i') }}</div>
                        </td>
                        <td class="p-3">{{ $booking->created_at->diffForHumans() }}</td>
                        <td class="p-3 space-x-2 whitespace-nowrap">
                            <form action="{{ route('barber.booking.accept', $booking) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="bg-green-600 text-white text-xs px-3 py-1.5 rounded-lg">Terima</button>
                            </form>
                            <form action="{{ route('barber.booking.reject', $booking) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="bg-red-500 text-white text-xs px-3 py-1.5 rounded-lg"
                                        onclick="return confirm('Tolak booking ini?')">Tolak</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-gray-500">Tidak ada booking masuk saat ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
