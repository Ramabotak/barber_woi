@extends('layouts.admin')

@section('title', 'Edit Barber - Barber Woi')

@section('content')

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.barbers.index') }}" class="text-gray-500 hover:text-charcoal transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="font-heading text-2xl font-bold text-charcoal">Edit Barber</h2>
            <p class="text-gray-500 text-sm">Perbarui data & profil {{ $barber->user->name }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8 max-w-2xl">

        <div class="flex items-center gap-4 mb-6">
            @if ($barber->photo)
                <img src="{{ Storage::url($barber->photo) }}" alt="{{ $barber->user->name }}"
                     class="w-20 h-20 rounded-full object-cover border-2 border-gold/40">
            @else
                <div class="w-20 h-20 rounded-full bg-charcoal text-gold flex items-center justify-center text-2xl font-bold">
                    {{ strtoupper(substr($barber->user->name, 0, 1)) }}
                </div>
            @endif
            <div>
                <p class="font-semibold text-charcoal">{{ $barber->user->name }}</p>
                <p class="text-sm text-gray-500">{{ $barber->user->email }}</p>
            </div>
        </div>

        <form action="{{ route('admin.barbers.update', $barber) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name', $barber->user->name) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-colors">
                @error('name')<p class="text-branddanger text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $barber->user->email) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-colors">
                @error('email')<p class="text-branddanger text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru (opsional)</label>
                <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-colors">
                @error('password')<p class="text-branddanger text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                <input type="text" name="phone_number" value="{{ old('phone_number', $barber->user->phone_number) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-colors">
                @error('phone_number')<p class="text-branddanger text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pengalaman</label>
                <textarea name="experience" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-colors">{{ old('experience', $barber->experience) }}</textarea>
                @error('experience')<p class="text-branddanger text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Foto (opsional)</label>
                <input type="file" name="photo" accept="image/*"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-gray-100 file:text-sm file:font-medium file:text-charcoal hover:file:bg-gray-200">
                @error('photo')<p class="text-branddanger text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-colors">
                    <option value="aktif" @selected(old('status', $barber->status) === 'aktif')>Aktif</option>
                    <option value="libur" @selected(old('status', $barber->status) === 'libur')>Libur</option>
                    <option value="cuti" @selected(old('status', $barber->status) === 'cuti')>Cuti</option>
                    <option value="nonaktif" @selected(old('status', $barber->status) === 'nonaktif')>Nonaktif</option>
                </select>
                @error('status')<p class="text-branddanger text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-gold hover:bg-gold/90 text-charcoal px-6 py-2.5 rounded-lg font-bold transition-colors">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.barbers.index') }}"
                   class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

@endsection