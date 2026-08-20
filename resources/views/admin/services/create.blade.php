@extends('layouts.admin')

@section('title', 'Tambah Layanan - Barber Woi')

@section('content')

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.services.index') }}" class="text-gray-500 hover:text-charcoal transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="font-heading text-2xl font-bold text-charcoal">Tambah Layanan Baru</h2>
            <p class="text-gray-500 text-sm">Isi detail layanan yang ditawarkan barbershop</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8 max-w-2xl">
        <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Layanan</label>
                <input type="text" name="service_name" value="{{ old('service_name') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-colors">
                @error('service_name')<p class="text-branddanger text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Layanan</label>
                <input type="file" name="photo" accept="image/*"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-gray-100 file:text-sm file:font-medium file:text-charcoal hover:file:bg-gray-200">
                @error('photo')<p class="text-branddanger text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                    <input type="number" name="price" value="{{ old('price') }}" required min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-colors">
                    @error('price')<p class="text-branddanger text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (menit)</label>
                    <input type="number" name="duration" value="{{ old('duration') }}" required min="1"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-colors">
                    @error('duration')<p class="text-branddanger text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-colors">{{ old('description') }}</textarea>
                @error('description')<p class="text-branddanger text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold transition-colors">
                    <option value="active" @selected(old('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(old('status') === 'inactive')>Nonaktif</option>
                </select>
                @error('status')<p class="text-branddanger text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-gold hover:bg-gold/90 text-charcoal px-6 py-2.5 rounded-lg font-bold transition-colors">
                    Simpan
                </button>
                <a href="{{ route('admin.services.index') }}"
                   class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

@endsection