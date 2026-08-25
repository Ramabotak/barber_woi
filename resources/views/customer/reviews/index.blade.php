@extends('layouts.customer')

@section('title', 'Ulasan Saya - Barber Woi')

@section('content')
    <div class="mb-7 border-b border-gray-200 pb-4">
        <p class="mb-1 text-[11px] font-bold uppercase tracking-[0.18em] text-gold">Pengalaman Anda</p>
        <h1 class="font-heading text-2xl font-bold tracking-tight text-charcoal sm:text-3xl">Ulasan Saya</h1>
        <p class="mt-2 text-sm text-muted">Kelola masukan dan riwayat ulasan untuk layanan Anda.</p>
    </div>

    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 rounded-xl border border-brandsuccess/20 bg-brandsuccess/10 px-4 py-3 text-sm text-brandsuccess"><span class="material-symbols-outlined">task_alt</span>{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        @forelse($reviews as $review)
            <article class="flex min-h-[200px] flex-col rounded-xl border border-gray-200 bg-white p-5 transition hover:border-gold/60 hover:shadow-md">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0"><p class="text-[10px] font-bold uppercase tracking-wider text-muted">{{ $review->created_at->translatedFormat('d M Y') }}</p><h2 class="mt-1 truncate font-heading text-base font-bold text-charcoal">{{ $review->booking->service->service_name }}</h2><div class="mt-2 flex items-center gap-2"><span class="flex h-7 w-7 items-center justify-center overflow-hidden rounded-full bg-charcoal text-[10px] font-bold text-gold">@if($review->booking->barber->photo)<img src="{{ Storage::url($review->booking->barber->photo) }}" alt="{{ $review->booking->barber->user->name }}" class="h-full w-full object-cover">@else{{ strtoupper(substr($review->booking->barber->user->name, 0, 1)) }}@endif</span><p class="text-xs text-muted">oleh <span class="font-semibold text-charcoal">{{ $review->booking->barber->user->name }}</span></p></div></div>
                    <div class="flex shrink-0 text-gold">@for($i = 1; $i <= 5; $i++)<span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' {{ $i <= $review->rating ? 1 : 0 }}">star</span>@endfor</div>
                </div>
                <p class="mt-5 flex-1 text-sm leading-6 text-muted">{{ $review->comment ?: 'Anda memberikan rating tanpa komentar.' }}</p>
                <div class="mt-5 flex items-center gap-4 border-t border-gray-100 pt-4"><a href="{{ route('customer.booking.show', $review->booking) }}" class="inline-flex items-center text-xs font-bold text-gold hover:underline">Lihat / Edit <span class="material-symbols-outlined ml-1 text-[15px]">arrow_forward</span></a><form action="{{ route('customer.booking.review.destroy', $review->booking) }}" method="POST" onsubmit="return confirm('Hapus ulasan ini?');">@csrf @method('DELETE')<button type="submit" class="text-xs font-bold text-branddanger hover:underline">Hapus</button></form></div>
            </article>
        @empty
            <div class="col-span-full flex min-h-[280px] flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 bg-white px-6 text-center"><span class="material-symbols-outlined text-5xl text-gold">rate_review</span><h2 class="mt-4 font-heading text-lg font-bold text-charcoal">Belum ada ulasan</h2><p class="mt-2 max-w-sm text-sm leading-6 text-muted">Anda belum memberikan ulasan untuk layanan apa pun. Bagikan pengalaman Anda setelah kunjungan berikutnya.</p><a href="{{ route('customer.booking.index') }}" class="mt-5 text-sm font-bold text-gold hover:underline">Lihat Booking Saya</a></div>
        @endforelse
    </div>

    @if($reviews->hasPages())<div class="mt-6">{{ $reviews->links() }}</div>@endif
@endsection
