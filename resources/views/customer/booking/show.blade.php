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
            <div class="flex justify-between"><dt class="text-gray-500">Jadwal</dt><dd>{{ $booking->schedule?->date?->format('d M Y') }}{{ $booking->slot_time ? ', ' . $booking->slot_time->format('H:i') : '' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">No. Antrean</dt><dd>#{{ $booking->queue_number }}</dd></div>
        </dl>
    </div>

    {{-- Info Pembayaran --}}
    @if(!in_array($booking->status, ['completed', 'cancelled']))
        <div class="bg-white rounded-xl shadow p-4 mb-4">
            @if($booking->payment && $booking->payment->status === 'paid')
                <div class="flex items-center gap-2 text-green-700 text-sm">
                    <span>✅</span>
                    <span>Pembayaran berhasil {{ $booking->payment->paid_at?->diffForHumans() }}. Menunggu konfirmasi barber.</span>
                </div>
            @else
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <p class="text-sm font-medium">Belum dibayar</p>
                        <p class="text-xs text-gray-400">Selesaikan pembayaran agar booking bisa diproses barber.</p>
                    </div>
                    <div class="flex gap-2">
                        @if($booking->payment)
                            <form action="{{ route('customer.payment.check', $booking) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 whitespace-nowrap">
                                    Cek Status
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('customer.payment.show', $booking) }}"
                           class="bg-brand-gold text-brand-navy px-4 py-2 rounded-lg text-sm font-semibold hover:bg-amber-400 whitespace-nowrap">
                            Bayar Sekarang
                        </a>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Review --}}
    @if($booking->status === 'completed')
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-brand-navy mb-4">Ulasan</h2>

            @if($booking->review)
                <div x-data="{ editing: false }">
                    <div x-show="!editing">
                        <div class="text-amber-500 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= $booking->review->rating ? '★' : '☆' }}
                            @endfor
                        </div>
                        <p class="text-sm text-gray-600 mb-3">{{ $booking->review->comment ?? '-' }}</p>
                        <div class="flex gap-2">
                            <button type="button" @click="editing = true"
                                    class="text-sm font-semibold text-blue-600 hover:underline">Edit Ulasan</button>
                            <form action="{{ route('customer.booking.review.destroy', $booking) }}" method="POST"
                                  onsubmit="return confirm('Hapus ulasan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-red-600 hover:underline">Hapus Ulasan</button>
                            </form>
                        </div>
                    </div>

                    <form x-show="editing" x-cloak
                          action="{{ route('customer.booking.review.update', $booking) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-medium mb-1">Rating</label>
                            <div class="flex gap-1 text-2xl" x-data="{ rating: {{ $booking->review->rating }} }">
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
                                      class="w-full border rounded-lg px-3 py-2 text-sm">{{ $booking->review->comment }}</textarea>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit"
                                    class="bg-brand-gold text-brand-navy px-5 py-2 rounded-lg font-semibold text-sm hover:bg-amber-400">
                                Simpan Perubahan
                            </button>
                            <button type="button" @click="editing = false"
                                    class="px-5 py-2 rounded-lg font-semibold text-sm text-gray-600 hover:bg-gray-100">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
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
