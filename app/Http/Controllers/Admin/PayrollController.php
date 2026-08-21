<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\BarberCompensationSetting;
use App\Models\Expense;
use App\Models\Payroll;
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function __construct(protected PayrollService $payrollService)
    {
    }

    public function index(Request $request): View
    {
        [$startDate, $endDate] = $this->validatedPeriod($request);

        $barbers = Barber::with(['user', 'compensationSetting'])->orderBy('status')->get();
        $payrolls = Payroll::with(['barber.user', 'expense'])
            ->whereDate('period_start', '<=', $endDate)
            ->whereDate('period_end', '>=', $startDate)
            ->latest('period_end')
            ->paginate(12)
            ->withQueryString();

        return view('admin.payrolls.index', compact('barbers', 'payrolls', 'startDate', 'endDate'));
    }

    public function updateSetting(Request $request, Barber $barber): RedirectResponse
    {
        $validated = $request->validate([
            'fixed_salary' => ['required', 'numeric', 'min:0', 'max:999999999999.99'],
            'commission_type' => ['required', Rule::in(array_keys(BarberCompensationSetting::COMMISSION_TYPES))],
            'commission_value' => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
        ]);

        $commissionValue = $validated['commission_type'] === 'none'
            ? 0
            : (float) ($validated['commission_value'] ?? 0);

        BarberCompensationSetting::updateOrCreate(
            ['barber_id' => $barber->id],
            [
                'fixed_salary' => $validated['fixed_salary'],
                'commission_type' => $validated['commission_type'],
                'commission_value' => $commissionValue,
            ],
        );

        return back()->with('success', "Skema gaji {$barber->user->name} berhasil disimpan.");
    }

    public function calculate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'barber_id' => ['required', 'exists:barbers,id'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
        ]);

        $barber = Barber::with(['user', 'compensationSetting'])->findOrFail($validated['barber_id']);
        $this->payrollService->calculate(
            $barber,
            Carbon::parse($validated['period_start']),
            Carbon::parse($validated['period_end']),
            $request->user()->id,
        );

        return redirect()->route('admin.payrolls.index', [
            'start_date' => $validated['period_start'],
            'end_date' => $validated['period_end'],
        ])->with('success', 'Payroll berhasil dihitung sebagai draft. Periksa penyesuaian sebelum membayar.');
    }

    public function update(Request $request, Payroll $payroll): RedirectResponse
    {
        abort_unless($payroll->status === 'draft', 422, 'Hanya payroll draft yang dapat diubah.');

        $validated = $request->validate([
            'bonus_amount' => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
            'deduction_amount' => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->payrollService->refreshTotal(
            $payroll,
            (float) ($validated['bonus_amount'] ?? 0),
            (float) ($validated['deduction_amount'] ?? 0),
            $validated['notes'] ?? null,
        );

        return back()->with('success', 'Penyesuaian payroll berhasil disimpan.');
    }

    public function pay(Request $request, Payroll $payroll): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => ['required', Rule::in(array_keys(Expense::PAYMENT_METHODS))],
        ]);

        $this->payrollService->markPaid($payroll, $request->user()->id, $validated['payment_method']);

        return back()->with('success', 'Gaji barber telah ditandai dibayar dan otomatis tercatat sebagai pengeluaran.');
    }

    private function validatedPeriod(Request $request): array
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        return [
            Carbon::parse($request->input('start_date', now()->startOfMonth()->toDateString()))->startOfDay(),
            Carbon::parse($request->input('end_date', now()->endOfMonth()->toDateString()))->endOfDay(),
        ];
    }
}
