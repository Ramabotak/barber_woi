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
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        @forelse($barbers as $barber)
            <div class="bg-white rounded-xl shadow overflow-hidden">
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
                    <p class="text-xs text-gray-500 line-clamp-2">{{ $barber->experience ?? 'Barber profesional' }}</p>
                </div>
            </div>
        @empty
            <p class="col-span-full text-gray-400 text-sm">Belum ada barber aktif saat ini.</p>
        @endforelse
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
