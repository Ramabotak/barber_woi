<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Review;
use App\Services\ProfanityFilterService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct(
        protected ProfanityFilterService $profanityFilter
    ) {
    }

    // Daftar semua ulasan yang pernah dibuat customer yang sedang login.
    public function index(Request $request): View
    {
        $reviews = Review::with(['booking.barber.user', 'booking.service'])
            ->where('customer_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('customer.reviews.index', compact('reviews'));
    }

    // Dipanggil via fetch/AJAX dari halaman buat booking, saat customer klik "Lihat Ulasan" pada card barber.
    public function barberReviews(Barber $barber): JsonResponse
    {
        $reviews = Review::with('customer')
            ->whereHas('booking', fn ($q) => $q->where('barber_id', $barber->id))
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(fn (Review $review) => [
                'customer_name' => $review->customer->name,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'date' => $review->created_at->format('d M Y'),
            ]);

        return response()->json([
            'barber_name' => $barber->user->name,
            'average' => $barber->averageRating(),
            'count' => $barber->reviewsCount(),
            'reviews' => $reviews,
        ]);
    }

    public function store(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->customer_id === $request->user()->id, 403);
        abort_unless($booking->status === 'completed', 422, 'Booking belum selesai.');
        abort_if($booking->review()->exists(), 422, 'Booking ini sudah diulas.');

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        // Sensor kata kasar sebelum disimpan, biar tidak tampil apa adanya ke publik.
        $comment = $this->profanityFilter->censor($validated['comment'] ?? null);

        Review::create([
            'booking_id' => $booking->id,
            'customer_id' => $request->user()->id,
            'rating' => $validated['rating'],
            'comment' => $comment,
        ]);

        return back()->with('success', 'Terima kasih atas ulasan Anda.');
    }

    // Customer bisa mengedit ulasan miliknya sendiri (rating & komentar).
    public function update(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->customer_id === $request->user()->id, 403);

        $review = $booking->review()->firstOrFail();

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $comment = $this->profanityFilter->censor($validated['comment'] ?? null);

        $review->update([
            'rating' => $validated['rating'],
            'comment' => $comment,
        ]);

        return back()->with('success', 'Ulasan berhasil diperbarui.');
    }

    // Customer bisa menghapus ulasan miliknya sendiri.
    public function destroy(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->customer_id === $request->user()->id, 403);

        $review = $booking->review()->firstOrFail();
        $review->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
