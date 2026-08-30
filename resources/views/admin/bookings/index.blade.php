@extends('layouts.admin')

@section('title', 'Kelola Booking - Barber Woi')

@section('content')

    @php
        $statusStyle = [
            'pending'   => 'bg-gray-100 text-gray-600 border-gray-300',
            'paid'      => 'bg-brandsuccess/10 text-brandsuccess border-brandsuccess/30',
            'accepted'  => 'bg-blue-50 text-blue-600 border-blue-200',
            'waiting'   => 'bg-brandwarning/10 text-brandwarning border-brandwarning/30',
            'late'      => 'bg-orange-50 text-orange-600 border-orange-200',
            'serving'   => 'bg-indigo-50 text-indigo-600 border-indigo-200',
            'completed' => 'bg-gray-100 text-gray-600 border-gray-300',
            'cancelled' => 'bg-branddanger/10 text-branddanger border-branddanger/30',
        ];
        $allStatuses = ['pending', 'paid', 'accepted', 'waiting', 'late', 'serving', 'completed', 'cancelled'];
    @endphp

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-200 pb-4 mb-6">
        <div>
            <h2 class="font-heading text-2xl md:text-3xl font-bold text-charcoal">Kelola Booking</h2>
            <p class="text-gray-500 mt-1">Lihat, filter, dan kelola semua booking pelanggan</p>
        </div>
        <form method="GET" class="relative">
            @if (request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">search</span>
            <input type="text" name="search" value="{{ request('search') }}"
                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:border-gold focus:ring-gold transition-colors w-full md:w-64"
                   placeholder="Cari kode booking / nama customer">
        </form>
    </div>

    @if (session('success'))
        <div class="bg-brandsuccess/10 border border-brandsuccess text-brandsuccess p-3 rounded-lg mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter Bar --}}
    <section class="flex flex-wrap items-center gap-2 bg-white p-3 border border-gray-200 rounded-lg shadow-sm mb-6">
        <span class="text-xs font-medium text-gray-500 uppercase px-2">Status</span>
        <div class="flex flex-wrap gap-1.5">
            <a href="{{ route('admin.bookings.index', array_filter(['search' => request('search'), 'date' => request('date')])) }}"
               class="px-3 py-1 rounded-full text-xs font-medium border transition-colors
                      {{ !request('status') ? 'bg-charcoal text-white border-charcoal' : 'bg-white text-charcoal border-gray-300 hover:border-charcoal' }}">
                Semua
            </a>
            @foreach ($allStatuses as $status)
                <a href="{{ route('admin.bookings.index', array_filter(['status' => $status, 'search' => request('search'), 'date' => request('date')])) }}"
                   class="px-3 py-1 rounded-full text-xs font-medium border transition-colors
                          {{ request('status') === $status ? 'bg-charcoal text-white border-charcoal' : 'bg-white text-charcoal border-gray-300 hover:border-charcoal' }}">
                    {{ ucfirst($status) }}
                </a>
            @endforeach
        </div>
        <div class="ml-auto flex items-center gap-2">
            <form method="GET" class="flex items-center gap-2">
                @if (request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                @if (request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                <input type="date" name="date" value="{{ request('date') }}"
                       class="border border-gray-300 rounded-lg px-3 py-1.5 text-xs">
                <button type="submit" class="flex items-center gap-1 px-3 py-1.5 border border-gray-300 rounded-lg text-xs hover:bg-gray-50 transition-colors">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span>
                    Filter Tanggal
                </button>
                @if (request()->anyFilled(['status', 'date', 'search']))
                    <a href="{{ route('admin.bookings.index') }}" class="text-xs text-gray-500 hover:text-charcoal underline">Reset</a>
                @endif
            </form>
        </div>
    </section>

    {{-- Data Table --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Barber</th>
                        <th class="py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Layanan</th>
                        <th class="py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal</th>
                        <th class="py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($bookings as $booking)
                        @php $isFinal = in_array($booking->status, ['completed', 'cancelled']); @endphp
                        <tr class="hover:bg-gray-50 transition-colors group {{ $isFinal ? 'opacity-70' : '' }}">
                            <td class="py-3 px-4 text-sm font-medium">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="text-charcoal hover:text-gold hover:underline">
                                    {{ $booking->booking_code }}
                                </a>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500">
                                        {{ strtoupper(substr($booking->customer->name ?? '-', 0, 2)) }}
                                    </div>
                                    <span class="text-sm text-charcoal">{{ $booking->customer->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $booking->barber->user->name ?? '-' }}</td>
                            <td class="py-3 px-4 text-sm text-charcoal">{{ $booking->service->service_name ?? '-' }}</td>
                            <td class="py-3 px-4">
                                <div class="text-sm text-charcoal">{{ $booking->schedule?->date?->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $booking->slot_time ? \Carbon\Carbon::parse($booking->slot_time)->format('H:i') : '' }}</div>
                            </td>
                            <td class="py-3 px-4 text-sm font-medium text-charcoal">
                                Rp {{ number_format($booking->payment->amount ?? $booking->service->price ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-full border text-xs font-medium {{ $statusStyle[$booking->status] ?? 'bg-gray-100 text-gray-600 border-gray-300' }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @if (!$isFinal)
                                        <form action="{{ route('admin.bookings.force-complete', $booking) }}" method="POST"
                                              onsubmit="return confirm('Tandai booking ini selesai secara paksa?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="p-1.5 text-gray-500 hover:text-brandsuccess transition-colors rounded hover:bg-brandsuccess/10" title="Tandai Selesai">
                                                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST"
                                              onsubmit="return confirm('Batalkan booking ini?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="p-1.5 text-gray-500 hover:text-branddanger transition-colors rounded hover:bg-branddanger/10" title="Batalkan">
                                                <span class="material-symbols-outlined text-[18px]">cancel</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-gray-500">Belum ada booking.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
            <span class="text-xs text-gray-500">Menampilkan {{ $bookings->firstItem() ?? 0 }}-{{ $bookings->lastItem() ?? 0 }} dari {{ $bookings->total() }} booking</span>
            {{ $bookings->links() }}
        </div>
    </div>

@endsection
