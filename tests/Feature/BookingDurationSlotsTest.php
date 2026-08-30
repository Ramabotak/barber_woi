<?php

use App\Models\Barber;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Validation\ValidationException;

it('only offers start times whose entire service duration is free', function () {
    $customer = User::factory()->create(['role' => 'customer']);
    $barberUser = User::factory()->create(['role' => 'barber']);
    $barber = Barber::create(['user_id' => $barberUser->id, 'status' => 'aktif']);
    $cut = Service::create(['service_name' => 'Potong rambut', 'price' => 35000, 'duration' => 30, 'status' => 'active']);
    $coloring = Service::create(['service_name' => 'Hair coloring', 'price' => 150000, 'duration' => 90, 'status' => 'active']);
    $schedule = Schedule::create([
        'barber_id' => $barber->id,
        'date' => now()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '12:00',
        'status' => 'tersedia',
    ]);

    // Kotak 11:00-11:30 terpakai. Maka coloring 09:30-11:00 masih boleh,
    // sedangkan 10:00-11:30 dan semua start yang lebih akhir tidak boleh.
    Booking::create([
        'customer_id' => $customer->id,
        'barber_id' => $barber->id,
        'service_id' => $cut->id,
        'schedule_id' => $schedule->id,
        'slot_time' => '11:00',
        'status' => 'pending',
    ]);

    $this->actingAs($customer)
        ->getJson(route('customer.booking.schedules', ['barber' => $barber, 'service_id' => $coloring->id]))
        ->assertOk()
        ->assertJsonPath('0.locked_slots', 3)
        ->assertJsonPath('0.slots.0.available', true)  // 09:00-10:30
        ->assertJsonPath('0.slots.1.available', true)  // 09:30-11:00
        ->assertJsonPath('0.slots.2.available', false) // 10:00-11:30 overlaps 11:00
        ->assertJsonPath('0.slots.3.available', false); // cannot fit before 12:00
});

it('rejects a booking that overlaps any locked slot of another service', function () {
    $customer = User::factory()->create(['role' => 'customer']);
    $barberUser = User::factory()->create(['role' => 'barber']);
    $barber = Barber::create(['user_id' => $barberUser->id, 'status' => 'aktif']);
    $cut = Service::create(['service_name' => 'Potong rambut', 'price' => 35000, 'duration' => 30, 'status' => 'active']);
    $coloring = Service::create(['service_name' => 'Hair coloring', 'price' => 150000, 'duration' => 90, 'status' => 'active']);
    $schedule = Schedule::create([
        'barber_id' => $barber->id,
        'date' => now()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '12:00',
        'status' => 'tersedia',
    ]);

    app(BookingService::class)->createBooking($customer->id, $barber->id, $coloring->id, $schedule->id, '09:00');

    expect(fn () => app(BookingService::class)->createBooking(
        $customer->id,
        $barber->id,
        $cut->id,
        $schedule->id,
        '10:00',
    ))->toThrow(ValidationException::class);
});
