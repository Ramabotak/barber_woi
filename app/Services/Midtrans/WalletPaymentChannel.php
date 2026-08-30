<?php

namespace App\Services\Midtrans;

use InvalidArgumentException;

class WalletPaymentChannel implements PaymentChannel
{
    private const WALLETS = ['gopay', 'shopeepay'];

    public function methods(): array { return self::WALLETS; }

    public function payload(string $method, array $options = []): array
    {
        if (!in_array($method, self::WALLETS, true)) {
            throw new InvalidArgumentException('E-wallet tidak didukung.');
        }

        return ['payment_type' => $method];
    }
}
