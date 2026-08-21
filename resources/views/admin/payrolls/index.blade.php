@extends('layouts.admin')

@section('title', 'Kelola Gaji Barber - Barber Woi')

@section('content')
    @php
        $payrollStatusClasses = [
            'draft' => 'bg-amber-50 text-amber-700 border-amber-200',
            'paid' => 'bg-green-50 text-green-700 border-green-200',
            'cancelled' => 'bg-red-50 text-red-700 border-red-200',
        ];
    @endphp

    <div class="flex flex-col gap-4 border-b border-gray-200 pb-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="font-heading text-2xl md:text-3xl font-bold text-charcoal">Kelola Gaji Barber</h1>
            <p class="mt-1 text-sm text-muted">Hitung otomatis dari booking selesai, lalu catat sebagai pengeluaran saat dibayar.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.reports.index', ['period' => 'custom', 'start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm font-semibold text-charcoal transition hover:bg-gray-50">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>Laporan Keuangan
            </a>
            <form method="GET" class="flex items-center gap-2">
                <input type="date" name="start_date" value="{{ $startDate->toDateString() }}" class="rounded-lg border-gray-300 py-2 text-sm focus:border-gold focus:ring-gold">
                <span class="text-xs text-muted">s.d.</span>
                <input type="date" name="end_date" value="{{ $endDate->toDateString() }}" class="rounded-lg border-gray-300 py-2 text-sm focus:border-gold focus:ring-gold">
                <button class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold hover:bg-gray-50">Terapkan</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mt-5 rounded-lg border border-brandsuccess/30 bg-brandsuccess/10 px-4 py-3 text-sm text-brandsuccess">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mt-5 rounded-lg border border-branddanger/30 bg-branddanger/10 px-4 py-3 text-sm text-branddanger">
            <p class="font-semibold">Payroll belum dapat diproses:</p>
            <ul class="mt-1 list-inside list-disc">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <section class="mt-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 border-b border-gray-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-heading text-lg font-bold text-charcoal">Buat Payroll</h2>
                <p class="mt-1 text-xs text-muted">Hanya booking berstatus selesai di periode ini yang dihitung sebagai komisi.</p>
            </div>
            <form method="POST" action="{{ route('admin.payrolls.calculate') }}" class="flex flex-wrap items-center gap-2">
                @csrf
                <select name="barber_id" required class="min-w-48 rounded-lg border-gray-300 py-2 text-sm focus:border-gold focus:ring-gold">
                    <option value="">Pilih barber</option>
                    @foreach($barbers as $barber)
                        <option value="{{ $barber->id }}" @selected(old('barber_id') == $barber->id)>{{ $barber->user->name }}{{ $barber->compensationSetting ? '' : ' — skema belum diatur' }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="period_start" value="{{ $startDate->toDateString() }}">
                <input type="hidden" name="period_end" value="{{ $endDate->toDateString() }}">
                <button class="inline-flex items-center gap-1.5 rounded-lg bg-gold px-4 py-2 text-sm font-bold text-charcoal hover:bg-gold/90">
                    <span class="material-symbols-outlined text-[18px]">calculate</span>Hitung Payroll
                </button>
            </form>
        </div>
    </section>

    <section class="mt-5">
        <div class="mb-3">
            <h2 class="font-heading text-lg font-bold text-charcoal">Skema Gaji & Komisi</h2>
            <p class="mt-1 text-xs text-muted">Gaji tetap diprorata sesuai rentang tanggal. Komisi dapat berupa nominal tiap booking selesai atau persentase dari pendapatannya.</p>
        </div>
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            @forelse($barbers as $barber)
                @php $setting = $barber->compensationSetting; @endphp
                <form method="POST" action="{{ route('admin.payrolls.settings.update', $barber) }}"
                      class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm" x-data="{ commissionType: '{{ old('commission_type', $setting?->commission_type ?? 'none') }}' }">
                    @csrf @method('PUT')
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex h-10 w-10 flex-none items-center justify-center rounded-full bg-charcoal text-sm font-bold text-gold">{{ strtoupper(substr($barber->user->name, 0, 1)) }}</div>
                            <div class="min-w-0">
                                <h3 class="truncate font-heading font-bold text-charcoal">{{ $barber->user->name }}</h3>
                                <p class="text-xs text-muted">{{ ucfirst($barber->status) }} · {{ $setting ? 'Skema tersimpan' : 'Belum ada skema' }}</p>
                            </div>
                        </div>
                        <button class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-charcoal hover:bg-gray-50">Simpan</button>
                    </div>
                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <label>
                            <span class="text-xs font-semibold text-gray-600">Gaji tetap / bulan</span>
                            <input type="number" name="fixed_salary" min="0" step="1" value="{{ old('fixed_salary', $setting?->fixed_salary ?? 0) }}" required
                                   class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-gold focus:ring-gold">
                        </label>
                        <label>
                            <span class="text-xs font-semibold text-gray-600">Jenis komisi</span>
                            <select name="commission_type" x-model="commissionType" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-gold focus:ring-gold">
                                @foreach(\App\Models\BarberCompensationSetting::COMMISSION_TYPES as $value => $label)
                                    <option value="{{ $value }}" @selected(old('commission_type', $setting?->commission_type) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span class="text-xs font-semibold text-gray-600" x-text="commissionType === 'percentage' ? 'Nilai komisi (%)' : 'Nilai komisi (Rp)'"></span>
                            <input type="number" name="commission_value" min="0" step="0.01" value="{{ old('commission_value', $setting?->commission_value ?? 0) }}" x-bind:disabled="commissionType === 'none'"
                                   class="mt-1 w-full rounded-lg border-gray-300 text-sm disabled:bg-gray-100 focus:border-gold focus:ring-gold">
                        </label>
                    </div>
                </form>
            @empty
                <div class="rounded-xl border border-dashed border-gray-300 bg-white px-5 py-12 text-center text-sm text-muted xl:col-span-2">Belum ada barber untuk dikonfigurasi.</div>
            @endforelse
        </div>
    </section>

    <section class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-1 border-b border-gray-200 p-5">
            <h2 class="font-heading text-lg font-bold text-charcoal">Riwayat Payroll</h2>
            <p class="text-xs text-muted">Draft dapat diberi bonus/potongan. Tombol bayar membuat satu pengeluaran “Gaji/Komisi Barber” secara otomatis.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[1150px] w-full text-left">
                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-muted">Barber & Periode</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-muted">Booking Selesai</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-muted">Gaji Tetap</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-muted">Komisi</th>
                        <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-muted">Penyesuaian</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-muted">Total Dibayar</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-muted">Status</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-muted">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payrolls as $payroll)
                        <tr class="align-top hover:bg-gray-50">
                            <td class="px-4 py-4">
                                <p class="text-sm font-semibold text-charcoal">{{ $payroll->barber->user->name }}</p>
                                <p class="mt-0.5 text-xs text-muted">{{ $payroll->period_start->translatedFormat('d M Y') }} — {{ $payroll->period_end->translatedFormat('d M Y') }}</p>
                            </td>
                            <td class="px-4 py-4 text-right text-sm font-semibold text-charcoal">{{ number_format($payroll->completed_bookings) }}</td>
                            <td class="px-4 py-4 text-right text-sm text-gray-600">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-right text-sm text-gray-600">
                                Rp {{ number_format($payroll->commission_amount, 0, ',', '.') }}
                                <p class="mt-0.5 text-[10px] text-muted">
                                    @if($payroll->commission_type === 'per_booking') Rp {{ number_format($payroll->commission_value, 0, ',', '.') }}/booking
                                    @elseif($payroll->commission_type === 'percentage') {{ number_format($payroll->commission_value, 2, ',', '.') }}%
                                    @else Tanpa komisi @endif
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                @if($payroll->status === 'draft')
                                    <form method="POST" action="{{ route('admin.payrolls.update', $payroll) }}" class="grid w-52 gap-1.5">
                                        @csrf @method('PATCH')
                                        <input type="number" name="bonus_amount" min="0" step="1" value="{{ $payroll->bonus_amount }}" placeholder="Bonus (Rp)" class="rounded-md border-gray-300 py-1.5 text-xs focus:border-gold focus:ring-gold">
                                        <input type="number" name="deduction_amount" min="0" step="1" value="{{ $payroll->deduction_amount }}" placeholder="Potongan (Rp)" class="rounded-md border-gray-300 py-1.5 text-xs focus:border-gold focus:ring-gold">
                                        <input type="text" name="notes" value="{{ $payroll->notes }}" maxlength="1000" placeholder="Catatan (opsional)" class="rounded-md border-gray-300 py-1.5 text-xs focus:border-gold focus:ring-gold">
                                        <button class="justify-self-start rounded-md border border-gray-300 px-2 py-1 text-[11px] font-semibold text-charcoal hover:bg-gray-50">Simpan</button>
                                    </form>
                                @else
                                    <p class="text-xs text-gray-600">Bonus: Rp {{ number_format($payroll->bonus_amount, 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-600">Potongan: Rp {{ number_format($payroll->deduction_amount, 0, ',', '.') }}</p>
                                    @if($payroll->notes)<p class="mt-1 max-w-52 text-[11px] text-muted">{{ $payroll->notes }}</p>@endif
                                @endif
                            </td>
                            <td class="px-4 py-4 text-right text-sm font-bold text-charcoal">Rp {{ number_format($payroll->total_amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex rounded-full border px-2 py-1 text-[11px] font-semibold {{ $payrollStatusClasses[$payroll->status] }}">{{ $payroll->status_label }}</span>
                                @if($payroll->paid_at)<p class="mt-1 text-[10px] text-muted">{{ $payroll->paid_at->translatedFormat('d M Y') }}</p>@endif
                            </td>
                            <td class="px-4 py-4 text-right">
                                @if($payroll->status === 'draft')
                                    <form method="POST" action="{{ route('admin.payrolls.pay', $payroll) }}" class="inline-flex items-center gap-1" onsubmit="return confirm('Tandai gaji ini sudah dibayar? Pengeluaran akan otomatis dicatat.')">
                                        @csrf @method('PATCH')
                                        <select name="payment_method" class="rounded-md border-gray-300 py-1.5 text-xs focus:border-gold focus:ring-gold">
                                            @foreach(\App\Models\Expense::PAYMENT_METHODS as $key => $label)
                                                <option value="{{ $key }}" @selected($key === 'transfer')>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <button class="rounded-md bg-gold px-2.5 py-1.5 text-xs font-bold text-charcoal hover:bg-gold/90">Bayar</button>
                                    </form>
                                @elseif($payroll->expense)
                                    <a href="{{ route('admin.reports.index', ['period' => 'custom', 'start_date' => $payroll->period_start->toDateString(), 'end_date' => now()->toDateString(), 'search' => $payroll->barber->user->name]) }}"
                                       class="text-xs font-semibold text-gold hover:underline">Lihat pengeluaran</a>
                                @else
                                    <span class="text-xs text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-12 text-center text-sm text-muted">Belum ada payroll untuk periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between border-t border-gray-200 px-5 py-3">
            <span class="text-xs text-muted">Menampilkan {{ $payrolls->firstItem() ?? 0 }}–{{ $payrolls->lastItem() ?? 0 }} dari {{ $payrolls->total() }} payroll</span>
            {{ $payrolls->links() }}
        </div>
    </section>
@endsection
