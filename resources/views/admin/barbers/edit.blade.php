@extends('layouts.admin')

@section('title', 'Edit Barber - Barber Woi')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Edit Barber</h1>

    <div class="bg-white rounded-lg shadow p-6 max-w-lg">
        @if($barber->photo)
            <img src="{{ Storage::url($barber->photo) }}" alt="{{ $barber->user->name }}"
                 class="w-24 h-24 rounded-full object-cover mb-4">
        @endif

        <form action="{{ route('admin.barbers.update', $barber) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium">Nama</label>
                <input type="text" name="name" value="{{ old('name', $barber->user->name) }}" required
                       class="w-full border rounded-lg px-3 py-2">
                @error('name')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email', $barber->user->email) }}" required
                       class="w-full border rounded-lg px-3 py-2">
                @error('email')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Password Baru (opsional)</label>
                <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah"
                       class="w-full border rounded-lg px-3 py-2">
                @error('password')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Nomor Telepon</label>
                <input type="text" name="phone_number" value="{{ old('phone_number', $barber->user->phone_number) }}"
                       class="w-full border rounded-lg px-3 py-2">
                @error('phone_number')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Pengalaman</label>
                <textarea name="experience" rows="3"
                          class="w-full border rounded-lg px-3 py-2">{{ old('experience', $barber->experience) }}</textarea>
                @error('experience')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Ganti Foto (opsional)</label>
                <input type="file" name="photo" accept="image/*"
                       class="w-full border rounded-lg px-3 py-2">
                @error('photo')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Status</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2">
                    <option value="aktif" @selected(old('status', $barber->status) === 'aktif')>Aktif</option>
                    <option value="libur" @selected(old('status', $barber->status) === 'libur')>Libur</option>
                    <option value="cuti" @selected(old('status', $barber->status) === 'cuti')>Cuti</option>
                    <option value="nonaktif" @selected(old('status', $barber->status) === 'nonaktif')>Nonaktif</option>
                </select>
                @error('status')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="bg-brand-gold text-brand-navy px-6 py-2 rounded-lg font-semibold hover:bg-amber-400">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.barbers.index') }}" class="px-6 py-2 rounded-lg border">Batal</a>
            </div>
        </form>
    </div>
@endsection
