@extends('layouts.barber')

@section('title', 'Antrean Aktif - Barber Woi')

@section('content')
    @php
        $servingCount = $bookings->where('status', 'serving')->count();
        $waitingCount = $bookings->whereIn('status', ['paid', 'waiting'])->count();
    @endphp

    <div class="mx-auto max-w-[1200px]">
        <header class="mb-7 border-b border-[#c7c6ca] pb-6 sm:flex sm:items-end sm:justify-between">
            <div><p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#795902]">Service floor</p><h1 class="mt-2 font-['Plus_Jakarta_Sans'] text-3xl font-semibold tracking-tight text-[#1a1c1c]">Antrean aktif</h1><p class="mt-2 text-sm text-[#646368]">Prioritaskan pelanggan yang sudah lunas dan siap Anda layani.</p></div>
            <div class="mt-4 flex items-center gap-2 text-xs font-medium text-[#46464a] sm:mt-0"><span class="h-2 w-2 rounded-full bg-[#2e7d32]"></span>Data diperbarui saat status booking berubah</div>
        </header>

        @if(session('success'))<div class="mb-5 flex items-start gap-3 border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"><span class="material-symbols-outlined mt-0.5 text-[19px] text-green-700">check_circle</span><p>{{ session('success') }}</p></div>@endif
        @if(session('error') || $errors->any())<div class="mb-5 flex items-start gap-3 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><span class="material-symbols-outlined mt-0.5 text-[19px] text-red-700">error</span><div>{{ session('error') }} @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div></div>@endif

        <section class="mb-7 grid gap-px overflow-hidden border border-[#c7c6ca] bg-[#c7c6ca] sm:grid-cols-3">
            <div class="bg-white p-5"><div class="flex items-start justify-between"><div><p class="text-[10px] font-bold uppercase tracking-[0.15em] text-[#646368]">Total hari ini</p><p class="mt-3 font-['Plus_Jakarta_Sans'] text-4xl font-semibold tracking-tight text-[#1a1c1c]">{{ $bookings->count() }}</p></div><span class="material-symbols-outlined text-2xl text-[#795902]">format_list_numbered</span></div><p class="mt-4 text-xs text-[#646368]">Pelanggan dalam antrean layanan</p></div>
            <div class="bg-white p-5"><div class="flex items-start justify-between"><div><p class="text-[10px] font-bold uppercase tracking-[0.15em] text-[#646368]">Siap dilayani</p><p class="mt-3 font-['Plus_Jakarta_Sans'] text-4xl font-semibold tracking-tight text-[#795902]">{{ $waitingCount }}</p></div><span class="material-symbols-outlined text-2xl text-[#795902]">hourglass_top</span></div><p class="mt-4 text-xs text-[#646368]">Sudah lunas atau menunggu giliran</p></div>
            <div class="bg-[#1a1c1c] p-5 text-white"><div class="flex items-start justify-between"><div><p class="text-[10px] font-bold uppercase tracking-[0.15em] text-white/55">Sedang di kursi</p><p class="mt-3 font-['Plus_Jakarta_Sans'] text-4xl font-semibold tracking-tight text-[#fdd275]">{{ $servingCount }}</p></div><span class="material-symbols-outlined text-2xl text-[#fdd275]">content_cut</span></div><p class="mt-4 text-xs text-white/55">Layanan yang sedang berjalan</p></div>
        </section>

        <div class="grid gap-4 xl:grid-cols-2">
            @forelse($bookings as $booking)
                @php
                    $meta = match($booking->status) {
                        'serving' => ['Sedang dilayani', 'content_cut', '#1a1c1c', 'bg-[#1a1c1c] text-[#fdd275] border-[#1a1c1c]'],
                        'late' => ['Terlambat', 'priority_high', '#ba1a1a', 'bg-[#fff0f0] text-[#ba1a1a] border-[#f0b7b7]'],
                        'waiting' => ['Menunggu giliran', 'hourglass_top', '#795902', 'bg-[#fff8e9] text-[#795902] border-[#ecd29a]'],
                        default => ['Lunas · siap dilayani', 'verified', '#2e7d32', 'bg-[#edf8ef] text-[#2e7d32] border-[#b7dfbd]'],
                    };
                    $isServing = $booking->status === 'serving';
                    $isLate = $booking->status === 'late';
                @endphp
                <article class="border border-[#c7c6ca] bg-white p-5 shadow-[0_4px_14px_rgba(24,24,24,.04)] transition hover:border-[#8b898d] hover:shadow-[0_10px_22px_rgba(24,24,24,.07)]">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex min-w-0 items-center gap-4"><div class="grid h-14 w-14 shrink-0 place-items-center border border-[#c7c6ca] bg-[#f1f0ed] font-['Plus_Jakarta_Sans'] text-2xl font-semibold text-[#1a1c1c]">{{ str_pad($booking->queue_number, 2, '0', STR_PAD_LEFT) }}</div><div class="min-w-0"><h2 class="truncate font-['Plus_Jakarta_Sans'] text-lg font-semibold text-[#1a1c1c]">{{ $booking->customer->name }}</h2><p class="mt-1 truncate text-xs text-[#646368]">{{ $booking->customer->phone_number ?? 'Kontak tidak tersedia' }}</p></div></div>
                        <span class="inline-flex shrink-0 items-center gap-1.5 border px-2.5 py-1.5 text-[10px] font-bold {{ $meta[3] }}"><span class="material-symbols-outlined text-[15px]">{{ $meta[1] }}</span>{{ $meta[0] }}</span>
                    </div>
                    <div class="mt-5 flex items-center justify-between gap-4 border-y border-[#e5e4e1] py-3.5"><div class="min-w-0"><p class="truncate text-sm font-semibold text-[#1a1c1c]">{{ $booking->service->service_name }}</p><p class="mt-1 text-xs text-[#646368]">{{ $booking->service->duration ?? '-' }} menit · slot {{ $booking->slot_time?->format('H:i') ?? '-' }} WIB</p></div><span class="material-symbols-outlined shrink-0 text-xl text-[#795902]">{{ $isServing ? 'content_cut' : 'schedule' }}</span></div>
                    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><p class="flex items-center gap-1.5 text-xs {{ $isLate ? 'font-semibold text-[#ba1a1a]' : 'text-[#646368]' }}"><span class="material-symbols-outlined text-[17px]">{{ $isLate ? 'warning' : 'schedule' }}</span>@if($isLate)Konfirmasi kehadiran pelanggan sebelum melanjutkan.@elseif($isServing)Mulai {{ $booking->check_in_time?->format('H:i') ?? '-' }} WIB.@elseSiap dimulai saat kursi tersedia.@endif</p>
                        <div class="flex flex-wrap gap-2">
                            @if($isServing)<form action="{{ route('barber.booking.finish', $booking) }}" method="POST">@csrf @method('PATCH')<button class="min-h-10 bg-[#fdd275] px-4 text-sm font-semibold text-[#4b3600] transition hover:bg-[#ffdf9e]">Selesaikan</button></form>
                            @elseif(in_array($booking->status, ['paid', 'waiting', 'late'], true))
                                @if($booking->status === 'waiting')<form action="{{ route('barber.booking.late', $booking) }}" method="POST">@csrf @method('PATCH')<button class="min-h-10 border border-[#c7c6ca] px-3 text-sm font-medium text-[#1a1c1c] transition hover:bg-[#f1f0ed]">Tandai telat</button></form>@endif
                                <form action="{{ route('barber.booking.start', $booking) }}" method="POST">@csrf @method('PATCH')<button class="min-h-10 bg-[#1a1c1c] px-4 text-sm font-semibold text-white transition hover:bg-black">Mulai layanan</button></form>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="border border-dashed border-[#aaa7a1] bg-white px-6 py-16 text-center xl:col-span-2"><span class="material-symbols-outlined text-4xl text-[#795902]">format_list_numbered</span><h2 class="mt-4 font-['Plus_Jakarta_Sans'] text-xl font-semibold text-[#1a1c1c]">Antrean masih kosong</h2><p class="mt-2 text-sm text-[#646368]">Booking yang sudah lunas akan muncul di sini.</p></div>
            @endforelse
        </div>
    </div>
@endsection
