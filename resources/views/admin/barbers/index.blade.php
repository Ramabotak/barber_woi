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

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left p-3">Nama</th>
                    <th class="text-left p-3">Email</th>
                    <th class="text-left p-3">Telepon</th>
                    <th class="text-left p-3">Status</th>
                    <th class="text-left p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barbers as $barber)
                    <tr class="border-b">
                        <td class="p-3">{{ $barber->user->name }}</td>
                        <td class="p-3">{{ $barber->user->email }}</td>
                        <td class="p-3">{{ $barber->user->phone ?? '-' }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($barber->status == 'aktif') bg-green-100 text-green-700
                                @elseif($barber->status == 'libur') bg-yellow-100 text-yellow-700
                                @else bg-gray-100 text-gray-700 @endif">
                                {{ $barber->status }}
                            </span>
                        </td>
                        <td class="p-3 space-x-2">
                            <a href="{{ route('admin.barbers.edit', $barber) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form action="{{ route('admin.barbers.destroy', $barber) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Hapus barber?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-3">{{ $barbers->links() }}</div>
    </div>
@endsection