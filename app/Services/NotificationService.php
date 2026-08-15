<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Notification;

class NotificationService
{
    public function send(int $userId, int $bookingId, string $title, string $message): Notification
    {
        return Notification::create([
            'booking_id' => $bookingId,
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    public function notifyBookingCreated(Booking $booking): void
    {
        $this->send($booking->customer_id, $booking->id, 'Booking Dibuat',
            "Booking {$booking->booking_code} berhasil dibuat, menunggu konfirmasi barber.");

        $this->send($booking->barber->user_id, $booking->id, 'Booking Baru',
            "Ada booking baru {$booking->booking_code} yang menunggu konfirmasi Anda.");
    }

    public function notifyBookingAccepted(Booking $booking): void
    {
        $this->send($booking->customer_id, $booking->id, 'Booking Diterima',
            "Booking {$booking->booking_code} telah diterima. Nomor antrean Anda: {$booking->queue_number}.");
    }

    public function notifyBookingPaid(Booking $booking): void
    {
        $this->send($booking->customer_id, $booking->id, 'Pembayaran Berhasil',
            "Pembayaran booking {$booking->booking_code} berhasil. Menunggu konfirmasi barber.");

        $this->send($booking->barber->user_id, $booking->id, 'Booking Dibayar',
            "Ada booking {$booking->booking_code} yang sudah dibayar, menunggu konfirmasi Anda.");
    }

    public function notifyBookingRejected(Booking $booking): void
    {
        $this->send($booking->customer_id, $booking->id, 'Booking Ditolak',
            "Mohon maaf, booking {$booking->booking_code} ditolak oleh barber.");
    }

    public function notifyStatusChanged(Booking $booking): void
    {
        $labels = [
            'waiting' => 'menunggu giliran',
            'late' => 'ditandai terlambat',
            'serving' => 'sedang dilayani',
            'completed' => 'selesai dilayani',
            'cancelled' => 'dibatalkan',
        ];
        $label = $labels[$booking->status] ?? $booking->status;

        $this->send($booking->customer_id, $booking->id, 'Status Antrean Diperbarui',
            "Booking {$booking->booking_code} sekarang {$label}.");
    }

    public function notifyRefundProcessed(Booking $booking): void
    {
        $this->send($booking->customer_id, $booking->id, 'Refund Diproses',
            "Pembayaran untuk booking {$booking->booking_code} telah direfund oleh admin.");
    }
}
