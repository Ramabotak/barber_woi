<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification as MidtransNotification;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createSnapTransaction(Booking $booking): Payment
    {
        $booking->loadMissing(['customer', 'service']);

        $params = [
            'transaction_details' => [
                'order_id' => $booking->booking_code . '-' . time(),
                'gross_amount' => (int) $booking->service->price,
            ],
            'customer_details' => [
                'first_name' => $booking->customer->name,
                'email' => $booking->customer->email,
                'phone' => $booking->customer->phone_number,
            ],
            'item_details' => [[
                'id' => (string) $booking->service->id,
                'price' => (int) $booking->service->price,
                'quantity' => 1,
                'name' => $booking->service->service_name,
            ]],
        ];

        $snapToken = Snap::getSnapToken($params);

        return Payment::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'amount' => $booking->service->price,
                'status' => 'pending',
                'snap_token' => $snapToken,
                'transaction_id' => $params['transaction_details']['order_id'],
            ]
        );
    }

    public function handleCallback(array $payload): array
    {
        $notification = new MidtransNotification($payload);

        $transactionStatus = $notification->transaction_status;
        $fraudStatus = $notification->fraud_status ?? null;

        $status = match (true) {
            $transactionStatus === 'capture' && $fraudStatus === 'challenge' => 'pending',
            $transactionStatus === 'capture' && $fraudStatus === 'accept' => 'paid',
            $transactionStatus === 'settlement' => 'paid',
            $transactionStatus === 'pending' => 'pending',
            $transactionStatus === 'deny' => 'failed',
            $transactionStatus === 'cancel' => 'cancelled',
            $transactionStatus === 'expire' => 'expired',
            default => 'pending',
        };

        return [
            'order_id' => $notification->order_id,
            'status' => $status,
            'payment_type' => $notification->payment_type ?? null,
        ];
    }
}
