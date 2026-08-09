@extends('layouts.barber')

@section('title', 'Antrean Aktif - Barber Woi')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Antrean Aktif</h1>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 text-red-700 p-3 rounded mb-4">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($bookings as $booking)
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl font-bold text-brand-navy">#{{ $booking->queue_number }}</span>
                    <span class="text-xs px-2 py-1 rounded-full
                        @if($booking->status == 'serving') bg-blue-100 text-blue-700
                        @elseif($booking->status == 'late') bg-red-100 text-red-700
                        @elseif($booking->status == 'waiting') bg-amber-100 text-amber-800
                        @else bg-gray-100 text-gray-700 @endif">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
                <p class="font-medium">{{ $booking->customer->name }}</p>
                <p class="text-sm text-gray-500 mb-4">{{ $booking->service->service_name }} &middot; {{ $booking->booking_code }}</p>

                <div class="flex gap-2 flex-wrap">
                    @if($booking->status === 'accepted')
                        <form action="{{ route('barber.booking.start', $booking) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="bg-brand-navy text-white text-xs px-3 py-1.5 rounded-lg">Mulai Layanan</button>
                        </form>
                    @elseif($booking->status === 'waiting')
                        <form action="{{ route('barber.booking.start', $booking) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="bg-brand-navy text-white text-xs px-3 py-1.5 rounded-lg">Mulai Layanan</button>
                        </form>
                        <form action="{{ route('barber.booking.late', $booking) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="bg-red-100 text-red-700 text-xs px-3 py-1.5 rounded-lg">Tandai Telat</button>
                        </form>
                    @elseif($booking->status === 'late')
                        <form action="{{ route('barber.booking.start', $booking) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="bg-brand-navy text-white text-xs px-3 py-1.5 rounded-lg">Mulai Layanan</button>
                        </form>
                    @elseif($booking->status === 'serving')
                        <form action="{{ route('barber.booking.finish', $booking) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="bg-green-600 text-white text-xs px-3 py-1.5 rounded-lg">Selesai</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-gray-400 text-sm col-span-full py-6 text-center">Tidak ada antrean aktif saat ini.</p>
        @endforelse
    </div>
@endsection
