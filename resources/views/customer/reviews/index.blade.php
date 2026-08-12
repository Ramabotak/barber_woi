@extends('layouts.customer')

@section('title', 'Ulasan Saya - Barber Woi')

@section('content')
    <h1 class="text-2xl font-bold text-brand-navy mb-6">Ulasan Saya</h1>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="space-y-3">
        @forelse($reviews as $review)
            <div class="bg-white rounded-xl shadow p-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-semibold text-brand-navy">{{ $review->booking->barber->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $review->booking->service->service_name }} &middot; {{ $review->created_at->format('d M Y') }}</p>
                    </div>
                    <div class="text-amber-500 text-sm whitespace-nowrap">
                        @for($i = 1; $i <= 5; $i++)
                            {{ $i <= $review->rating ? '★' : '☆' }}
                        @endfor
                    </div>
                </div>

                @if($review->comment)
                    <p class="text-sm text-gray-600 mb-3">{{ $review->comment }}</p>
                @endif

                <div class="flex gap-3">
                    <a href="{{ route('customer.booking.show', $review->booking) }}"
                       class="text-sm font-semibold text-blue-600 hover:underline">Lihat / Edit</a>
                    <form action="{{ route('customer.booking.review.destroy', $review->booking) }}" method="POST"
                          onsubmit="return confirm('Hapus ulasan ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm font-semibold text-red-600 hover:underline">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-400 text-sm py-6 text-center">Anda belum pernah memberi ulasan.</p>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $reviews->links() }}
    </div>
@endsection
