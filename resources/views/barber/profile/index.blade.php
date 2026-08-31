@extends("layouts.barber")

@section("title", "Profil Barber - Barber Woi")

@section("content")
    <div class="mx-auto flex max-w-[1200px] flex-col gap-6">
        <header class="mb-6">
            <h1 class="font-['Plus_Jakarta_Sans'] text-[28px] font-semibold tracking-tight text-[#1a1c1c]">Profil Saya</h1>
            <p class="mt-1 text-sm text-[#46464a]">Kelola informasi barber Anda</p>
        </header>

        @if(session("success"))
            <div class="rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session("success") }}</div>
        @endif

        <!-- Profil Barber Card -->
        <section class="overflow-hidden rounded-xl border border-[#c7c6ca] bg-white p-6">
            <h2 class="mb-4 text-lg font-semibold text-[#1a1c1c]">Informasi Barber</h2>
            <div class="flex items-start gap-4">
                @if($barber->photo)
                    <img src="{{ asset('storage/' . $barber->photo) }}" alt="$barber->user->name" class="h-24 w-24 shrink-0 rounded-lg object-cover">
                @else
                    <div class="grid h-24 w-24 shrink-0 place-items-center rounded-lg bg-[#eeeeee] text-[#46464a]">
                        <span class="text-3xl font-bold">{{ strtoupper(substr($barber->user->name, 0, 1)) }}</span>
                    </div>
                @endif
                <div class="min-w-0 flex-1">
                    <h3 class="text-xl font-bold text-[#1a1c1c]">{{ $barber->user->name }}</h3>
                    <p class="text-sm text-[#46464a]">{{ $barber->user->email }}</p>
                    @if($barber->experience)
                        <p class="mt-2 text-sm text-[#46464a]">
                            <span class="font-medium text-[#795902]">Pengalaman:</span> {{ $barber->experience }} tahun
                        </p>
                    @endif
                    <div class="mt-3 flex items-center gap-2">
                        <span class="rounded-full bg-[#f9f9f9] px-3 py-1 text-xs font-semibold text-[#46464a] border border-[#c7c6ca]">
                            Status: {{ ucfirst($barber->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Rating & Review Section -->
        <section class="overflow-hidden rounded-xl border border-[#c7c6ca] bg-white p-6">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-[#1a1c1c]">Rating & Ulasan</h2>
                    <p class="text-sm text-[#46464a]">Dari pelanggan yang pernah booking</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-[#795902]">{{ $barber->averageRating() ?? "-" }}</div>
                        <div class="text-xs text-[#46464a]">Rata-rata rating</div>
                    </div>
                    <div class="h-8 w-px bg-[#c7c6ca]"></div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-[#1a1c1c]">{{ $barber->reviewsCount() }}</div>
                        <div class="text-xs text-[#46464a]">Total ulasan</div>
                    </div>
                </div>
            </div>

            @if($barber->reviewsCount() > 0)
                <div class="mb-4 flex items-center gap-1">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="@if($i <= round($barber->averageRating())) text-[#795902] @else text-[#c7c6ca] @endif">★</span>
                    @endfor
                    <span class="ml-2 text-xs text-[#46464a]">({{ $barber->reviewsCount() }} ulasan)</span>
                </div>

                @foreach($reviews as $review)
                    <div class="border-b border-[#e2e2e2] py-4 last:border-0">
                        <div class="flex items-start gap-3">
                            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#eeeeee] text-sm font-bold text-[#46464a]">
                                {{ strtoupper(substr($review->customer->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-[#1a1c1c]">{{ $review->customer->name }}</span>
                                    <div class="flex items-center gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="@if($i <= $review->rating) text-[#795902] @else text-[#c7c6ca] @endif text-xs">★</span>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-sm text-[#46464a]">{{ $review->comment }}</p>
                                <p class="mt-2 text-xs text-[#c7c6ca]">
                                    {{ $review->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($reviews->hasMorePages())
                    <div class="mt-4 text-center">
                        <button class="rounded-lg border border-[#c7c6ca] bg-[#f9f9f9] px-4 py-2 text-xs font-semibold text-[#46464a] hover:bg-[#eeeeee]">
                            Lihat Lebih Banyak
                        </button>
                    </div>
                @endif
            @else
                <div class="py-12 text-center text-sm text-gray-400">
                    Belum ada ulasan dari pelanggan
                </div>
            @endif
        </section>
    </div>
@endsection
