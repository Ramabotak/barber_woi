<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        // Satu kata kunci dapat dipakai untuk menemukan barber maupun layanan.
        // Batas panjang menjaga query tetap ringan dan nyaman digunakan.
        $search = mb_substr($search, 0, 100);

        $barbers = Barber::active()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($barberQuery) use ($search) {
                    $barberQuery->where('experience', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->with('user')
            ->with(['schedules' => fn ($q) => $q
                ->where('date', '>=', now()->toDateString())
                ->where('status', 'tersedia')
                ->orderBy('date')
                ->orderBy('start_time'),
            ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->get();
        $services = Service::active()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($serviceQuery) use ($search) {
                    $serviceQuery->where('service_name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->get();

        // Saran berasal dari data aktif, sehingga setiap saran pasti relevan
        // dengan pilihan yang bisa dipesan pelanggan.
        $searchSuggestions = Barber::active()
            ->with('user:id,name')
            ->get()
            ->pluck('user.name')
            ->filter()
            ->merge(Service::active()->pluck('service_name'))
            ->unique()
            ->take(8)
            ->values();

        $activeBooking = Booking::query()
            ->where('customer_id', auth()->id())
            ->active()
            ->with(['barber.user', 'service', 'schedule', 'payment'])
            ->latest()
            ->first();

        return view('customer.dashboard', compact('barbers', 'services', 'activeBooking', 'search', 'searchSuggestions'));
    }
}
