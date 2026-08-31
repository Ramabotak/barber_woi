<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(protected NotificationService $notificationService)
    {
    }

    public function index(Request $request): View
    {
        $query = Booking::with(['customer', 'barber.user', 'service', 'schedule', 'payment']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('date')) {
            $query->whereHas('schedule', fn ($q) => $q->whereDate('date', $request->input('date')));
        }
        // Pencarian berdasarkan kode booking atau nama customer (dipakai search bar di topbar)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $bookings = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);
        $booking->load(['customer', 'barber.user', 'service', 'schedule', 'payment', 'review']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        $this->authorize('update', $booking);
        abort_if(in_array($booking->status, ['completed', 'cancelled'], true), 422, 'Booking tidak bisa dibatalkan.');

        $booking->update(['status' => 'cancelled']);
        $this->notificationService->notifyStatusChanged($booking);

        return back()->with('success', "Booking {$booking->booking_code} dibatalkan.");
    }

    // Jalan pintas admin: langsung tandai selesai dari status apa pun (kecuali
    // yang sudah completed/cancelled), tanpa harus lewat urutan status normal.
    // Berguna untuk kasus status nyangkut, misalnya webhook pembayaran belum
    // sempat masuk saat testing.
    public function forceComplete(Booking $booking): RedirectResponse
    {
        $this->authorize('forceActions', $booking);
        abort_if(in_array($booking->status, ['completed', 'cancelled'], true), 422, 'Booking ini sudah final.');

        $booking->update([
            'status' => 'completed',
            'check_in_time' => $booking->check_in_time ?? now(),
            'finished_at' => now(),
        ]);

        $this->notificationService->notifyStatusChanged($booking);

        return back()->with('success', "Booking {$booking->booking_code} langsung ditandai selesai.");
    }
}
