<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected NotificationService $notificationService
    ) {
    }

    protected function authorizeOwns(Request $request, Booking $booking): void
    {
        $barber = $request->user()->barber;
        abort_unless($barber && $booking->barber_id === $barber->id, 403);
    }

    public function incoming(Request $request): View
    {
        $barber = $request->user()->barber;
        abort_unless($barber, 403);

        $bookings = Booking::with(['customer', 'service', 'schedule', 'payment'])
            ->where('barber_id', $barber->id)
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        return view('barber.booking.incoming', compact('bookings'));
    }

    public function accept(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeOwns($request, $booking);
        $this->bookingService->transitionStatus($booking, 'accepted');
        $this->notificationService->notifyBookingAccepted($booking);

        return back()->with('success', "Booking {$booking->booking_code} diterima.");
    }

    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeOwns($request, $booking);
        $this->bookingService->transitionStatus($booking, 'cancelled');
        $this->notificationService->notifyBookingRejected($booking);

        return back()->with('success', "Booking {$booking->booking_code} ditolak.");
    }

    public function activeQueue(Request $request): View
    {
        $barber = $request->user()->barber;
        abort_unless($barber, 403);

        $bookings = Booking::with(['customer', 'service', 'schedule'])
            ->where('barber_id', $barber->id)
            ->whereIn('status', ['accepted', 'waiting', 'late', 'serving'])
            ->orderBy('queue_number')->get();

        return view('barber.booking.queue', compact('bookings'));
    }

    public function startService(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeOwns($request, $booking);

        if (in_array($booking->status, ['accepted', 'paid'], true)) {
            $this->bookingService->transitionStatus($booking, 'waiting');
        }

        $booking->refresh();
        $this->bookingService->transitionStatus($booking, 'serving');
        $this->notificationService->notifyStatusChanged($booking);

        return back()->with('success', "Layanan untuk booking {$booking->booking_code} dimulai.");
    }

    public function finishService(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeOwns($request, $booking);
        $this->bookingService->transitionStatus($booking, 'completed');
        $this->notificationService->notifyStatusChanged($booking);

        return back()->with('success', "Booking {$booking->booking_code} telah selesai.");
    }

    public function markLate(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeOwns($request, $booking);
        $this->bookingService->transitionStatus($booking, 'late');
        $this->notificationService->notifyStatusChanged($booking);

        return back()->with('success', "Booking {$booking->booking_code} ditandai terlambat.");
    }
}
