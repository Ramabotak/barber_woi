<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $revenue = Payment::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate . ' 23:59:59'])->sum('amount');

        $bookingCountByStatus = Booking::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');

        $popularServices = Booking::join('services', 'services.id', '=', 'bookings.service_id')
            ->whereBetween('bookings.created_at', [$startDate, $endDate . ' 23:59:59'])
            ->select('services.service_name', DB::raw('count(*) as total_booking'))
            ->groupBy('services.id', 'services.service_name')
            ->orderByDesc('total_booking')->limit(10)->get();

        return view('admin.reports.index', compact('revenue', 'bookingCountByStatus', 'popularServices', 'startDate', 'endDate'));
    }
}
