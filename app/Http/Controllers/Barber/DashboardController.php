<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $barber = $request->user()->barber;
        abort_unless($barber, 403, 'Akun Anda belum terhubung dengan data barber.');

        $pendingBookings = Booking::with(['customer', 'service', 'schedule'])
            ->where('barber_id', $barber->id)
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        $servingBookings = Booking::with(['customer', 'service', 'schedule'])
            ->where('barber_id', $barber->id)
            ->whereIn('status', ['paid', 'waiting', 'late', 'serving'])
            ->orderBy('queue_number')
            ->get();

        $todaySchedules = Schedule::where('barber_id', $barber->id)
            ->whereDate('date', today())->orderBy('start_time')->get();

        $todayBookings = Booking::where('barber_id', $barber->id)
            ->whereHas('schedule', fn ($query) => $query->whereDate('date', today()))
            ->where('status', '!=', 'cancelled')->count();
        $availableSlots = $todaySchedules->sum(fn (Schedule $schedule) => max(0, Carbon::parse($schedule->start_time)->diffInMinutes(Carbon::parse($schedule->end_time))) / 30);
        $revenueToday = Payment::where('status', 'paid')->whereHas('booking', fn ($query) => $query->where('barber_id', $barber->id))->whereDate('paid_at', today())->sum('amount');
        $yesterdayRevenue = Payment::where('status', 'paid')->whereHas('booking', fn ($query) => $query->where('barber_id', $barber->id))->whereDate('paid_at', today()->subDay())->sum('amount');
        $revenueChange = $yesterdayRevenue > 0 ? round((($revenueToday - $yesterdayRevenue) / $yesterdayRevenue) * 100) : null;
        $remainingWorkMinutes = $todaySchedules->where('status', '!=', 'libur')->sum(function (Schedule $schedule) {
            $end = Carbon::parse(today()->format('Y-m-d').' '.$schedule->end_time->format('H:i:s'));
            return $end->isFuture() ? now()->diffInMinutes($end) : 0;
        });
        $chartDates = collect(range(6, 0))->map(fn ($daysAgo) => today()->subDays($daysAgo));
        $weeklyRevenue = Payment::where('status', 'paid')->whereHas('booking', fn ($query) => $query->where('barber_id', $barber->id))
            ->whereBetween('paid_at', [$chartDates->first()->copy()->startOfDay(), today()->endOfDay()])
            ->selectRaw('DATE(paid_at) as date, SUM(amount) as total')->groupBy('date')->pluck('total', 'date');
        $chartLabels = $chartDates->map(fn (Carbon $date) => $date->isoFormat('ddd'))->values();
        $chartValues = $chartDates->map(fn (Carbon $date) => (float) ($weeklyRevenue[$date->toDateString()] ?? 0))->values();

        $reviews = Review::with(['customer'])
            ->whereHas('booking', fn ($query) => $query->where('barber_id', $barber->id))
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('barber.dashboard', compact('pendingBookings', 'servingBookings', 'todaySchedules', 'barber', 'todayBookings', 'availableSlots', 'revenueToday', 'revenueChange', 'remainingWorkMinutes', 'chartLabels', 'chartValues', 'reviews'));
    }

    public function profile(Request $request): View
    {
        $barber = $request->user()->barber;
        abort_unless($barber, 403, 'Akun Anda belum terhubung dengan data barber.');

        $reviews = Review::with(['customer'])
            ->whereHas('booking', fn ($query) => $query->where('barber_id', $barber->id))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('barber.profile.index', compact('barber', 'reviews'));
    }
}
