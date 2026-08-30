<?php

namespace App\Services\Midtrans;

use InvalidArgumentException;

class VirtualAccountPaymentChannel implements PaymentChannel
{
    private const BANKS = ['bca', 'bni', 'bri', 'permata'];

    public function methods(): array { return self::BANKS; }

    public function payload(string $method, array $options = []): array
    {
        if (!in_array($method, self::BANKS, true)) {
            throw new InvalidArgumentException('Bank Virtual Account tidak didukung.');
        }

        return ['payment_type' => 'bank_transfer', 'bank_transfer' => ['bank' => $method]];
    }
}
