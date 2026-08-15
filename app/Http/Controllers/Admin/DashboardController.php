<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalBookings = Booking::count();
        $revenue = Payment::where('status', 'paid')->sum('amount');
        $activeBarbers = Barber::where('status', 'aktif')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $recentBookings = Booking::with('customer', 'barber.user', 'service')->latest()->take(5)->get();

        // Data grafik: pendapatan 7 hari terakhir
        $revenueChartLabels = [];
        $revenueChartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $revenueChartLabels[] = $date->translatedFormat('d M');
            $revenueChartData[] = (float) Payment::where('status', 'paid')
                ->whereDate('paid_at', $date)
                ->sum('amount');
        }

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
            'revenueChartLabels',
            'revenueChartData',
            'statusChartLabels',
            'statusChartData'
        ));
    }
}
