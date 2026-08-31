<?php

use App\Models\Barber;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;

it('shows paid bookings, hides unpaid accepted bookings, and lets the barber start the service', function () {
    $barberUser = User::factory()->create(['role' => 'barber']);
    $barber = Barber::create(['user_id' => $barberUser->id, 'status' => 'aktif']);
    $customer = User::factory()->create(['role' => 'customer']);
    $service = Service::create([
        'service_name' => 'Potong rambut',
        'price' => 35000,
        'duration' => 30,
        'status' => 'active',
    ]);
    $schedule = Schedule::create([
        'barber_id' => $barber->id,
        'date' => now()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '12:00',
        'status' => 'tersedia',
    ]);

    $paid = Booking::create([
        'customer_id' => $customer->id,
        'barber_id' => $barber->id,
        'service_id' => $service->id,
        'schedule_id' => $schedule->id,
        'slot_time' => '09:00',
        'status' => 'paid',
    ]);
    $unpaid = Booking::create([
        'customer_id' => $customer->id,
        'barber_id' => $barber->id,
        'service_id' => $service->id,
        'schedule_id' => $schedule->id,
        'slot_time' => '09:30',
        'status' => 'accepted',
    ]);

    $this->actingAs($barberUser)
        ->get(route('barber.queue'))
        ->assertOk()
        ->assertViewHas('bookings', function ($bookings) use ($paid, $unpaid) {
            return $bookings->pluck('id')->all() === [$paid->id]
                && ! $bookings->contains('id', $unpaid->id);
        });

    $this->actingAs($barberUser)
        ->patch(route('barber.booking.start', $paid))
        ->assertSessionHas('success');

    expect($paid->fresh()->status)->toBe('serving');
});
