<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'customer_id',
        'barber_id',
        'service_id',
        'schedule_id',
        'slot_time',
        'queue_number',
        'status',
        'check_in_time',
        'finished_at',
    ];

    protected $casts = [
        'slot_time' => 'datetime:H:i:s',
        'check_in_time' => 'datetime',
        'finished_at' => 'datetime',
    ];

    // Alur status booking yang valid:
    // pending (dibuat, menunggu persetujuan barber) -> accepted (siap dibayar)
    // -> paid (lunas, masuk antrean) -> waiting/serving -> completed/cancelled.
    // Dipakai untuk mencegah status "lompat" (mis. pending langsung ke completed).
    public const STATUS_FLOW = [
        'pending' => ['accepted', 'cancelled'],
        'accepted' => ['paid', 'cancelled'],
        'paid' => ['waiting', 'cancelled'],
        'waiting' => ['serving', 'late', 'cancelled'],
        'late' => ['serving', 'cancelled'],
        'serving' => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
    ];

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::STATUS_FLOW[$this->status] ?? [], true);
    }

    // Boot method untuk generate booking_code otomatis
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = self::generateBookingCode();
            }

            if (empty($booking->queue_number)) {
                $booking->queue_number = self::generateQueueNumber($booking->barber_id, $booking->schedule_id);
            }
        });
    }

    // Generate kode booking unik
    public static function generateBookingCode(): string
    {
        $date = now()->format('Ymd');
        $lastBooking = self::whereDate('created_at', today())->latest('id')->first();
        $number = $lastBooking ? ((int) substr($lastBooking->booking_code, -3)) + 1 : 1;
        return 'BKG-' . $date . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    // Generate nomor antrean per barber per jadwal
    public static function generateQueueNumber(int $barberId, int $scheduleId): int
    {
        $lastQueue = self::where('barber_id', $barberId)
                        ->where('schedule_id', $scheduleId)
                        ->max('queue_number');
        return $lastQueue ? $lastQueue + 1 : 1;
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Scope status
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Dipakai halaman "Booking Saya" (tab Aktif vs Riwayat)
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function scopeHistory($query)
    {
        return $query->whereIn('status', ['completed', 'cancelled']);
    }
}
