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
    ) {}

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

            $slot = $this->validateSlotTime($schedule, $service, $slotTime);

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
    public function slots(Schedule $schedule, Service $service): array
    {
        $start = $this->timeOnSchedule($schedule, $schedule->start_time->format('H:i:s'));
        $end = $this->timeOnSchedule($schedule, $schedule->end_time->format('H:i:s'));
        $requiredSlots = $this->slotsRequired($service);
        $bookings = $this->activeBookings($schedule);

        $slots = [];
        for ($cursor = $start->copy(); $cursor->lt($end); $cursor->addMinutes(self::SLOT_MINUTES)) {
            $labelStart = $cursor->format('H:i');
            $labelEnd = $cursor->copy()->addMinutes(self::SLOT_MINUTES)->format('H:i');

            $slots[] = [
                'time' => $labelStart,
                'label' => $labelStart.' - '.$labelEnd,
                // Sebuah jam hanya ditawarkan apabila seluruh kotak 30 menit
                // yang diperlukan layanan ini masih kosong secara berurutan.
                'available' => $this->isRangeAvailable(
                    $schedule,
                    $cursor,
                    $requiredSlots,
                    $bookings,
                    $end,
                ),
            ];
        }

        return $slots;
    }

    // Validasi slot: harus berupa kelipatan 30 menit, berada dalam rentang jadwal,
    // dan belum dibooking oleh orang lain.
    protected function validateSlotTime(Schedule $schedule, Service $service, ?string $slotTime): string
    {
        if (! $slotTime) {
            throw ValidationException::withMessages([
                'slot_time' => 'Pilih jam slot terlebih dahulu.',
            ]);
        }

        $picked = $this->timeOnSchedule($schedule, $slotTime);
        $start = $this->timeOnSchedule($schedule, $schedule->start_time->format('H:i:s'));
        $end = $this->timeOnSchedule($schedule, $schedule->end_time->format('H:i:s'));

        $valid = $picked >= $start
            && $picked->lt($end)
            && $picked->minute % self::SLOT_MINUTES === 0
            && $picked->second === 0;

        if (! $valid) {
            throw ValidationException::withMessages([
                'slot_time' => 'Slot yang dipilih tidak valid untuk jadwal ini.',
            ]);
        }

        if (! $this->isRangeAvailable(
            $schedule,
            $picked,
            $this->slotsRequired($service),
            $this->activeBookings($schedule),
            $end,
        )) {
            throw ValidationException::withMessages([
                'slot_time' => 'Jam ini tidak tersedia untuk durasi layanan yang dipilih. Pilih jam lain.',
            ]);
        }

        return $picked->format('H:i:s');
    }

    /** Durasi layanan selalu mengunci jumlah slot 30 menit yang dibulatkan ke atas. */
    protected function slotsRequired(Service $service): int
    {
        return max((int) ceil(((int) $service->duration) / self::SLOT_MINUTES), 1);
    }

    /** Booking aktif beserta layanan diperlukan untuk menghitung rentang bloknya. */
    protected function activeBookings(Schedule $schedule)
    {
        return Booking::with('service:id,duration')
            ->where('schedule_id', $schedule->id)
            ->whereNotNull('slot_time')
            ->where('status', '!=', 'cancelled')
            ->get();
    }

    /**
     * Pastikan interval calon booking muat di jadwal dan tidak beririsan dengan
     * interval booking aktif lainnya. Contoh coloring 90 menit mengunci 3 slot.
     */
    protected function isRangeAvailable(Schedule $schedule, Carbon $start, int $slots, $bookings, Carbon $scheduleEnd): bool
    {
        $candidateEnd = $start->copy()->addMinutes($slots * self::SLOT_MINUTES);

        if ($start->lt($this->timeOnSchedule($schedule, $schedule->start_time->format('H:i:s'))) || $candidateEnd->gt($scheduleEnd)) {
            return false;
        }

        foreach ($bookings as $booking) {
            // Booking lama tanpa data layanan tidak ikut mengosongkan jadwal.
            // Dalam data normal relasi ini selalu tersedia karena foreign key.
            if (! $booking->service) {
                continue;
            }

            $bookingStart = $this->timeOnSchedule($schedule, $booking->slot_time->format('H:i:s'));
            $bookingEnd = $bookingStart->copy()->addMinutes($this->slotsRequired($booking->service) * self::SLOT_MINUTES);

            if ($bookingStart->lt($candidateEnd) && $bookingEnd->gt($start)) {
                return false;
            }
        }

        return true;
    }

    protected function timeOnSchedule(Schedule $schedule, string $time): Carbon
    {
        return Carbon::parse($schedule->date->format('Y-m-d').' '.$time);
    }

    protected function maybeMarkScheduleFull(Schedule $schedule): void
    {
        // Lock schedule untuk prevent race condition saat concurrent bookings
        $schedule = Schedule::where('id', $schedule->id)->lockForUpdate()->first();
        
        $start = $this->timeOnSchedule($schedule, $schedule->start_time->format('H:i:s'));
        $end = $this->timeOnSchedule($schedule, $schedule->end_time->format('H:i:s'));
        $bookings = $this->activeBookings($schedule);
        $isFull = true;

        // Status penuh dihitung dari kotak yang terkunci, bukan jumlah record
        // booking: satu layanan 90 menit dapat menutup tiga kotak sekaligus.
        for ($cursor = $start->copy(); $cursor->lt($end); $cursor->addMinutes(self::SLOT_MINUTES)) {
            if ($this->isRangeAvailable($schedule, $cursor, 1, $bookings, $end)) {
                $isFull = false;
                break;
            }
        }

        if ($isFull) {
            $schedule->update(['status' => 'penuh']);
        }
    }

    public function transitionStatus(Booking $booking, string $newStatus): Booking
    {
        if (! $booking->canTransitionTo($newStatus)) {
            throw ValidationException::withMessages([
                'status' => "Status tidak bisa diubah dari '{$booking->status}' ke '{$newStatus}'.",
            ]);
        }

        $booking->status = $newStatus;

        if ($newStatus === 'serving' && ! $booking->check_in_time) {
            $booking->check_in_time = now();
        }

        if ($newStatus === 'completed') {
            $booking->finished_at = now();
        }

        $booking->save();

        return $booking;
    }
}
