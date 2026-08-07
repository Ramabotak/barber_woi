<x-app-layout>
    <div class="p-6 max-w-lg">
        <h1 class="text-2xl font-bold mb-4">Tambah Layanan Baru</h1>

        <form action="{{ route('admin.services.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium">Nama Layanan</label>
                <input type="text" name="service_name" value="{{ old('service_name') }}" required 
                       class="w-full border rounded-lg px-3 py-2">
                @error('service_name')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Harga (Rp)</label>
                <input type="number" name="price" value="{{ old('price') }}" required 
                       class="w-full border rounded-lg px-3 py-2">
                @error('price')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Durasi (menit)</label>
                <input type="number" name="duration" value="{{ old('duration') }}" required 
                       class="w-full border rounded-lg px-3 py-2">
                @error('duration')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Deskripsi</label>
                <textarea name="description" rows="3" 
                          class="w-full border rounded-lg px-3 py-2">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium">Status</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2">
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
                @error('status')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
            </div>
            <button type="submit" 
                    class="bg-brand-gold text-brand-navy px-6 py-2 rounded-lg font-semibold">
                Simpan
            </button>
        </form>
    </div>
</x-app-layout>