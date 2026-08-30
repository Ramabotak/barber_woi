<?php

namespace App\Services\Midtrans;

use InvalidArgumentException;

class CreditCardPaymentChannel implements PaymentChannel
{
    public function methods(): array { return ['credit_card']; }

    public function payload(string $method, array $options = []): array
    {
        if (empty($options['token_id'])) {
            throw new InvalidArgumentException('Token kartu tidak tersedia. Silakan isi ulang data kartu.');
        }

        return [
            'payment_type' => 'credit_card',
            'credit_card' => ['token_id' => $options['token_id'], 'authentication' => true],
        ];
    }
}
