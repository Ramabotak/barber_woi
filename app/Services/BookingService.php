<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {
    }

    // Membuat booking baru. booking_code & queue_number tetap di-generate
    // otomatis lewat Booking::boot() (static::creating), di sini kita hanya
    // membungkusnya dalam transaction + row lock jadwal agar aman dari
    // race condition saat banyak customer booking jadwal yang sama bersamaan.
    public function createBooking(int $customerId, int $barberId, int $serviceId, int $scheduleId): Booking
    {
        return DB::transaction(function () use ($customerId, $barberId, $serviceId, $scheduleId) {
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

            $booking = Booking::create([
                'customer_id' => $customerId,
                'barber_id' => $barberId,
                'service_id' => $service->id,
                'schedule_id' => $schedule->id,
                'status' => 'pending',
            ]);

            $this->maybeMarkScheduleFull($schedule, $service);
            $this->notificationService->notifyBookingCreated($booking->fresh(['barber']));

            return $booking;
        });
    }

    protected function maybeMarkScheduleFull(Schedule $schedule, Service $service): void
    {
        $totalMinutes = now()->parse($schedule->start_time)->diffInMinutes(now()->parse($schedule->end_time));
        $capacity = $service->duration > 0 ? intdiv($totalMinutes, $service->duration) : 1;
        $capacity = max($capacity, 1);

        $bookedCount = Booking::where('schedule_id', $schedule->id)
            ->where('status', '!=', 'cancelled')
            ->count();

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
