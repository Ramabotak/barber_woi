@extends('layouts.customer')

@section('title', 'Booking Saya - Barber Woi')

@section('content')
    <h1 class="text-2xl font-bold text-brand-navy mb-6">Booking Saya</h1>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div x-data="{ tab: 'aktif' }">
        <div class="flex gap-2 mb-4 border-b">
            <button @click="tab = 'aktif'"
                    :class="tab === 'aktif' ? 'border-brand-gold text-brand-navy font-semibold' : 'border-transparent text-gray-500'"
                    class="px-4 py-2 border-b-2 text-sm">
                Aktif ({{ $activeBookings->count() }})
            </button>
            <button @click="tab = 'riwayat'"
                    :class="tab === 'riwayat' ? 'border-brand-gold text-brand-navy font-semibold' : 'border-transparent text-gray-500'"
                    class="px-4 py-2 border-b-2 text-sm">
                Riwayat ({{ $historyBookings->count() }})
            </button>
        </div>

        <div x-show="tab === 'aktif'" class="space-y-3">
            @forelse($activeBookings as $booking)
                @include('customer.booking._card', ['booking' => $booking])
            @empty
                <p class="text-gray-400 text-sm py-6 text-center">Belum ada booking aktif.</p>
            @endforelse
        </div>

        <div x-show="tab === 'riwayat'" class="space-y-3" style="display: none;">
            @forelse($historyBookings as $booking)
                @include('customer.booking._card', ['booking' => $booking])
            @empty
                <p class="text-gray-400 text-sm py-6 text-center">Belum ada riwayat booking.</p>
            @endforelse
        </div>
    </div>
@endsection
