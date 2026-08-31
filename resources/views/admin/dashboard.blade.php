@extends('layouts.admin')

@section('title', 'Dashboard Admin - Barber Woi')

@section('content')

    {{-- Page Header --}}
    <div>
        <h2 class="font-heading text-2xl font-bold text-charcoal">Dashboard Overview</h2>
        <p class="text-sm text-muted mt-1">Selamat datang kembali, Admin. Ini yang terjadi hari ini.</p>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white border border-gray-200 rounded-xl p-5 relative overflow-hidden group hover:border-gold/50 transition-colors">
            <span class="material-symbols-outlined absolute top-4 right-4 text-4xl text-gold/20 group-hover:text-gold/30 transition-colors">event_available</span>
            <p class="text-[11px] font-semibold text-muted uppercase tracking-wider mb-1">Total Booking</p>
            <h3 class="font-heading text-3xl font-bold text-charcoal">{{ number_format($totalBookings, 0, ',', '.') }}</h3>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5 relative overflow-hidden group hover:border-gold/50 transition-colors">
            <span class="material-symbols-outlined absolute top-4 right-4 text-4xl text-gold/20 group-hover:text-gold/30 transition-colors">payments</span>
            <p class="text-[11px] font-semibold text-muted uppercase tracking-wider mb-1">Pendapatan</p>
            <h3 class="font-heading text-3xl font-bold text-charcoal">Rp {{ number_format($revenue, 0, ',', '.') }}</h3>
        </div>

        <div class="bg-charcoal rounded-xl p-5 relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-gold/10 to-transparent"></div>
            <span class="material-symbols-outlined absolute top-4 right-4 text-4xl text-gold/30">content_cut</span>
            <p class="relative text-[11px] font-semibold text-cream/50 uppercase tracking-wider mb-1">Barber Aktif</p>
            <h3 class="relative font-heading text-3xl font-bold text-gold">{{ $activeBarbers }}</h3>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5 relative overflow-hidden group hover:border-gold/50 transition-colors">
            <span class="material-symbols-outlined absolute top-4 right-4 text-4xl text-gold/20 group-hover:text-gold/30 transition-colors">groups</span>
            <p class="text-[11px] font-semibold text-muted uppercase tracking-wider mb-1">Customer</p>
            <h3 class="font-heading text-3xl font-bold text-charcoal">{{ number_format($totalCustomers, 0, ',', '.') }}</h3>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-6 lg:col-span-2 shadow-[0_1px_2px_rgba(28,28,30,0.03)]">
            <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-heading text-lg font-semibold tracking-tight text-charcoal">Ringkasan Pendapatan</h3>
                    <p class="mt-1 text-xs text-muted">Pendapatan harian untuk {{ \Carbon\Carbon::create($selectedYear, $selectedMonth)->translatedFormat('F Y') }}</p>
                </div>
                <form method="GET" action="{{ route('admin.dashboard') }}" class="inline-flex w-fit items-center gap-1 rounded-lg border border-gray-200 bg-gray-50/70 p-1">
                    <label for="revenue-month" class="sr-only">Bulan pendapatan</label>
                    <select id="revenue-month" name="month" onchange="this.form.submit()" class="rounded-md border-0 bg-transparent py-1.5 pl-2 pr-7 text-xs font-semibold text-charcoal focus:ring-0">
                        @foreach(range(1, 12) as $month)
                            <option value="{{ $month }}" @selected($selectedMonth === $month)>{{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                    <span class="h-5 w-px bg-gray-200"></span>
                    <label for="revenue-year" class="sr-only">Tahun pendapatan</label>
                    <select id="revenue-year" name="year" onchange="this.form.submit()" class="rounded-md border-0 bg-transparent py-1.5 pl-2 pr-7 text-xs font-semibold text-charcoal focus:ring-0">
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" @selected($selectedYear === $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                    <noscript><button type="submit" class="rounded-md bg-charcoal px-3 py-1.5 text-xs font-medium text-white">Tampilkan</button></noscript>
                </form>
            </div>
            <div class="relative h-72 w-full">
                <canvas id="revenueChart"></canvas>
                @unless($hasRevenueData)
                    <div class="pointer-events-none absolute inset-0 flex items-center justify-center pb-8">
                        <div class="rounded-lg border border-gold/20 bg-white/90 px-4 py-3 text-center shadow-sm">
                            <span class="material-symbols-outlined block text-gold/70">payments</span>
                            <p class="mt-1 text-xs font-semibold text-charcoal">Belum ada pendapatan</p>
                            <p class="mt-0.5 text-[11px] text-muted">Data akan muncul setelah pembayaran berhasil.</p>
                        </div>
                    </div>
                @endunless
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <h3 class="font-heading text-base font-semibold text-charcoal mb-6">Status Booking (30 Hari)</h3>
            <div class="h-64 w-full">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Recent Bookings --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="p-5 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-heading text-base font-semibold text-charcoal">Booking Terbaru</h3>
            <a href="{{ route('admin.bookings.index') }}" class="text-gold text-sm font-semibold hover:underline">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="p-4 text-[11px] font-semibold text-muted uppercase tracking-wider">Kode</th>
                        <th class="p-4 text-[11px] font-semibold text-muted uppercase tracking-wider">Customer</th>
                        <th class="p-4 text-[11px] font-semibold text-muted uppercase tracking-wider">Barber</th>
                        <th class="p-4 text-[11px] font-semibold text-muted uppercase tracking-wider">Layanan</th>
                        <th class="p-4 text-[11px] font-semibold text-muted uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-charcoal divide-y divide-gray-100">
                    @forelse($recentBookings as $booking)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 font-medium">{{ $booking->booking_code }}</td>
                            <td class="p-4">{{ $booking->customer->name ?? '-' }}</td>
                            <td class="p-4 text-muted">{{ $booking->barber->user->name ?? '-' }}</td>
                            <td class="p-4 text-muted">{{ $booking->service->service_name ?? '-' }}</td>
                            <td class="p-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border
                                    @if($booking->status == 'completed') bg-gray-100 text-gray-600 border-gray-200
                                    @elseif($booking->status == 'cancelled') bg-red-50 text-branddanger border-red-200
                                    @elseif($booking->status == 'serving') bg-blue-50 text-blue-600 border-blue-200
                                    @elseif($booking->status == 'paid') bg-brandsuccess/10 text-brandsuccess border-brandsuccess/20
                                    @else bg-gold/10 text-brandwarning border-gold/20 @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-muted">Belum ada booking</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
    function renderAdminDashboardCharts() {
        if (typeof Chart === 'undefined') {
            setTimeout(renderAdminDashboardCharts, 50);
            return;
        }

        const revenueCtx = document.getElementById('revenueChart');
        if (!revenueCtx) return;

        const formatRupiah = (amount) => 'Rp ' + Number(amount).toLocaleString('id-ID', {
            maximumFractionDigits: 0,
        });
        const chartContext = revenueCtx.getContext('2d');
        const gradient = chartContext.createLinearGradient(0, 0, 0, 288);
        gradient.addColorStop(0, 'rgba(201, 162, 75, 0.28)');
        gradient.addColorStop(0.65, 'rgba(201, 162, 75, 0.08)');
        gradient.addColorStop(1, 'rgba(201, 162, 75, 0)');

        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($revenueChartLabels),
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: @json($revenueChartData),
                    borderColor: '#1C1C1E',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#C9A24B',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: (context) => context.raw > 0 ? 3.5 : 0,
                    pointHoverRadius: 6,
                    pointHitRadius: 16,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1C1C1E',
                        titleFont: { family: 'Inter', size: 12 },
                        bodyFont: { family: 'Plus Jakarta Sans', size: 13, weight: 'bold' },
                        padding: 12,
                        cornerRadius: 6,
                        displayColors: false,
                        callbacks: {
                            label: (context) => formatRupiah(context.parsed.y)
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        border: { display: false },
                        ticks: {
                            autoSkip: true,
                            maxTicksLimit: 8,
                            maxRotation: 0,
                            font: { family: 'Inter', size: 11 },
                            color: '#8A8A8E',
                            padding: 10,
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: @json($hasRevenueData) ? undefined : 100000,
                        border: { display: false },
                        grid: { color: '#ECECEC', drawBorder: false },
                        ticks: {
                            font: { family: 'Inter', size: 11 },
                            color: '#8A8A8E',
                            stepSize: @json($hasRevenueData) ? undefined : 20000,
                            callback: (value) => formatRupiah(value),
                            padding: 8,
                        }
                    }
                },
                interaction: { intersect: false, mode: 'index' },
                layout: { padding: { top: 4, right: 6 } },
            }
        });

        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($statusChartLabels),
                    datasets: [{
                        data: @json($statusChartData),
                        backgroundColor: [
                            '#9CA3AF', // pending
                            '#34D399', // paid
                            '#60A5FA', // accepted
                            '#C9A24B', // waiting
                            '#F87171', // late
                            '#3B82F6', // serving
                            '#1C1C1E', // completed
                            '#EF4444', // cancelled
                        ],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 10, font: { family: 'Inter', size: 11 }, color: '#46464a' }
                        }
                    }
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', renderAdminDashboardCharts);
    } else {
        renderAdminDashboardCharts();
    }
    </script>
    @endpush
@endsection
