<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Models\Barber;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $totalBookings = Booking::count();
       $revenue = Payment::where('status', 'paid')->sum('amount');
        $activeBarbers = Barber::where('status', 'aktif')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $recentBookings = Booking::with('customer', 'barber', 'service')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalBookings',
            'revenue',
            'activeBarbers',
            'totalCustomers',
            'recentBookings'
        ));
    }
}