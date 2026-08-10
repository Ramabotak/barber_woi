@extends('layouts.admin')

@section('title', 'Dashboard Admin - Barber Woi')

@section('content')
    <h1 class="text-2xl font-bold text-brand-navy mb-6">Dashboard Admin</h1>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500">Total Booking</p>
            <p class="text-3xl font-bold text-brand-navy">{{ $totalBookings }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500">Pendapatan</p>
            <p class="text-3xl font-bold text-brand-gold">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500">Barber Aktif</p>
            <p class="text-3xl font-bold text-brand-navy">{{ $activeBarbers }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
            <p class="text-sm text-gray-500">Customer</p>
            <p class="text-3xl font-bold text-brand-navy">{{ $totalCustomers }}</p>
        </div>
    </div>

    {{-- Grafik --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 lg:col-span-2">
            <h2 class="text-lg font-semibold mb-4 text-brand-navy">Pendapatan 7 Hari Terakhir</h2>
            <canvas id="revenueChart" height="110"></canvas>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <h2 class="text-lg font-semibold mb-4 text-brand-navy">Status Booking (30 Hari)</h2>
            <canvas id="statusChart" height="110"></canvas>
        </div>
    </div>

    {{-- Booking Terbaru --}}
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
        <h2 class="text-lg font-semibold mb-4 text-brand-navy">Booking Terbaru</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Kode</th>
                        <th class="text-left py-2">Customer</th>
                        <th class="text-left py-2">Barber</th>
                        <th class="text-left py-2">Layanan</th>
                        <th class="text-left py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBookings as $booking)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2">{{ $booking->booking_code }}</td>
                            <td>{{ $booking->customer->name ?? '-' }}</td>
                            <td>{{ $booking->barber->user->name ?? '-' }}</td>
                            <td>{{ $booking->service->service_name ?? '-' }}</td>
                            <td>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($booking->status == 'completed') bg-green-100 text-green-700
                                    @elseif($booking->status == 'cancelled') bg-red-100 text-red-700
                                    @elseif($booking->status == 'serving') bg-blue-100 text-blue-700
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ $booking->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-500">Belum ada booking</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const revenueCtx = document.getElementById('revenueChart');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($revenueChartLabels),
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: @json($revenueChartData),
                    borderColor: '#D4AF37',
                    backgroundColor: 'rgba(212, 175, 55, 0.15)',
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#D4AF37',
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => 'Rp ' + value.toLocaleString('id-ID')
                        }
                    }
                }
            }
        });

        const statusCtx = document.getElementById('statusChart');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($statusChartLabels),
                datasets: [{
                    data: @json($statusChartData),
                    backgroundColor: [
                        '#9CA3AF', // pending - abu
                        '#60A5FA', // accepted - biru muda
                        '#FBBF24', // waiting - kuning
                        '#F87171', // late - merah muda
                        '#3B82F6', // serving - biru
                        '#22C55E', // completed - hijau
                        '#EF4444', // cancelled - merah
                    ],
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } }
            }
        });
    });
    </script>
    @endpush
@endsection