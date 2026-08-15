<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Service;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    // Ukuran tiap slot jadwal (menit). Customer memilih slot spesifik,
    // bukan keseluruhan rentang hari.
    public const SLOT_MINUTES = 30;

    public function __construct(
        protected NotificationService $notificationService
    ) {
    }

    // Membuat booking baru. booking_code & queue_number tetap di-generate
    // otomatis lewat Booking::boot() (static::creating), di sini kita hanya
    // membungkusnya dalam transaction + row lock jadwal agar aman dari
    // race condition saat banyak customer booking jadwal yang sama bersamaan.
    public function createBooking(
        int $customerId,
        int $barberId,
        int $serviceId,
        int $scheduleId,
        ?string $slotTime = null,
    ): Booking {
        return DB::transaction(function () use ($customerId, $barberId, $serviceId, $scheduleId, $slotTime) {
            $schedule = Schedule::where('id', $scheduleId)
                ->where('barber_id', $barberId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($schedule->status !== 'tersedia') {
                throw ValidationException::withMessages([
                    'schedule_id' => 'Jadwal ini sudah tidak tersedia.',
                ]);
            }

            $service = Service::where('id', $serviceId)->where('status', 'active')->firstOrFail();

            $slot = $this->validateSlotTime($schedule, $slotTime);

            $booking = Booking::create([
                'customer_id' => $customerId,
                'barber_id' => $barberId,
                'service_id' => $service->id,
                'schedule_id' => $schedule->id,
                'slot_time' => $slot,
                'status' => 'pending',
            ]);

            $this->maybeMarkScheduleFull($schedule);
            $this->notificationService->notifyBookingCreated($booking->fresh(['barber']));

            return $booking;
        });
    }

    // Buat daftar slot 30 menit beserta ketersediaannya untuk sebuah jadwal.
    // Dipakai endpoint /schedules agar frontend tinggal me-render tombol slot.
    public function slots(Schedule $schedule): array
    {
        $start = Carbon::parse($schedule->start_time);
        $end = Carbon::parse($schedule->end_time);

        $booked = Booking::where('schedule_id', $schedule->id)
            ->whereNotNull('slot_time')
            ->where('status', '!=', 'cancelled')
            ->pluck('slot_time')
            ->map(fn ($t) => Carbon::parse($t)->format('H:i'))
            ->flip();

        $slots = [];
        for ($cursor = $start->copy(); $cursor->lt($end); $cursor->addMinutes(self::SLOT_MINUTES)) {
            $labelStart = $cursor->format('H:i');
            $labelEnd = $cursor->copy()->addMinutes(self::SLOT_MINUTES)->format('H:i');

            $slots[] = [
                'time' => $labelStart,
                'label' => $labelStart . ' - ' . $labelEnd,
                'available' => !$booked->has($labelStart),
            ];
        }

        return $slots;
    }

    // Validasi slot: harus berupa kelipatan 30 menit, berada dalam rentang jadwal,
    // dan belum dibooking oleh orang lain.
    protected function validateSlotTime(Schedule $schedule, ?string $slotTime): string
    {
        if (!$slotTime) {
            throw ValidationException::withMessages([
                'slot_time' => 'Pilih jam slot terlebih dahulu.',
            ]);
        }

        $picked = Carbon::parse($slotTime);
        $start = Carbon::parse($schedule->start_time);
        $end = Carbon::parse($schedule->end_time);

        $valid = $picked >= $start
            && $picked->lt($end)
            && $picked->minute % self::SLOT_MINUTES === 0
            && $picked->second === 0;

        if (!$valid) {
            throw ValidationException::withMessages([
                'slot_time' => 'Slot yang dipilih tidak valid untuk jadwal ini.',
            ]);
        }

        $alreadyBooked = Booking::where('schedule_id', $schedule->id)
            ->where('slot_time', $slotTime)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($alreadyBooked) {
            throw ValidationException::withMessages([
                'slot_time' => 'Slot jam tersebut sudah dibooking customer lain.',
            ]);
        }

        return $slotTime;
    }

    protected function maybeMarkScheduleFull(Schedule $schedule): void
    {
        $totalMinutes = Carbon::parse($schedule->start_time)->diffInMinutes(Carbon::parse($schedule->end_time));
        $capacity = max(intdiv($totalMinutes, self::SLOT_MINUTES), 1);

        $bookedCount = Booking::where('schedule_id', $schedule->id)
            ->whereNotNull('slot_time')
            ->where('status', '!=', 'cancelled')
            ->distinct('slot_time')
            ->count('slot_time');

        if ($bookedCount >= $capacity) {
            $schedule->update(['status' => 'penuh']);
        }
    }

    public function transitionStatus(Booking $booking, string $newStatus): Booking
    {
        if (!$booking->canTransitionTo($newStatus)) {
            throw ValidationException::withMessages([
                'status' => "Status tidak bisa diubah dari '{$booking->status}' ke '{$newStatus}'.",
            ]);
        }

        $booking->status = $newStatus;

        if ($newStatus === 'serving' && !$booking->check_in_time) {
            $booking->check_in_time = now();
        }

        if ($newStatus === 'completed') {
            $booking->finished_at = now();
        }

        $booking->save();

        return $booking;
    }
}
