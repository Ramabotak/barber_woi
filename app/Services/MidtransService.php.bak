<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\Midtrans\CreditCardPaymentChannel;
use App\Services\Midtrans\MandiriBillPaymentChannel;
use App\Services\Midtrans\PaymentChannel;
use App\Services\Midtrans\QrisPaymentChannel;
use App\Services\Midtrans\VirtualAccountPaymentChannel;
use App\Services\Midtrans\WalletPaymentChannel;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    /** @var array<string, PaymentChannel> */
    private array $channels = [];

    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        foreach ([new QrisPaymentChannel(), new VirtualAccountPaymentChannel(), new MandiriBillPaymentChannel(), new WalletPaymentChannel(), new CreditCardPaymentChannel()] as $channel) {
            foreach ($channel->methods() as $method) $this->channels[$method] = $channel;
        }
    }

    public function availableMethods(): array { return array_keys($this->channels); }
    public function driver(): string { return config('midtrans.payment_driver') === 'core' ? 'core' : 'snap'; }

    /** Legacy rollback path. Keep this until Core API production validation is complete. */
    public function createSnapTransaction(Booking $booking, ?string $selectedMethod = null): Payment
    {
        $booking->loadMissing(['customer', 'service']);
        $params = [
            'transaction_details' => ['order_id' => $booking->booking_code . '-' . time(), 'gross_amount' => (int) $booking->service->price],
            'customer_details' => ['first_name' => $booking->customer->name, 'email' => $booking->customer->email, 'phone' => $booking->customer->phone_number],
            'item_details' => [['id' => (string) $booking->service->id, 'price' => (int) $booking->service->price, 'quantity' => 1, 'name' => $booking->service->service_name]],
        ];
        $snapChannels = ['qris' => 'gopay', 'bca' => 'bca_va', 'bni' => 'bni_va', 'bri' => 'bri_va', 'permata' => 'permata_va'];
        if ($selectedMethod && isset($snapChannels[$selectedMethod])) $params['enabled_payments'] = [$snapChannels[$selectedMethod]];

        return Payment::updateOrCreate(['booking_id' => $booking->id], [
            'amount' => $booking->service->price, 'status' => 'pending', 'payment_method' => $selectedMethod,
            'snap_token' => Snap::getSnapToken($params), 'transaction_id' => $params['transaction_details']['order_id'],
            'payment_data' => null, 'expires_at' => null,
        ]);
    }

    /** Core API charge; card values never reach this method, only Midtrans token_id does. */
    public function createCoreTransaction(Booking $booking, string $method, array $options = []): Payment
    {
        $booking->loadMissing(['customer', 'service']);
        $channel = $this->channels[$method] ?? throw new InvalidArgumentException('Metode pembayaran tidak didukung.');
        $orderId = $booking->booking_code . '-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(5));
        $payload = array_merge([
            'transaction_details' => ['order_id' => $orderId, 'gross_amount' => (int) $booking->service->price],
            'customer_details' => ['first_name' => $booking->customer->name, 'email' => $booking->customer->email, 'phone' => $booking->customer->phone_number],
            'item_details' => [['id' => (string) $booking->service->id, 'price' => (int) $booking->service->price, 'quantity' => 1, 'name' => $booking->service->service_name]],
        ], $channel->payload($method, array_merge($options, ['bill_info2' => $booking->booking_code])));
        if ($url = config('midtrans.notification_url')) $payload['notification_url'] = $url;

        $response = Http::acceptJson()->asJson()->withBasicAuth(config('midtrans.server_key'), '')
            ->post($this->coreUrl('/v2/charge'), $payload)->throw()->json();
        $response['method_key'] = $method;
        $response['method_label'] = $this->methodLabel($method);
        $response['instruction'] = $this->instructionData($response);

        return Payment::updateOrCreate(['booking_id' => $booking->id], [
            'amount' => $booking->service->price, 'status' => $this->statusFromGateway($response['transaction_status'] ?? 'pending', $response['fraud_status'] ?? null),
            'payment_method' => $method, 'transaction_id' => $response['order_id'] ?? $orderId, 'snap_token' => null,
            'payment_data' => $response, 'expires_at' => $this->parseExpiry($response['expiry_time'] ?? null),
        ]);
    }

    public function getStatus(string $orderId): array
    {
        return Http::acceptJson()->withBasicAuth(config('midtrans.server_key'), '')
            ->get($this->coreUrl('/v2/' . rawurlencode($orderId) . '/status'))->throw()->json();
    }

    /** Reject forged webhooks before applying transaction status. */
    public function normalizeNotification(array $payload): array
    {
        $signature = hash('sha512', ($payload['order_id'] ?? '') . ($payload['status_code'] ?? '') . ($payload['gross_amount'] ?? '') . config('midtrans.server_key'));
        if (empty($payload['signature_key']) || !hash_equals($signature, (string) $payload['signature_key'])) throw new InvalidArgumentException('Invalid Midtrans notification signature.');
        return $payload;
    }

    public function applyGatewayResponse(Payment $payment, array $response): Payment
    {
        $status = $this->statusFromGateway($response['transaction_status'] ?? 'pending', $response['fraud_status'] ?? null);
        $payment->fill(['status' => $status, 'payment_method' => $response['payment_type'] ?? $payment->payment_method, 'payment_data' => array_merge($payment->payment_data ?? [], $response), 'expires_at' => $this->parseExpiry($response['expiry_time'] ?? null) ?? $payment->expires_at]);
        if ($status === 'paid' && !$payment->paid_at) $payment->paid_at = now();
        $payment->save();
        return $payment;
    }

    public function errorMessage(\Throwable $exception): string
    {
        if ($exception instanceof RequestException) {
            $body = $exception->response->json();
            if ($exception->response->status() === 402) {
                $message = $body['status_message'] ?? $body['error_messages'][0] ?? null;
                if ($message && !str_contains(strtolower($message), 'fraud')) return $message;
                return 'Transaksi ditolak oleh Fraud Detection System Midtrans. Coba metode lain atau hubungi pihak bank.';
            }
            return $body['status_message'] ?? $body['error_messages'][0] ?? 'Midtrans tidak dapat membuat instruksi pembayaran.';
        }
        return $exception->getMessage() ?: 'Pembayaran belum dapat disiapkan.';
    }

    private function coreUrl(string $path): string { return (config('midtrans.is_production') ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com') . $path; }
    /** Normalize bank-transfer and Mandiri Bill details so checkout has one stable shape. */
    private function instructionData(array $response): array
    {
        $instruction = [
            'gateway_transaction_id' => $response['transaction_id'] ?? null,
            'order_id' => $response['order_id'] ?? null,
            'gross_amount' => $response['gross_amount'] ?? null,
            'transaction_status' => $response['transaction_status'] ?? null,
            'expiry_time' => $response['expiry_time'] ?? null,
            'payment_type' => $response['payment_type'] ?? null,
        ];

        if (($response['payment_type'] ?? null) === 'echannel') {
            return $instruction + ['bank' => 'mandiri', 'bill_key' => $response['bill_key'] ?? null, 'biller_code' => $response['biller_code'] ?? null];
        }

        $va = $response['va_numbers'][0] ?? [];
        return $instruction + ['bank' => $va['bank'] ?? null, 'va_number' => $va['va_number'] ?? ($response['permata_va_number'] ?? null)];
    }
    private function parseExpiry(?string $expiry): ?Carbon { return $expiry ? Carbon::parse($expiry) : null; }
    private function statusFromGateway(?string $status, ?string $fraud): string
    {
        return match (true) {
            $status === 'capture' && $fraud === 'challenge' => 'pending', $status === 'capture', $status === 'settlement' => 'paid',
            $status === 'deny' => 'failed', $status === 'cancel' => 'cancelled', $status === 'expire' => 'expired', default => 'pending',
        };
    }
    private function methodLabel(string $method): string { return ['qris' => 'QRIS', 'credit_card' => 'Kartu Kredit', 'mandiri_bill' => 'Mandiri Bill', 'gopay' => 'GoPay', 'shopeepay' => 'ShopeePay'][$method] ?? strtoupper($method) . ' Virtual Account'; }
}
