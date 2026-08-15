@extends('layouts.admin')

@section('title', 'Kelola Booking - Barber Woi')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Kelola Booking</h1>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    {{-- Filter --}}
    <form method="GET" class="bg-white rounded-lg shadow p-4 mb-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="status" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Status</option>
                @foreach(['pending', 'paid', 'accepted', 'waiting', 'late', 'serving', 'completed', 'cancelled'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Jadwal</label>
            <input type="date" name="date" value="{{ request('date') }}" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <button type="submit" class="bg-brand-navy text-white px-4 py-2 rounded-lg text-sm font-semibold">Filter</button>
        <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 rounded-lg text-sm border">Reset</a>
    </form>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left p-3">Kode</th>
                    <th class="text-left p-3">Customer</th>
                    <th class="text-left p-3">Barber</th>
                    <th class="text-left p-3">Layanan</th>
                    <th class="text-left p-3">Jadwal</th>
                    <th class="text-left p-3">Status</th>
                    <th class="text-left p-3">Pembayaran</th>
                    <th class="text-left p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="text-blue-600 hover:underline">
                                {{ $booking->booking_code }}
                            </a>
                        </td>
                        <td class="p-3">{{ $booking->customer->name ?? '-' }}</td>
                        <td class="p-3">{{ $booking->barber->user->name ?? '-' }}</td>
                        <td class="p-3">{{ $booking->service->service_name ?? '-' }}</td>
                        <td class="p-3">{{ $booking->schedule?->date?->format('d M Y') }}{{ $booking->slot_time ? ', ' . $booking->slot_time->format('H:i') : '' }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($booking->status == 'completed') bg-green-100 text-green-700
                                @elseif($booking->status == 'cancelled') bg-red-100 text-red-700
                                @elseif($booking->status == 'serving') bg-blue-100 text-blue-700
                                @elseif($booking->status == 'paid') bg-emerald-100 text-emerald-700
                                @else bg-amber-100 text-amber-800 @endif">
                                {{ $booking->status }}
                            </span>
                        </td>
                        <td class="p-3">
                            @if($booking->payment)
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($booking->payment->status == 'paid') bg-green-100 text-green-700
                                    @elseif($booking->payment->status == 'refunded') bg-purple-100 text-purple-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ $booking->payment->status }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="p-3 space-x-2 whitespace-nowrap">
                            @if(!in_array($booking->status, ['completed', 'cancelled']))
                                <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-red-600 hover:underline"
                                            onclick="return confirm('Batalkan booking ini?')">Batalkan</button>
                                </form>
                            @endif
                            @if($booking->status === 'cancelled' && $booking->payment && $booking->payment->status !== 'refunded')
                                <form action="{{ route('admin.bookings.refund', $booking) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-purple-600 hover:underline"
                                            onclick="return confirm('Proses refund untuk booking ini?')">Refund</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-6 text-center text-gray-500">Belum ada booking.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-3">{{ $bookings->links() }}</div>
    </div>
@endsection
