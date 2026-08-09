@extends('layouts.admin')

@section('title', 'Ulasan Customer - Barber Woi')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Ulasan Customer</h1>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500">Rata-rata Rating</p>
            <p class="text-3xl font-bold text-brand-gold">
                {{ $averageRating ? number_format($averageRating, 1) : '-' }} <span class="text-lg text-gray-400">/ 5</span>
            </p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500">Total Ulasan</p>
            <p class="text-3xl font-bold text-brand-navy">{{ $totalReviews }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="bg-white rounded-lg shadow p-4 mb-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Rating</label>
            <select name="rating" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Rating</option>
                @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" @selected(request('rating') == $i)>{{ $i }} Bintang</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="bg-brand-navy text-white px-4 py-2 rounded-lg text-sm font-semibold">Filter</button>
        <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2 rounded-lg text-sm border">Reset</a>
    </form>

    <div class="bg-white rounded-lg shadow divide-y">
        @forelse($reviews as $review)
            <div class="p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-semibold text-sm">{{ $review->customer->name }}</p>
                        <p class="text-xs text-gray-500">
                            untuk {{ $review->booking->barber->user->name ?? '-' }}
                            &middot; {{ $review->booking->service->service_name ?? '-' }}
                            &middot; {{ $review->booking->booking_code ?? '-' }}
                        </p>
                        <div class="mt-1 text-amber-500 text-sm">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= $review->rating ? '★' : '☆' }}
                            @endfor
                        </div>
                        @if($review->comment)
                            <p class="text-sm text-gray-700 mt-2">{{ $review->comment }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-2">{{ $review->created_at->diffForHumans() }}</p>
                    </div>
                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                          onsubmit="return confirm('Hapus ulasan ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline text-xs whitespace-nowrap">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-gray-500 text-sm">Belum ada ulasan.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $reviews->links() }}</div>
@endsection
