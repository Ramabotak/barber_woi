<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'expense_date' => ['required', 'date'],
            'category' => ['required', Rule::in(array_keys(Expense::CATEGORIES))],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:1', 'max:999999999999.99'],
            'payment_method' => ['required', Rule::in(array_keys(Expense::PAYMENT_METHODS))],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $receiptPath = $request->hasFile('receipt')
            ? $request->file('receipt')->store('expense-receipts', 'public')
            : null;
        unset($validated['receipt']);

        Expense::create([
            ...$validated,
            'receipt_path' => $receiptPath,
            'status' => 'pending',
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('admin.reports.index', $request->only(['period', 'start_date', 'end_date']))
            ->with('success', 'Pengeluaran berhasil dicatat dan menunggu persetujuan.');
    }

    public function approve(Request $request, Expense $expense): RedirectResponse
    {
        abort_unless($expense->status === 'pending', 422, 'Hanya pengeluaran menunggu yang dapat disetujui.');

        $expense->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_note' => null,
        ]);

        return back()->with('success', 'Pengeluaran telah disetujui dan masuk ke laporan keuangan.');
    }

    public function reject(Request $request, Expense $expense): RedirectResponse
    {
        abort_unless($expense->status === 'pending', 422, 'Hanya pengeluaran menunggu yang dapat ditolak.');

        $validated = $request->validate([
            'rejection_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $expense->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_note' => $validated['rejection_note'] ?? null,
        ]);

        return back()->with('success', 'Pengeluaran telah ditolak dan tidak dihitung sebagai biaya.');
    }
}
