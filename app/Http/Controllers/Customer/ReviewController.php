<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->customer_id === $request->user()->id, 403);
        abort_unless($booking->status === 'completed', 422, 'Booking belum selesai.');
        abort_if($booking->review()->exists(), 422, 'Booking ini sudah diulas.');

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Review::create([
            'booking_id' => $booking->id,
            'customer_id' => $request->user()->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return back()->with('success', 'Terima kasih atas ulasan Anda.');
    }
}
