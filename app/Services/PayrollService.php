<?php

namespace App\Services;

use App\Models\Barber;
use App\Models\Expense;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PayrollService
{
    /**
     * Membuat draft payroll dari skema gaji barber dan booking yang benar-benar
     * sudah selesai pada periode terpilih. Draft tidak memengaruhi laporan
     * keuangan sampai pembayaran dikonfirmasi.
     */
    public function calculate(Barber $barber, Carbon $startDate, Carbon $endDate, int $adminId): Payroll
    {
        return DB::transaction(function () use ($barber, $startDate, $endDate, $adminId) {
            $setting = $barber->compensationSetting;

            if (!$setting) {
                throw ValidationException::withMessages([
                    'barber_id' => "Skema gaji untuk {$barber->user->name} belum diatur.",
                ]);
            }

            $overlaps = Payroll::query()
                ->where('barber_id', $barber->id)
                ->where('status', '!=', 'cancelled')
                ->whereDate('period_start', '<=', $endDate)
                ->whereDate('period_end', '>=', $startDate)
                ->exists();

            if ($overlaps) {
                throw ValidationException::withMessages([
                    'period_start' => 'Sudah ada payroll aktif untuk barber dan periode yang saling tumpang tindih.',
                ]);
            }

            $completedBookings = $barber->bookings()
                ->with('payment')
                ->where('status', 'completed')
                ->whereBetween('finished_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->get();

            $completedCount = $completedBookings->count();
            $bookingRevenue = $completedBookings
                ->filter(fn ($booking) => $booking->payment?->status === 'paid')
                ->sum(fn ($booking) => (float) $booking->payment->amount);

            $baseSalary = $this->proratedMonthlySalary(
                (float) $setting->fixed_salary,
                $startDate,
                $endDate,
            );

            $commission = match ($setting->commission_type) {
                'per_booking' => $completedCount * (float) $setting->commission_value,
                'percentage' => $bookingRevenue * ((float) $setting->commission_value / 100),
                default => 0,
            };

            return Payroll::create([
                'barber_id' => $barber->id,
                'period_start' => $startDate->toDateString(),
                'period_end' => $endDate->toDateString(),
                'base_salary' => round($baseSalary, 2),
                'commission_amount' => round($commission, 2),
                'bonus_amount' => 0,
                'deduction_amount' => 0,
                'total_amount' => round($baseSalary + $commission, 2),
                'completed_bookings' => $completedCount,
                'commission_type' => $setting->commission_type,
                'commission_value' => $setting->commission_value,
                'status' => 'draft',
                'calculated_by' => $adminId,
            ]);
        });
    }

    /**
     * Ketika payroll dibayar, buat satu expense yang disetujui. Dengan relasi
     * payroll_id yang unik, pembayaran yang sama tidak mungkin tercatat dua kali.
     */
    public function markPaid(Payroll $payroll, int $adminId, string $paymentMethod): Payroll
    {
        return DB::transaction(function () use ($payroll, $adminId, $paymentMethod) {
            $payroll = Payroll::with('barber.user')->lockForUpdate()->findOrFail($payroll->id);

            if ($payroll->status !== 'draft') {
                throw ValidationException::withMessages([
                    'payroll' => 'Payroll ini tidak dapat dibayar karena statusnya bukan draft.',
                ]);
            }

            if ((float) $payroll->total_amount <= 0) {
                throw ValidationException::withMessages([
                    'payroll' => 'Total payroll harus lebih besar dari nol sebelum dibayar.',
                ]);
            }

            Expense::create([
                'expense_date' => now()->toDateString(),
                'category' => 'gaji_komisi',
                'description' => sprintf(
                    'Pembayaran gaji/komisi %s (%s - %s)',
                    $payroll->barber->user->name,
                    $payroll->period_start->format('d M Y'),
                    $payroll->period_end->format('d M Y'),
                ),
                'amount' => $payroll->total_amount,
                'payment_method' => $paymentMethod,
                'status' => 'approved',
                'created_by' => $adminId,
                'reviewed_by' => $adminId,
                'reviewed_at' => now(),
                'payroll_id' => $payroll->id,
            ]);

            $payroll->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            return $payroll->fresh('expense');
        });
    }

    public function refreshTotal(Payroll $payroll, float $bonus, float $deduction, ?string $notes): Payroll
    {
        $total = max(0, (float) $payroll->base_salary + (float) $payroll->commission_amount + $bonus - $deduction);

        $payroll->update([
            'bonus_amount' => $bonus,
            'deduction_amount' => $deduction,
            'total_amount' => $total,
            'notes' => $notes,
        ]);

        return $payroll->fresh();
    }

    private function proratedMonthlySalary(float $monthlySalary, Carbon $startDate, Carbon $endDate): float
    {
        if ($monthlySalary <= 0) {
            return 0;
        }

        $total = 0;
        $cursor = $startDate->copy()->startOfDay();
        $end = $endDate->copy()->startOfDay();

        while ($cursor->lte($end)) {
            $segmentEnd = $cursor->copy()->endOfMonth()->startOfDay();
            if ($segmentEnd->gt($end)) {
                $segmentEnd = $end->copy();
            }

            $daysInSegment = $cursor->diffInDays($segmentEnd) + 1;
            $total += ($monthlySalary / $cursor->daysInMonth) * $daysInSegment;
            $cursor = $segmentEnd->copy()->addDay();
        }

        return $total;
    }
}
