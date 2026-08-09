@extends('layouts.customer')

@section('title', 'Detail Booking - Barber Woi')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-brand-navy">Detail Booking</h1>
        <a href="{{ route('customer.booking.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali</a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 text-red-700 p-3 rounded mb-4">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-xl shadow p-6 mb-4">
        <div class="flex items-start justify-between mb-4">
            <div>
                <p class="text-xs text-gray-400">Kode Booking</p>
                <p class="font-bold text-lg text-brand-navy">{{ $booking->booking_code }}</p>
            </div>
            <span class="px-3 py-1.5 text-xs rounded-full font-semibold
                @if($booking->status == 'completed') bg-green-100 text-green-700
                @elseif($booking->status == 'cancelled') bg-red-100 text-red-700
                @elseif($booking->status == 'serving') bg-blue-100 text-blue-700
                @elseif($booking->status == 'late') bg-red-50 text-red-600
                @else bg-amber-100 text-amber-800 @endif">
                {{ ucfirst($booking->status) }}
            </span>
        </div>

        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">Barber</dt><dd>{{ $booking->barber->user->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Layanan</dt><dd>{{ $booking->service->service_name }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Harga</dt><dd>Rp {{ number_format($booking->service->price, 0, ',', '.') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Jadwal</dt><dd>{{ $booking->schedule?->date?->format('d M Y') }}, {{ \Carbon\Carbon::parse($booking->schedule->start_time)->format('H:i') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">No. Antrean</dt><dd>#{{ $booking->queue_number }}</dd></div>
        </dl>
    </div>

    {{-- Info pembayaran (sementara nonaktif) --}}
    @if(!in_array($booking->status, ['completed', 'cancelled']))
        <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-4 mb-4 text-sm">
            💳 Pembayaran online sedang dalam pemeliharaan. Silakan lakukan pembayaran langsung di tempat saat kedatangan.
        </div>
    @endif

    {{-- Review --}}
    @if($booking->status === 'completed')
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-brand-navy mb-4">Ulasan</h2>

            @if($booking->review)
                <div class="text-amber-500 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        {{ $i <= $booking->review->rating ? '★' : '☆' }}
                    @endfor
                </div>
                <p class="text-sm text-gray-600">{{ $booking->review->comment ?? '-' }}</p>
            @else
                <form action="{{ route('customer.booking.review', $booking) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium mb-1">Rating</label>
                        <div class="flex gap-1 text-2xl" x-data="{ rating: 0 }">
                            <input type="hidden" name="rating" x-bind:value="rating" required>
                            <template x-for="i in 5" :key="i">
                                <span @click="rating = i"
                                      class="cursor-pointer"
                                      :class="i <= rating ? 'text-amber-500' : 'text-gray-300'">★</span>
                            </template>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Komentar (opsional)</label>
                        <textarea name="comment" rows="3" maxlength="1000"
                                  class="w-full border rounded-lg px-3 py-2 text-sm"
                                  placeholder="Bagaimana pengalaman Anda?"></textarea>
                    </div>
                    <button type="submit"
                            class="bg-brand-gold text-brand-navy px-5 py-2 rounded-lg font-semibold text-sm hover:bg-amber-400">
                        Kirim Ulasan
                    </button>
                </form>
            @endif
        </div>
    @endif
@endsection
