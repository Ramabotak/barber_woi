<x-app-layout>
    <div class="p-6">
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

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left p-3">Nama Layanan</th>
                        <th class="text-left p-3">Harga</th>
                        <th class="text-left p-3">Durasi (menit)</th>
                        <th class="text-left p-3">Status</th>
                        <th class="text-left p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $service)
                        <tr class="border-b">
                            <td class="p-3">{{ $service->service_name }}</td>
                            <td class="p-3">Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                            <td class="p-3">{{ $service->duration }} menit</td>
                            <td class="p-3">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($service->status == 'active') bg-green-100 text-green-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ $service->status == 'active' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-3 space-x-2">
                                <a href="{{ route('admin.services.edit', $service) }}" 
                                   class="text-blue-600 hover:underline">Edit</a>
                                <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline" 
                                            onclick="return confirm('Hapus layanan ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-3">{{ $services->links() }}</div>
        </div>
    </div>
</x-app-layout>