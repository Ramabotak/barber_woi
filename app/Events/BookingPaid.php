<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingPaid implements ShouldBroadcast
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
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'status' => $this->booking->status,
            'message' => "Booking {$this->booking->booking_code} sudah dibayar - masuk antrean",
        ];
    }

    public function broadcastAs(): string
    {
        return 'booking.paid';
    }
}
