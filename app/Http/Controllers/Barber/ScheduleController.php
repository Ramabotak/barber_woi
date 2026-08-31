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

        // Cegah membuat jadwal yang bertabrakan (overlap) dengan jadwal lain di hari yang sama.
        $overlapping = Schedule::where('barber_id', $barber->id)
            ->where('date', $validated['date'])
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->first();

        if ($overlapping) {
            $existingStart = \Carbon\Carbon::parse($overlapping->start_time)->format('H:i');
            $existingEnd = \Carbon\Carbon::parse($overlapping->end_time)->format('H:i');
            return back()->withInput()->withErrors([
                'start_time' => "Jadwal bertabrakan dengan jadwal yang sudah ada ({$existingStart} – {$existingEnd} WIB).",
            ]);
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

    // Hapus jadwal jika tidak ada booking aktif/selesai (hanya boleh jika 0 booking atau semua booking cancelled).
    public function destroy(Request $request, Schedule $schedule): RedirectResponse
    {
        $barber = $request->user()->barber;
        abort_unless($barber && $schedule->barber_id === $barber->id, 403);

        $hasActiveOrCompleted = $schedule->bookings()->whereNotIn('status', ['cancelled'])->exists();
        if ($hasActiveOrCompleted) {
            return back()->withErrors(['schedule' => 'Jadwal ini memiliki booking yang aktif atau sudah selesai, sehingga tidak bisa dihapus. Silakan gunakan opsi Tutup Jadwal.']);
        }

        $schedule->delete();

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }

    // Barber tutup shift/jadwal manual jika sudah tidak ada antrean.
    public function closeShift(Request $request, Schedule $schedule): RedirectResponse
    {
        $barber = $request->user()->barber;
        abort_unless($barber && $schedule->barber_id === $barber->id, 403);

        $hasActive = $schedule->bookings()->whereNotIn('status', ['completed', 'cancelled'])->exists();
        if ($hasActive) {
            return back()->withErrors(['schedule' => 'Masih ada antrean aktif pada jadwal ini, tidak dapat menutup shift sekarang.']);
        }

        $schedule->update(['status' => 'libur']);

        return back()->with('success', 'Shift berhasil ditutup.');
    }
}
