<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private const EXPENSE_COLORS = [
        'gaji_komisi' => '#C9A24B',
        'produk_stok' => '#1C1C1E',
        'operasional' => '#8A8A8E',
        'sewa' => '#A67C52',
        'perawatan_alat' => '#D6C5A5',
        'marketing' => '#6B7280',
        'lainnya' => '#E5E7EB',
    ];

    public function index(Request $request): View
    {
        return view('admin.reports.index', $this->reportData($request));
    }

    public function export(Request $request): StreamedResponse
    {
        $data = $this->reportData($request, false);
        $filename = 'laporan-keuangan-'.$data['startDate']->format('Ymd').'-'.$data['endDate']->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM supaya Excel menampilkan teks Indonesia dengan benar.
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Tanggal', 'Keterangan', 'Kategori', 'Metode Pembayaran', 'Pemasukan', 'Pengeluaran', 'Status']);

            foreach ($data['transactionRows'] as $row) {
                fputcsv($handle, [
                    $row['date']->format('d/m/Y'),
                    $row['description'],
                    $row['category_label'],
                    $row['payment_method_label'],
                    $row['income'] ?: '',
                    $row['expense'] ?: '',
                    $row['status_label'],
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function reportData(Request $request, bool $paginate = true): array
    {
        [$startDate, $endDate, $period] = $this->resolvePeriod($request);
        $startAt = $startDate->copy()->startOfDay();
        $endAt = $endDate->copy()->endOfDay();

        $payments = Payment::with(['booking.customer', 'booking.service'])
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startAt, $endAt])
            ->orderByDesc('paid_at')
            ->get();

        $allExpenses = Expense::with(['payroll.barber.user'])
            ->whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->get();

        $approvedExpenses = $allExpenses->where('status', 'approved')->values();
        $revenue = (float) $payments->sum('amount');
        $expenseTotal = (float) $approvedExpenses->sum('amount');
        $netProfit = $revenue - $expenseTotal;

        [$previousStart, $previousEnd] = $this->previousPeriod($startDate, $endDate);
        $previousRevenue = (float) Payment::where('status', 'paid')
            ->whereBetween('paid_at', [$previousStart->startOfDay(), $previousEnd->endOfDay()])
            ->sum('amount');
        $previousExpenses = (float) Expense::approved()
            ->whereBetween('expense_date', [$previousStart->toDateString(), $previousEnd->toDateString()])
            ->sum('amount');

        $transactionRows = $this->transactionRows($payments, $allExpenses, $request);
        $transactions = $paginate ? $this->paginate($transactionRows, $request) : $transactionRows;
        $currentTransactionCount = $payments->count() + $approvedExpenses->count();
        $previousTransactionCount = Payment::where('status', 'paid')
            ->whereBetween('paid_at', [$previousStart->startOfDay(), $previousEnd->endOfDay()])
            ->count()
            + Expense::approved()->whereBetween('expense_date', [$previousStart->toDateString(), $previousEnd->toDateString()])->count();

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedPeriod' => $period,
            'revenue' => $revenue,
            'expenseTotal' => $expenseTotal,
            'netProfit' => $netProfit,
            'profitMargin' => $revenue > 0 ? ($netProfit / $revenue) * 100 : 0,
            'totalTransactions' => $currentTransactionCount,
            'revenueChange' => $this->percentageChange($revenue, $previousRevenue),
            'expenseChange' => $this->percentageChange($expenseTotal, $previousExpenses),
            'transactionChange' => $this->percentageChange($currentTransactionCount, $previousTransactionCount),
            'chart' => $this->chartData($payments, $approvedExpenses, $startDate, $endDate),
            'expenseComposition' => $this->expenseComposition($approvedExpenses),
            'transactions' => $transactions,
            'transactionRows' => $transactionRows,
        ];
    }

    private function resolvePeriod(Request $request): array
    {
        $request->validate([
            'period' => ['nullable', Rule::in(['this_month', 'last_month', 'this_year', 'custom'])],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', Rule::in(array_merge(['income'], array_keys(Expense::CATEGORIES)))],
            'transaction_status' => ['nullable', Rule::in(array_keys(Expense::STATUSES))],
        ]);

        $period = $request->input('period', 'this_month');
        $today = now();

        return match ($period) {
            'last_month' => [$today->copy()->subMonthNoOverflow()->startOfMonth(), $today->copy()->subMonthNoOverflow()->endOfMonth(), $period],
            'this_year' => [$today->copy()->startOfYear(), $today->copy()->endOfYear(), $period],
            'custom' => [
                Carbon::parse($request->input('start_date', $today->copy()->startOfMonth()->toDateString()))->startOfDay(),
                Carbon::parse($request->input('end_date', $today->copy()->endOfMonth()->toDateString()))->endOfDay(),
                $period,
            ],
            default => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth(), 'this_month'],
        };
    }

    private function previousPeriod(Carbon $startDate, Carbon $endDate): array
    {
        $days = $startDate->diffInDays($endDate) + 1;
        $previousEnd = $startDate->copy()->subDay()->endOfDay();
        $previousStart = $previousEnd->copy()->subDays($days - 1)->startOfDay();

        return [$previousStart, $previousEnd];
    }

    private function percentageChange(float|int $current, float|int $previous): ?float
    {
        if ((float) $previous === 0.0) {
            return (float) $current === 0.0 ? 0.0 : null;
        }

        return (($current - $previous) / abs($previous)) * 100;
    }

    private function chartData(Collection $payments, Collection $approvedExpenses, Carbon $startDate, Carbon $endDate): array
    {
        $isDaily = $startDate->diffInDays($endDate) <= 62;
        $labels = [];
        $revenue = [];
        $expenses = [];

        if ($isDaily) {
            for ($date = $startDate->copy()->startOfDay(); $date->lte($endDate); $date->addDay()) {
                $labels[] = $date->translatedFormat('d M');
                $revenue[] = (float) $payments->filter(fn ($payment) => $payment->paid_at?->isSameDay($date))->sum('amount');
                $expenses[] = (float) $approvedExpenses->filter(fn ($expense) => $expense->expense_date?->isSameDay($date))->sum('amount');
            }
        } else {
            for ($date = $startDate->copy()->startOfMonth(); $date->lte($endDate); $date->addMonth()) {
                $labels[] = $date->translatedFormat('M Y');
                $revenue[] = (float) $payments->filter(fn ($payment) => $payment->paid_at?->isSameMonth($date))->sum('amount');
                $expenses[] = (float) $approvedExpenses->filter(fn ($expense) => $expense->expense_date?->isSameMonth($date))->sum('amount');
            }
        }

        return compact('labels', 'revenue', 'expenses');
    }

    private function expenseComposition(Collection $approvedExpenses): Collection
    {
        $totals = $approvedExpenses->groupBy('category')->map(fn (Collection $items) => (float) $items->sum('amount'));

        return collect(Expense::CATEGORIES)
            ->map(fn (string $label, string $key) => [
                'key' => $key,
                'label' => $label,
                'amount' => $totals->get($key, 0),
                'color' => self::EXPENSE_COLORS[$key],
            ])
            ->values();
    }

    private function transactionRows(Collection $payments, Collection $expenses, Request $request): Collection
    {
        $incomeRows = $payments->map(function (Payment $payment) {
            $booking = $payment->booking;
            $serviceName = $booking?->service?->service_name ?? 'Layanan barber';
            $customerName = $booking?->customer?->name;

            return [
                'source' => 'income', 'model' => $payment, 'date' => $payment->paid_at,
                'description' => trim($serviceName . ($customerName ? ' — ' . $customerName : '')),
                'category' => 'income', 'category_label' => 'Pendapatan Layanan',
                'payment_method_label' => $payment->payment_method ? ucfirst(str_replace('_', ' ', $payment->payment_method)) : '-',
                'income' => (float) $payment->amount, 'expense' => 0,
                'status' => 'completed', 'status_label' => 'Selesai', 'receipt_path' => null,
            ];
        });

        $expenseRows = $expenses->map(function (Expense $expense) {
            return [
                'source' => 'expense', 'model' => $expense, 'date' => $expense->expense_date,
                'description' => $expense->description, 'category' => $expense->category,
                'category_label' => $expense->category_label,
                'payment_method_label' => $expense->payment_method ? (Expense::PAYMENT_METHODS[$expense->payment_method] ?? ucfirst($expense->payment_method)) : '-',
                'income' => 0, 'expense' => (float) $expense->amount,
                'status' => $expense->status, 'status_label' => $expense->status_label,
                'receipt_path' => $expense->receipt_path,
            ];
        });

        $rows = $incomeRows->concat($expenseRows);

        if ($request->filled('category')) {
            $rows = $rows->where('category', $request->string('category')->toString());
        }
        if ($request->filled('transaction_status')) {
            $rows = $rows->filter(fn (array $row) => $row['source'] === 'expense' && $row['status'] === $request->string('transaction_status')->toString());
        }
        if ($request->filled('search')) {
            $needle = Str::lower($request->string('search')->trim()->toString());
            $rows = $rows->filter(function (array $row) use ($needle) {
                return Str::contains(Str::lower(implode(' ', [
                    $row['description'], $row['category_label'], $row['payment_method_label'], $row['status_label'],
                ])), $needle);
            });
        }

        return $rows->sortByDesc('date')->values();
    }

    private function paginate(Collection $items, Request $request): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );
    }
}
