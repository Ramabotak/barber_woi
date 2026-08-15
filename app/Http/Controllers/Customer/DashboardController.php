<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\Service;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $barbers = Barber::active()->with('user')
            ->with(['schedules' => fn ($q) => $q
                ->where('date', '>=', now()->toDateString())
                ->where('status', 'tersedia')
                ->orderBy('date')
                ->orderBy('start_time'),
            ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->get();
        $services = Service::active()->get();

        return view('customer.dashboard', compact('barbers', 'services'));
    }
}
