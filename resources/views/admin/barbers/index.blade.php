@extends('layouts.admin')

@section('title', 'Kelola Barber - Barber Woi')

@section('content')

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="font-heading text-2xl md:text-3xl font-bold text-charcoal">Kelola Barber</h2>
            <p class="text-gray-500 mt-1">Lihat, tambah, dan kelola tim barber profesional Anda</p>
        </div>
        <a href="{{ route('admin.barbers.create') }}"
           class="bg-gold hover:bg-gold/90 text-charcoal font-bold py-3 px-5 rounded-lg shadow-sm transition-all active:scale-95 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Barber
        </a>
    </div>

    @if (session('success'))
        <div class="bg-brandsuccess/10 border border-brandsuccess text-brandsuccess p-3 rounded-lg mb-6 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Barber Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($barbers as $barber)
            @php
                $statusMap = [
                    'aktif'    => ['label' => 'Aktif',     'badge' => 'bg-gold text-charcoal',        'dot' => 'bg-charcoal', 'fade' => ''],
                    'libur'    => ['label' => 'Libur',     'badge' => 'bg-brandwarning text-white',   'dot' => 'bg-white',    'fade' => 'opacity-90'],
                    'cuti'     => ['label' => 'Cuti',      'badge' => 'bg-branddanger text-white',    'dot' => 'bg-white',    'fade' => 'opacity-80 grayscale-[30%]'],
                    'nonaktif' => ['label' => 'Nonaktif',  'badge' => 'bg-gray-200 text-gray-600 border border-gray-300', 'dot' => 'bg-gray-500', 'fade' => 'opacity-60 grayscale'],
                ];
                $s = $statusMap[$barber->status] ?? $statusMap['nonaktif'];
            @endphp

            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md hover:-translate-y-1 transition-all duration-300 relative flex flex-col h-full {{ $s['fade'] }}">

                <div class="absolute top-2 right-2 z-10 {{ $s['badge'] }} text-xs px-2 py-1 rounded-full font-bold flex items-center gap-1 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }}"></span>
                    {{ $s['label'] }}
                </div>

                <div class="h-48 w-full bg-gray-100 relative overflow-hidden">
                    @if ($barber->photo)
                        <img src="{{ Storage::url($barber->photo) }}" alt="{{ $barber->user->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-charcoal">
                            <span class="text-gold text-3xl font-bold">{{ strtoupper(substr($barber->user->name, 0, 1)) }}</span>
                        </div>
                    @endif
                </div>

                <div class="p-4 flex-1 flex flex-col">
                    <h3 class="font-heading text-lg font-semibold text-charcoal mb-1">{{ $barber->user->name }}</h3>
                    <p class="text-sm text-gray-500 mb-3 line-clamp-2">{{ $barber->experience ? Str::limit($barber->experience, 40) : 'Belum ada keterangan spesialisasi' }}</p>

                    <div class="flex items-center gap-2 mt-auto text-gray-600 bg-gray-50 px-2 py-1 rounded-md w-fit mb-3">
                        <span class="material-symbols-outlined text-[16px]">workspace_premium</span>
                        <span class="text-xs">{{ $barber->experience ? Str::before($barber->experience, ' ') : '-' }} Pengalaman</span>
                    </div>
                </div>

                <div class="border-t border-gray-200 px-4 py-2 flex justify-between bg-gray-50/50">
                    <a href="{{ route('admin.barbers.edit', $barber) }}"
                       class="flex items-center gap-1 text-gray-600 hover:text-charcoal transition-colors py-1 text-sm">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                        Edit
                    </a>
                    <div class="w-px bg-gray-200 mx-1"></div>
                    <form action="{{ route('admin.barbers.destroy', $barber) }}" method="POST"
                          onsubmit="return confirm('Hapus barber ini? Akun login-nya juga akan terhapus.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="flex items-center gap-1 text-branddanger hover:text-branddanger/80 transition-colors py-1 text-sm">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-gray-500">
                Belum ada barber. Klik "Tambah Barber" untuk menambahkan.
            </div>
        @endforelse
    </div>

    <div class="mt-8">{{ $barbers->links() }}</div>

@endsection