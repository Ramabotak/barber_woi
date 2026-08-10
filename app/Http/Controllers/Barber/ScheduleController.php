<?php

namespace App\Http\Controllers\Barber;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    // Daftar jadwal milik barber yang login, dari hari ini ke depan.
    public function index(Request $request): View
    {
        $barber = $request->user()->barber;
        abort_unless($barber, 403);

        $schedules = Schedule::where('barber_id', $barber->id)
            ->where('date', '>=', now()->toDateString())
            ->withCount(['bookings' => fn ($q) => $q->whereNotIn('status', ['cancelled'])])
            ->orderBy('date')->orderBy('start_time')
            ->get();

        return view('barber.schedule.index', compact('schedules'));
    }

    // Barber menambah slot jadwal baru (tanggal + jam mulai/selesai).
    public function store(Request $request): RedirectResponse
    {
        $barber = $request->user()->barber;
        abort_unless($barber, 403);

        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        // Cegah bikin jadwal yang bentrok/duplikat persis di tanggal & jam yang sama.
        $exists = Schedule::where('barber_id', $barber->id)
            ->where('date', $validated['date'])
            ->where('start_time', $validated['start_time'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['start_time' => 'Jadwal di tanggal & jam ini sudah ada.']);
        }

        Schedule::create([
            'barber_id' => $barber->id,
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'status' => 'tersedia',
        ]);

        return back()->with('success', 'Jadwal berhasil ditambahkan.');
    }

    // Hapus jadwal yang belum ada booking sama sekali.
    public function destroy(Request $request, Schedule $schedule): RedirectResponse
    {
        $barber = $request->user()->barber;
        abort_unless($barber && $schedule->barber_id === $barber->id, 403);

        $hasBookings = $schedule->bookings()->exists();
        abort_if($hasBookings, 422, 'Jadwal ini sudah punya booking, tidak bisa dihapus.');

        $schedule->delete();

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }

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
