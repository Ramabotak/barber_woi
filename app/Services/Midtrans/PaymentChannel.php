<?php

namespace App\Services\Midtrans;

interface PaymentChannel
{
    /** Internal method keys this channel can charge. */
    public function methods(): array;

    /** Return only the payment-method-specific portion of a Core API charge payload. */
    public function payload(string $method, array $options = []): array;
}
