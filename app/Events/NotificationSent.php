<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Notification $notification)
    {
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('user.' . $this->notification->user_id);
    }

    /**
     * Data yang dikirim ke client
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,
            'title' => $this->notification->title,
            'message' => $this->notification->message,
            'created_at' => $this->notification->created_at->toIso8601String(),
        ];
    }

    /**
     * Nama event yang di-listen di frontend
     */
    public function broadcastAs(): string
    {
        return 'notification.sent';
    }
}
