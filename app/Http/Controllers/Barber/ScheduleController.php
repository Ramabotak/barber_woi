<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ScheduleController extends Controller
{
    // Barber tutup shift/jadwal manual jika sudah tidak ada antrean.
    public function closeShift(Request $request, Schedule $schedule): RedirectResponse
    {
        $barber = $request->user()->barber;
        abort_unless($barber && $schedule->barber_id === $barber->id, 403);

        $hasActive = $schedule->bookings()->whereNotIn('status', ['completed', 'cancelled'])->exists();
        abort_if($hasActive, 422, 'Masih ada antrean aktif pada jadwal ini.');

        $schedule->update(['status' => 'libur']);

        return back()->with('success', 'Shift berhasil ditutup.');
    }
}
