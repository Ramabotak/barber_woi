<?php

use App\Services\Midtrans\MandiriBillPaymentChannel;
use App\Services\Midtrans\VirtualAccountPaymentChannel;

it('builds the Core API bank-transfer payload for every supported virtual account bank', function (string $bank) {
    $payload = (new VirtualAccountPaymentChannel())->payload($bank);

    expect($payload)->toBe([
        'payment_type' => 'bank_transfer',
        'bank_transfer' => ['bank' => $bank],
    ]);
})->with(['bca', 'bni', 'bri', 'permata']);

it('builds the distinct Core API echannel payload for Mandiri Bill', function () {
    $payload = (new MandiriBillPaymentChannel())->payload('mandiri_bill', [
        'bill_info2' => 'BKG-TEST-001',
    ]);

    expect($payload)->toBe([
        'payment_type' => 'echannel',
        'echannel' => [
            'bill_info1' => 'Barber Woi',
            'bill_info2' => 'BKG-TEST-001',
        ],
    ]);
});
