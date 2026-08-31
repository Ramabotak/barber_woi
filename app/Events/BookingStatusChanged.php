<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Booking $booking)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('booking.' . $this->booking->id);
    }

    public function broadcastWith(): array
    {
        $messages = [
            'waiting' => 'Booking sedang menunggu giliran',
            'serving' => 'Booking sedang dilayani',
            'late' => 'Booking ditandai terlambat',
            'completed' => 'Booking selesai dilayani',
            'cancelled' => 'Booking dibatalkan',
        ];

        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'status' => $this->booking->status,
            'message' => $messages[$this->booking->status] ?? 'Status booking berubah',
            'check_in_time' => $this->booking->check_in_time?->toIso8601String(),
            'finished_at' => $this->booking->finished_at?->toIso8601String(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'booking.status-changed';
    }
}
