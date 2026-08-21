<?php

use App\Models\Barber;
use App\Models\BarberCompensationSetting;
use App\Models\Booking;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use App\Services\PayrollService;
use Carbon\Carbon;

function financeAdmin(): User
{
    return User::factory()->create(['role' => 'admin']);
}

it('only includes approved expenses in the financial totals', function () {
    $admin = financeAdmin();
    $date = now()->startOfMonth()->toDateString();

    Expense::create([
        'expense_date' => $date,
        'category' => 'operasional',
        'description' => 'Tagihan listrik',
        'amount' => 200000,
        'payment_method' => 'transfer',
        'status' => 'approved',
        'created_by' => $admin->id,
        'reviewed_by' => $admin->id,
        'reviewed_at' => now(),
    ]);
    Expense::create([
        'expense_date' => $date,
        'category' => 'marketing',
        'description' => 'Iklan media sosial',
        'amount' => 100000,
        'payment_method' => 'qris',
        'status' => 'pending',
        'created_by' => $admin->id,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.reports.index', ['period' => 'this_month']))
        ->assertOk()
        ->assertSee('Rp 200.000')
        ->assertSee('Menunggu Persetujuan');
});

it('creates one approved expense when a calculated payroll is paid', function () {
    Carbon::setTestNow(Carbon::parse('2026-08-20 12:00:00'));

    $admin = financeAdmin();
    $barberUser = User::factory()->create(['role' => 'barber', 'name' => 'Andi']);
    $customer = User::factory()->create(['role' => 'customer']);
    $barber = Barber::create(['user_id' => $barberUser->id, 'status' => 'aktif']);
    $service = Service::create(['service_name' => 'Haircut', 'price' => 50000, 'status' => 'active']);
    $schedule = Schedule::create([
        'barber_id' => $barber->id,
        'date' => '2026-08-15',
        'start_time' => '09:00',
        'end_time' => '17:00',
        'status' => 'tersedia',
    ]);
    $booking = Booking::create([
        'booking_code' => 'BKG-20260815-001',
        'customer_id' => $customer->id,
        'barber_id' => $barber->id,
        'service_id' => $service->id,
        'schedule_id' => $schedule->id,
        'slot_time' => '09:00',
        'queue_number' => 1,
        'status' => 'completed',
        'finished_at' => '2026-08-15 10:00:00',
    ]);
    Payment::create([
        'booking_id' => $booking->id,
        'amount' => 50000,
        'payment_method' => 'qris',
        'status' => 'paid',
        'transaction_id' => 'PAYROLL-TEST-001',
        'paid_at' => '2026-08-15 08:00:00',
    ]);
    BarberCompensationSetting::create([
        'barber_id' => $barber->id,
        'fixed_salary' => 3000000,
        'commission_type' => 'per_booking',
        'commission_value' => 10000,
    ]);

    $service = app(PayrollService::class);
    $payroll = $service->calculate($barber->fresh(['user', 'compensationSetting']), Carbon::parse('2026-08-01'), Carbon::parse('2026-08-31'), $admin->id);
    $service->markPaid($payroll, $admin->id, 'transfer');

    expect($payroll->fresh()->status)->toBe('paid')
        ->and(Expense::where('payroll_id', $payroll->id)->where('status', 'approved')->count())->toBe(1)
        ->and((float) $payroll->fresh()->total_amount)->toBe(3010000.0);

    Carbon::setTestNow();
});
