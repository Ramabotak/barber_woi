<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\CoreApi;
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

    public function createSnapTransaction(Booking $booking, ?string $selectedMethod = null): Payment
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

        // The custom Barber Woi chooser selects the starting channel while
        // Snap supplies the regulated payment instructions for that channel.
        $snapChannels = [
            'qris' => 'gopay',
            'bca' => 'bca_va',
            'bni' => 'bni_va',
            'bri' => 'bri_va',
            'permata' => 'permata_va',
        ];
        if ($selectedMethod && isset($snapChannels[$selectedMethod])) {
            $params['enabled_payments'] = [$snapChannels[$selectedMethod]];
        }

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

    /** Create a Core API charge so Barber Woi can own the checkout UI. */
    public function createCoreTransaction(Booking $booking, string $method): Payment
    {
        $booking->loadMissing(['customer', 'service']);

        $params = [
            'transaction_details' => [
                'order_id' => $booking->booking_code . '-' . now()->format('YmdHis') . '-' . random_int(100, 999),
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

        if ($method === 'qris') {
            $params['payment_type'] = 'qris';
            $params['qris'] = ['acquirer' => 'gopay'];
        } else {
            $params['payment_type'] = 'bank_transfer';
            $params['bank_transfer'] = ['bank' => $method];
        }

        $response = (array) CoreApi::charge($params);
        $response['method_label'] = $method === 'qris' ? 'QRIS' : strtoupper($method) . ' Virtual Account';

        return Payment::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'amount' => $booking->service->price,
                'status' => 'pending',
                'payment_method' => $method,
                'transaction_id' => $params['transaction_details']['order_id'],
                'snap_token' => null,
                'payment_data' => $response,
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
