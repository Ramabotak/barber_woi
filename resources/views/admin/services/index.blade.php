@extends('layouts.admin')

@section('title', 'Daftar Layanan - Barber Woi')

@section('content')
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Daftar Layanan</h1>
            <a href="{{ route('admin.services.create') }}" 
               class="bg-brand-gold text-brand-navy px-4 py-2 rounded-lg font-semibold hover:bg-amber-400">
                + Tambah Layanan
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @forelse($services as $service)
                <div class="bg-white rounded-xl shadow overflow-hidden flex flex-col">
                    <div class="h-36 bg-gray-100 flex items-center justify-center">
                        @if($service->photo)
                            <img src="{{ Storage::url($service->photo) }}" alt="{{ $service->service_name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <span class="text-gray-300 text-sm">Tidak ada foto</span>
                        @endif
                    </div>
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h3 class="font-semibold text-brand-navy">{{ $service->service_name }}</h3>
                            <span class="shrink-0 px-2 py-1 text-xs rounded-full
                                @if($service->status == 'active') bg-green-100 text-green-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $service->status == 'active' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        <p class="text-brand-gold font-bold">Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 mb-3">{{ $service->duration }} menit</p>
                        @if($service->description)
                            <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ $service->description }}</p>
                        @endif

                        <div class="mt-auto flex gap-2 pt-2 border-t">
                            <a href="{{ route('admin.services.edit', $service) }}" 
                               class="flex-1 text-center text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg">
                                Edit
                            </a>
                            <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="flex-1">
                                @csrf @method('DELETE')
                                <button type="submit" 
                                        class="w-full text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded-lg"
                                        onclick="return confirm('Hapus layanan ini?')">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-gray-500">Belum ada layanan.</div>
            @endforelse
        </div>

        <div class="mt-6">{{ $services->links() }}</div>
@endsection
