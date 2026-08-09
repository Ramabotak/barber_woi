@extends('layouts.admin')

@section('title', 'Tambah Barber - Barber Woi')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Tambah Barber Baru</h1>

    <div class="bg-white rounded-lg shadow p-6 max-w-lg">
        <form action="{{ route('admin.barbers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full border rounded-lg px-3 py-2">
                @error('name')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border rounded-lg px-3 py-2">
                @error('email')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Password</label>
                <input type="password" name="password" required
                       class="w-full border rounded-lg px-3 py-2">
                @error('password')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Nomor Telepon</label>
                <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                       class="w-full border rounded-lg px-3 py-2">
                @error('phone_number')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Pengalaman</label>
                <textarea name="experience" rows="3"
                          class="w-full border rounded-lg px-3 py-2">{{ old('experience') }}</textarea>
                @error('experience')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Foto</label>
                <input type="file" name="photo" accept="image/*"
                       class="w-full border rounded-lg px-3 py-2">
                @error('photo')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Status</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2">
                    <option value="aktif" selected>Aktif</option>
                    <option value="libur">Libur</option>
                    <option value="cuti">Cuti</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
                @error('status')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="bg-brand-gold text-brand-navy px-6 py-2 rounded-lg font-semibold hover:bg-amber-400">
                    Simpan
                </button>
                <a href="{{ route('admin.barbers.index') }}" class="px-6 py-2 rounded-lg border">Batal</a>
            </div>
        </form>
    </div>
@endsection
