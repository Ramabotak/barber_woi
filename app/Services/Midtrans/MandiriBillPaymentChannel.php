<?php

namespace App\Services\Midtrans;

class MandiriBillPaymentChannel implements PaymentChannel
{
    public function methods(): array { return ['mandiri_bill']; }

    public function payload(string $method, array $options = []): array
    {
        return [
            'payment_type' => 'echannel',
            'echannel' => [
                'bill_info1' => 'Barber Woi',
                'bill_info2' => $options['bill_info2'] ?? 'Pembayaran booking',
            ],
        ];
    }
}
