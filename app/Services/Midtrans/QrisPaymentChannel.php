<?php

namespace App\Services\Midtrans;

class QrisPaymentChannel implements PaymentChannel
{
    public function methods(): array { return ['qris']; }

    public function payload(string $method, array $options = []): array
    {
        return ['payment_type' => 'qris', 'qris' => ['acquirer' => 'gopay']];
    }
}
