@extends('layouts.barber')

@section('title', 'Dashboard Barber - Barber Woi')

@section('content')
    <h1 class="text-2xl font-bold mb-1">Halo, {{ auth()->user()->name }} 👋</h1>
    <p class="text-gray-500 mb-6">Berikut ringkasan aktivitas Anda hari ini.</p>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500">Booking Masuk (Menunggu)</p>
            <p class="text-3xl font-bold text-brand-navy">{{ $pendingBookings->count() }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500">Sedang Dilayani</p>
            <p class="text-3xl font-bold text-brand-gold">{{ $servingBookings->count() }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500">Jadwal Hari Ini</p>
            <p class="text-3xl font-bold text-brand-navy">{{ $todaySchedules->count() }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Booking masuk terbaru --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-brand-navy">Booking Masuk Terbaru</h2>
                <a href="{{ route('barber.booking.incoming') }}" class="text-sm text-blue-600 hover:underline">Lihat semua</a>
            </div>

            @forelse($pendingBookings->take(5) as $booking)
                <div class="flex items-center justify-between py-3 border-b last:border-0">
                    <div>
                        <p class="font-medium text-sm">{{ $booking->customer->name }}
                            @if($booking->status === 'paid')
                                <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-semibold align-middle">Dibayar</span>
                            @endif
                        </p>
                        <p class="text-xs text-gray-500">{{ $booking->service->service_name }} &middot; {{ $booking->booking_code }}</p>
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('barber.booking.accept', $booking) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs bg-green-600 text-white px-3 py-1 rounded-lg">Terima</button>
                        </form>
                        <form action="{{ route('barber.booking.reject', $booking) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs bg-red-500 text-white px-3 py-1 rounded-lg"
                                    onclick="return confirm('Tolak booking ini?')">Tolak</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-sm py-4">Tidak ada booking masuk saat ini.</p>
            @endforelse
        </div>

        {{-- Jadwal hari ini --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-semibold text-brand-navy mb-4">Jadwal Hari Ini</h2>

            @forelse($todaySchedules as $schedule)
                <div class="flex items-center justify-between py-3 border-b last:border-0">
                    <div>
                        <p class="font-medium text-sm">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</p>
                        <span class="text-xs px-2 py-0.5 rounded-full
                            @if($schedule->status == 'tersedia') bg-green-100 text-green-700
                            @elseif($schedule->status == 'penuh') bg-amber-100 text-amber-800
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ ucfirst($schedule->status) }}
                        </span>
                    </div>
                    @if($schedule->status !== 'libur')
                        <form action="{{ route('barber.schedule.close', $schedule) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs text-red-500 hover:underline"
                                    onclick="return confirm('Tutup shift ini? Pastikan tidak ada antrean aktif.')">
                                Tutup Shift
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <p class="text-gray-400 text-sm py-4">Tidak ada jadwal untuk hari ini.</p>
            @endforelse
        </div>
    </div>
@endsection
