@extends('layouts.admin')

@section('title', 'Pengaturan - Barber Woi')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Pengaturan</h1>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 max-w-lg">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium">Nama Barbershop</label>
                <input type="text" name="shop_name" value="{{ old('shop_name', $settings['shop_name']) }}" required
                       class="w-full border rounded-lg px-3 py-2">
                @error('shop_name')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Alamat</label>
                <textarea name="shop_address" rows="2"
                          class="w-full border rounded-lg px-3 py-2">{{ old('shop_address', $settings['shop_address']) }}</textarea>
                @error('shop_address')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Nomor Telepon Barbershop</label>
                <input type="text" name="shop_phone" value="{{ old('shop_phone', $settings['shop_phone']) }}"
                       class="w-full border rounded-lg px-3 py-2">
                @error('shop_phone')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Jam Buka</label>
                    <input type="time" name="opening_time" value="{{ old('opening_time', $settings['opening_time']) }}" required
                           class="w-full border rounded-lg px-3 py-2">
                    @error('opening_time')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Jam Tutup</label>
                    <input type="time" name="closing_time" value="{{ old('closing_time', $settings['closing_time']) }}" required
                           class="w-full border rounded-lg px-3 py-2">
                    @error('closing_time')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium">Toleransi Keterlambatan (menit)</label>
                <input type="number" name="late_tolerance_minutes" value="{{ old('late_tolerance_minutes', $settings['late_tolerance_minutes']) }}" required min="0"
                       class="w-full border rounded-lg px-3 py-2">
                @error('late_tolerance_minutes')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <button type="submit"
                    class="bg-brand-gold text-brand-navy px-6 py-2 rounded-lg font-semibold hover:bg-amber-400">
                Simpan Pengaturan
            </button>
        </form>
    </div>
@endsection
