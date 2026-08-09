@extends('layouts.admin')

@section('title', 'Daftar Barber - Barber Woi')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Daftar Barber</h1>
        <a href="{{ route('admin.barbers.create') }}" 
           class="bg-brand-gold text-brand-navy px-4 py-2 rounded-lg font-semibold hover:bg-amber-400">
            + Tambah Barber
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @forelse($barbers as $barber)
            <div class="bg-white rounded-xl shadow overflow-hidden flex flex-col">
                <div class="h-40 bg-gray-100 flex items-center justify-center">
                    @if($barber->photo)
                        <img src="{{ Storage::url($barber->photo) }}" alt="{{ $barber->user->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-20 h-20 rounded-full bg-brand-navy text-white flex items-center justify-center text-2xl font-bold">
                            {{ strtoupper(substr($barber->user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="p-4 flex-1 flex flex-col">
                    <div class="flex items-start justify-between gap-2 mb-1">
                        <h3 class="font-semibold text-brand-navy">{{ $barber->user->name }}</h3>
                        <span class="shrink-0 px-2 py-1 text-xs rounded-full
                            @if($barber->status == 'aktif') bg-green-100 text-green-700
                            @elseif($barber->status == 'libur') bg-yellow-100 text-yellow-700
                            @elseif($barber->status == 'cuti') bg-blue-100 text-blue-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ ucfirst($barber->status) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500">{{ $barber->user->email }}</p>
                    <p class="text-xs text-gray-500 mb-3">{{ $barber->user->phone_number ?? '-' }}</p>
                    @if($barber->experience)
                        <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ $barber->experience }}</p>
                    @endif

                    <div class="mt-auto flex gap-2 pt-2 border-t">
                        <a href="{{ route('admin.barbers.edit', $barber) }}" 
                           class="flex-1 text-center text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg">
                            Edit
                        </a>
                        <form action="{{ route('admin.barbers.destroy', $barber) }}" method="POST" class="flex-1">
                            @csrf @method('DELETE')
                            <button type="submit" 
                                    class="w-full text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded-lg"
                                    onclick="return confirm('Hapus barber ini? Akun login-nya juga akan terhapus.')">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-gray-500">Belum ada barber.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $barbers->links() }}</div>
@endsection
