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
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->get();
        $services = Service::active()->get();

        return view('customer.dashboard', compact('barbers', 'services'));
    }
}
