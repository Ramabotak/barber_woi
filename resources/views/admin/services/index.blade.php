@extends('layouts.admin')

@section('title', 'Kelola Layanan - Barber Woi')

@section('content')

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="font-heading text-2xl md:text-3xl font-bold text-charcoal">Kelola Layanan</h2>
            <p class="text-gray-500 mt-1">Kelola daftar layanan, harga, dan durasi barbershop Anda</p>
        </div>
        <a href="{{ route('admin.services.create') }}"
           class="bg-gold hover:bg-gold/90 text-charcoal font-bold py-3 px-5 rounded-lg shadow-sm transition-all active:scale-95 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Layanan
        </a>
    </div>

    @if (session('success'))
        <div class="bg-brandsuccess/10 border border-brandsuccess text-brandsuccess p-3 rounded-lg mb-6 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Statistics Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-gold/15 flex items-center justify-center text-gold">
                <span class="material-symbols-outlined">check_circle</span>
            </div>
            <div>
                <p class="text-sm text-gray-500">Layanan Aktif</p>
                <p class="font-heading text-2xl font-bold text-charcoal">{{ $activeCount }}</p>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-500">
                <span class="material-symbols-outlined">visibility_off</span>
            </div>
            <div>
                <p class="text-sm text-gray-500">Layanan Nonaktif</p>
                <p class="font-heading text-2xl font-bold text-charcoal">{{ $inactiveCount }}</p>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-500">
                <span class="material-symbols-outlined">local_offer</span>
            </div>
            <div>
                <p class="text-sm text-gray-500">Rata-rata Harga</p>
                <p class="font-heading text-2xl font-bold text-charcoal">Rp {{ number_format($avgPrice / 1000, 0, ',', '.') }}k</p>
            </div>
        </div>
    </div>

    {{-- Service Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse ($services as $service)
            @php $isActive = $service->status === 'active'; @endphp
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col group {{ !$isActive ? 'opacity-75' : '' }}">

                <div class="h-40 w-full relative overflow-hidden bg-gray-100">
                    @if ($service->photo)
                        <img src="{{ Storage::url($service->photo) }}" alt="{{ $service->service_name }}"
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105 {{ !$isActive ? 'grayscale' : '' }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <span class="material-symbols-outlined text-4xl">content_cut</span>
                        </div>
                    @endif

                    <div class="absolute top-2 right-2 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider
                        {{ $isActive ? 'bg-white border border-gold text-gold' : 'bg-gray-100 text-gray-500' }}">
                        {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                    </div>
                </div>

                <div class="p-4 flex-1 flex flex-col">
                    <h3 class="font-semibold text-charcoal mb-1">{{ $service->service_name }}</h3>
                    <p class="text-sm text-gray-500 line-clamp-2 mb-3">{{ $service->description ?? 'Tidak ada deskripsi' }}</p>

                    <div class="mt-auto">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold {{ $isActive ? 'text-gold' : 'text-gray-500' }}">
                                Rp {{ number_format($service->price, 0, ',', '.') }}
                            </span>
                            <div class="flex items-center text-gray-500 gap-1 text-xs">
                                <span class="material-symbols-outlined text-[16px]">schedule</span>
                                {{ $service->duration }} menit
                            </div>
                        </div>
                        <hr class="border-gray-200 my-2">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.services.edit', $service) }}"
                               class="p-1.5 text-gray-500 hover:text-charcoal transition-colors rounded hover:bg-gray-100">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </a>
                            <form action="{{ route('admin.services.destroy', $service) }}" method="POST"
                                  onsubmit="return confirm('Hapus layanan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-500 hover:text-branddanger transition-colors rounded hover:bg-branddanger/10">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-gray-500">
                Belum ada layanan. Klik "Tambah Layanan" untuk menambahkan.
            </div>
        @endforelse
    </div>

    <div class="mt-8">{{ $services->links() }}</div>

@endsection