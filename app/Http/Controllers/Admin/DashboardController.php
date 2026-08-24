<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $selectedYear = (int) $request->integer('year', now()->year);
        $selectedMonth = (int) $request->integer('month', now()->month);

        if ($selectedYear < 2000 || $selectedYear > now()->year + 1) {
            $selectedYear = now()->year;
        }

        if ($selectedMonth < 1 || $selectedMonth > 12) {
            $selectedMonth = now()->month;
        }

        $chartStartDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfDay();
        $chartEndDate = $chartStartDate->copy()->endOfMonth();
        $availableYears = Payment::whereNotNull('paid_at')
            ->selectRaw('YEAR(paid_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($year) => (int) $year)
            ->push(now()->year)
            ->unique()
            ->sortDesc()
            ->values();

        $totalBookings = Booking::count();
        $revenue = Payment::where('status', 'paid')->sum('amount');
        $activeBarbers = Barber::where('status', 'aktif')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $recentBookings = Booking::with('customer', 'barber.user', 'service')->latest()->take(5)->get();

        // Data grafik: pendapatan harian pada bulan dan tahun yang dipilih.
        $revenueChartLabels = [];
        $revenueChartData = [];
        for ($date = $chartStartDate->copy(); $date->lte($chartEndDate); $date->addDay()) {
            $revenueChartLabels[] = $date->translatedFormat('d M');
            $revenueChartData[] = (float) Payment::where('status', 'paid')
                ->whereDate('paid_at', $date)
                ->sum('amount');
        }
        $hasRevenueData = collect($revenueChartData)->contains(fn ($amount) => $amount > 0);

        // Data grafik: distribusi status booking (30 hari terakhir)
        $statusCounts = Booking::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusLabels = ['pending', 'paid', 'accepted', 'waiting', 'late', 'serving', 'completed', 'cancelled'];
        $statusChartLabels = array_map('ucfirst', $statusLabels);
        $statusChartData = array_map(fn ($status) => (int) ($statusCounts[$status] ?? 0), $statusLabels);

        return view('admin.dashboard', compact(
            'totalBookings',
            'revenue',
            'activeBarbers',
            'totalCustomers',
            'recentBookings',
            'selectedYear',
            'selectedMonth',
            'availableYears',
            'hasRevenueData',
            'revenueChartLabels',
            'revenueChartData',
            'statusChartLabels',
            'statusChartData'
        ));
    }
}
