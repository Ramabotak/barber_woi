<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $barber = $request->user()->barber;
        abort_unless($barber, 403, 'Akun Anda belum terhubung dengan data barber.');

        $pendingBookings = Booking::with(['customer', 'service', 'schedule'])
            ->where('barber_id', $barber->id)->where('status', 'pending')
            ->orderBy('created_at')->get();

        $servingBookings = Booking::with(['customer', 'service', 'schedule'])
            ->where('barber_id', $barber->id)->where('status', 'serving')->get();

        $todaySchedules = Schedule::where('barber_id', $barber->id)
            ->whereDate('date', now()->toDateString())->orderBy('start_time')->get();

        return view('barber.dashboard', compact('pendingBookings', 'servingBookings', 'todaySchedules', 'barber'));
    }
}
