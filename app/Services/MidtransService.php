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

        return $this->normalizeStatus(
            $notification->transaction_status,
            $notification->fraud_status ?? null,
            $notification->order_id,
            $notification->payment_type ?? null
        );
    }

    /**
     * Cek status transaksi langsung ke API Midtrans (bukan dari webhook).
     * Berguna sebagai fallback kalau webhook tidak sampai ke server kita,
     * misalnya saat testing di localhost yang tidak bisa diakses Midtrans.
     */
    public function getStatus(string $orderId): array
    {
        $result = \Midtrans\Transaction::status($orderId);

        return $this->normalizeStatus(
            $result->transaction_status ?? null,
            $result->fraud_status ?? null,
            $result->order_id ?? $orderId,
            $result->payment_type ?? null
        );
    }

    protected function normalizeStatus(?string $transactionStatus, ?string $fraudStatus, ?string $orderId, ?string $paymentType): array
    {
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
            'order_id' => $orderId,
            'status' => $status,
            'payment_type' => $paymentType,
        ];
    }
}
