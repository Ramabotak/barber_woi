@extends('layouts.barber')

@section('title', 'Jadwal Saya - Barber Woi')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Jadwal Saya</h1>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 text-red-700 p-3 rounded mb-4">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Form tambah jadwal --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="font-semibold text-brand-navy mb-4">Tambah Jadwal Baru</h2>
        <form action="{{ route('barber.schedule.store') }}" method="POST" class="flex flex-wrap gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal</label>
                <input type="date" name="date" required min="{{ now()->toDateString() }}"
                       class="border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Jam Mulai</label>
                <input type="time" name="start_time" required class="border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Jam Selesai</label>
                <input type="time" name="end_time" required class="border rounded-lg px-3 py-2 text-sm">
            </div>
            <button type="submit" class="bg-brand-gold text-brand-navy px-5 py-2 rounded-lg text-sm font-semibold hover:bg-amber-400">
                + Tambah
            </button>
        </form>
        <p class="text-xs text-gray-400 mt-2">Jadwal yang ditambahkan otomatis berstatus "tersedia" dan langsung bisa dipilih customer.</p>
    </div>

    {{-- Daftar jadwal --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left p-3">Tanggal</th>
                    <th class="text-left p-3">Jam</th>
                    <th class="text-left p-3">Status</th>
                    <th class="text-left p-3">Booking</th>
                    <th class="text-left p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $schedule)
                    <tr class="border-b">
                        <td class="p-3">{{ \Carbon\Carbon::parse($schedule->date)->translatedFormat('d M Y (D)') }}</td>
                        <td class="p-3">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($schedule->status == 'tersedia') bg-green-100 text-green-700
                                @elseif($schedule->status == 'penuh') bg-amber-100 text-amber-800
                                @else bg-gray-100 text-gray-600 @endif">
                                {{ ucfirst($schedule->status) }}
                            </span>
                        </td>
                        <td class="p-3">{{ $schedule->bookings_count }} booking</td>
                        <td class="p-3 space-x-2 whitespace-nowrap">
                            @if($schedule->status !== 'libur')
                                <form action="{{ route('barber.schedule.close', $schedule) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-amber-600 hover:underline text-xs"
                                            onclick="return confirm('Tutup jadwal ini?')">Tutup</button>
                                </form>
                            @endif
                            @if($schedule->bookings_count === 0)
                                <form action="{{ route('barber.schedule.destroy', $schedule) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline text-xs"
                                            onclick="return confirm('Hapus jadwal ini?')">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-500">Belum ada jadwal. Tambahkan lewat form di atas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
