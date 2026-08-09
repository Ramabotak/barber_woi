<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $query = Review::with(['customer', 'booking.barber.user', 'booking.service']);

        // Filter opsional per barber, biar admin bisa lihat rating barber tertentu
        if ($request->filled('barber_id')) {
            $query->whereHas('booking', fn ($q) => $q->where('barber_id', $request->input('barber_id')));
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->input('rating'));
        }

        $reviews = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $averageRating = Review::avg('rating');
        $totalReviews = Review::count();

        return view('admin.reviews.index', compact('reviews', 'averageRating', 'totalReviews'));
    }

    // Admin bisa hapus ulasan yang mengandung konten tidak pantas.
    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
