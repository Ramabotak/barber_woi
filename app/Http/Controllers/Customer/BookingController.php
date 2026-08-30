<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Service;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}

    public function create(): View
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

        return view('customer.booking.create', compact('barbers', 'services'));
    }

    // Dipanggil via fetch/AJAX setelah customer pilih barber, untuk ambil jadwal tersedia.
    public function availableSchedules(Request $request, Barber $barber)
    {
        $validated = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
        ]);
        $service = Service::active()->findOrFail($validated['service_id']);

        $schedules = Schedule::where('barber_id', $barber->id)
            ->available()
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')->orderBy('start_time')
            ->get(['id', 'date', 'start_time', 'end_time']);

        return response()->json($schedules->map(fn (Schedule $schedule) => [
            'id' => $schedule->id,
            'date' => $schedule->date->format('Y-m-d'),
            'date_label' => $schedule->date->locale('id')->translatedFormat('l, d M Y'),
            'locked_slots' => max((int) ceil($service->duration / BookingService::SLOT_MINUTES), 1),
            'slots' => $this->bookingService->slots($schedule, $service),
        ]));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'barber_id' => ['required', 'exists:barbers,id'],
            'service_id' => ['required', 'exists:services,id'],
            'schedule_id' => ['required', 'exists:schedules,id'],
            'slot_time' => ['required', 'date_format:H:i'],
        ]);

        $booking = $this->bookingService->createBooking(
            customerId: $request->user()->id,
            barberId: $validated['barber_id'],
            serviceId: $validated['service_id'],
            scheduleId: $validated['schedule_id'],
            slotTime: $validated['slot_time'],
        );

        // Midtrans sudah aktif, arahkan ke halaman pembayaran Snap.
        return redirect()->route('customer.payment.show', $booking)
            ->with('success', "Booking {$booking->booking_code} berhasil dibuat. Silakan lanjutkan pembayaran.");
    }

    public function index(Request $request): View
    {
        $customerId = $request->user()->id;

        $activeBookings = Booking::with(['barber.user', 'service', 'schedule'])
            ->where('customer_id', $customerId)->active()
            ->orderByDesc('created_at')->get();

        $historyBookings = Booking::with(['barber.user', 'service', 'schedule', 'review'])
            ->where('customer_id', $customerId)->history()
            ->orderByDesc('created_at')->get();

        return view('customer.booking.index', compact('activeBookings', 'historyBookings'));
    }

    public function show(Request $request, Booking $booking): View
    {
        abort_unless($booking->customer_id === $request->user()->id, 403);
        $booking->load(['barber.user', 'service', 'schedule', 'payment', 'review']);

        return view('customer.booking.show', compact('booking'));
    }
}
