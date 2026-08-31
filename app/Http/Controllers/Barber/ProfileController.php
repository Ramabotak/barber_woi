<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $barber = $request->user()->barber;
        abort_unless($barber, 403, '\''Akun Anda belum terhubung dengan data barber.'\'' );

        $reviews = Review::with(['customer'])
            ->whereHas('booking', fn ($query) => $query->where('barber_id', $barber->id))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('barber.profile.index', compact('barber', 'reviews'));
    }
}
