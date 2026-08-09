@extends('layouts.admin')

@section('title', 'Laporan - Barber Woi')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Laporan</h1>

    <form method="GET" class="bg-white rounded-lg shadow p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <button type="submit" class="bg-brand-navy text-white px-4 py-2 rounded-lg text-sm font-semibold">Terapkan</button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500">Pendapatan Periode Ini</p>
            <p class="text-3xl font-bold text-brand-gold">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 md:col-span-2">
            <p class="text-sm text-gray-500 mb-3">Booking per Status</p>
            <div class="flex flex-wrap gap-2">
                @forelse($bookingCountByStatus as $status => $total)
                    <span class="px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-700">
                        {{ ucfirst($status) }}: <strong>{{ $total }}</strong>
                    </span>
                @empty
                    <span class="text-gray-400 text-sm">Tidak ada data pada periode ini.</span>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="font-semibold mb-4 text-brand-navy">Layanan Terpopuler</h2>
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">Layanan</th>
                    <th class="text-left py-2">Jumlah Booking</th>
                </tr>
            </thead>
            <tbody>
                @forelse($popularServices as $service)
                    <tr class="border-b">
                        <td class="py-2">{{ $service->service_name }}</td>
                        <td class="py-2">{{ $service->total_booking }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="py-4 text-center text-gray-500">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
