@extends('layouts.customer')

@section('title', 'Beranda - Barber Woi')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-brand-navy">Halo, {{ auth()->user()->name }} 👋</h1>
            <p class="text-gray-500 text-sm">Mau potong rambut? Pilih layanan favoritmu.</p>
        </div>
        <a href="{{ route('customer.booking.create') }}"
           class="bg-brand-gold text-brand-navy px-5 py-2.5 rounded-lg font-semibold hover:bg-amber-400 whitespace-nowrap">
            + Booking Sekarang
        </a>
    </div>

    {{-- Barber --}}
    <h2 class="text-lg font-semibold text-brand-navy mb-3">Barber Kami</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8" x-data="barberReviews()">
        @forelse($barbers as $barber)
            <div class="bg-white rounded-xl shadow overflow-hidden cursor-pointer hover:shadow-md transition-shadow"
                 @click="open({{ $barber->id }})">
                <div class="h-32 bg-gray-100 flex items-center justify-center">
                    @if($barber->photo)
                        <img src="{{ Storage::url($barber->photo) }}" alt="{{ $barber->user->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-16 h-16 rounded-full bg-brand-navy text-white flex items-center justify-center text-xl font-bold">
                            {{ strtoupper(substr($barber->user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="p-3">
                    <p class="font-semibold text-sm">{{ $barber->user->name }}</p>
                    <p class="text-xs text-gray-500 line-clamp-2 mb-1">{{ $barber->experience ?? 'Barber profesional' }}</p>
                    @if($barber->reviews_count > 0)
                        <p class="text-xs text-amber-500">★ {{ number_format($barber->reviews_avg_rating, 1) }} <span class="text-gray-400">({{ $barber->reviews_count }} ulasan)</span></p>
                    @else
                        <p class="text-xs text-gray-400">Belum ada ulasan</p>
                    @endif
                    @if($barber->schedules->isNotEmpty())
                        <p class="text-[10px] text-green-600 mt-1 leading-tight">
                            Jadwal: {{ $barber->schedules->take(3)->map(fn($s) => \Illuminate\Support\Carbon::parse($s->date)->locale('id')->translatedFormat('D, d M'))->implode(' · ') }}
                        </p>
                    @endif
                </div>
            </div>
        @empty
            <p class="col-span-full text-gray-400 text-sm">Belum ada barber aktif saat ini.</p>
        @endforelse

        {{-- Modal Rating & Riwayat Ulasan Barber --}}
        <div x-show="show" x-cloak
             class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
             @click.self="show = false">
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full max-h-[80vh] overflow-y-auto">
                <div class="p-5 border-b flex items-start justify-between sticky top-0 bg-white">
                    <div>
                        <h3 class="font-bold text-brand-navy" x-text="name"></h3>
                        <template x-if="!loading">
                            <p class="text-sm mt-1">
                                <span class="text-amber-500 font-semibold" x-show="average" x-text="'★ ' + average"></span>
                                <span class="text-gray-400" x-show="!average">Belum ada ulasan</span>
                                <span class="text-gray-400" x-show="average"> (<span x-text="count"></span> ulasan)</span>
                            </p>
                        </template>
                    </div>
                    <button type="button" @click="show = false" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
                </div>
                <div class="p-5">
                    <p x-show="loading" class="text-sm text-gray-400 text-center py-6">Memuat ulasan...</p>
                    <p x-show="!loading && reviews.length === 0" class="text-sm text-gray-400 text-center py-6">Belum ada ulasan untuk barber ini.</p>
                    <div class="space-y-4" x-show="!loading && reviews.length > 0">
                        <template x-for="review in reviews" :key="review.customer_name + review.date">
                            <div class="border-b pb-3 last:border-0">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-sm font-medium" x-text="review.customer_name"></p>
                                    <p class="text-xs text-gray-400" x-text="review.date"></p>
                                </div>
                                <div class="text-amber-500 text-xs mb-1">
                                    <template x-for="i in 5" :key="i">
                                        <span x-text="i <= review.rating ? '★' : '☆'"></span>
                                    </template>
                                </div>
                                <p class="text-sm text-gray-600" x-text="review.comment || '-'"></p>
                            </div>
                        </template>
                    </div>
                    <div class="mt-5 pt-4 border-t text-center">
                        <a href="{{ route('customer.booking.create') }}" class="text-sm font-semibold text-brand-gold hover:underline">Booking barber ini &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Layanan --}}
    <h2 class="text-lg font-semibold text-brand-navy mb-3">Layanan</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @forelse($services as $service)
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="h-28 bg-gray-100 flex items-center justify-center">
                    @if($service->photo)
                        <img src="{{ Storage::url($service->photo) }}" alt="{{ $service->service_name }}"
                             class="w-full h-full object-cover">
                    @else
                        <span class="text-gray-300 text-xs">Tidak ada foto</span>
                    @endif
                </div>
                <div class="p-3">
                    <p class="font-semibold text-sm">{{ $service->service_name }}</p>
                    <p class="text-brand-gold font-bold text-sm">Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400">{{ $service->duration }} menit</p>
                </div>
            </div>
        @empty
            <p class="col-span-full text-gray-400 text-sm">Belum ada layanan tersedia.</p>
        @endforelse
    </div>
@endsection

@push('scripts')
<script>
    function barberReviews() {
        return {
            show: false,
            loading: false,
            name: '',
            average: null,
            count: 0,
            reviews: [],
            open(barberId) {
                this.show = true;
                this.loading = true;
                this.reviews = [];

                fetch(`{{ url('customer/booking/barber') }}/${barberId}/reviews`)
                    .then(res => res.json())
                    .then(data => {
                        this.name = data.barber_name;
                        this.average = data.average;
                        this.count = data.count;
                        this.reviews = data.reviews;
                        this.loading = false;
                    })
                    .catch(() => {
                        this.loading = false;
                    });
            }
        }
    }
</script>
@endpush
