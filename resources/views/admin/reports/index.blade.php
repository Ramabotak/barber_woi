@extends('layouts.admin')

@section('title', 'Laporan Keuangan & Pengeluaran Admin - Barber Woi')

@section('content')
    @php
        $query = request()->query();
        $periodLabels = [
            'this_month' => 'Bulan Ini',
            'last_month' => 'Bulan Lalu',
            'this_year' => 'Tahun Ini',
            'custom' => 'Rentang Kustom',
        ];
        $expenseStatusClasses = [
            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
            'approved' => 'bg-green-50 text-green-700 border-green-200',
            'rejected' => 'bg-red-50 text-red-700 border-red-200',
        ];
        $showExpenseModal = $errors->any() && !request()->is('admin/payrolls*');
    @endphp

    <div x-data="{ expenseOpen: {{ $showExpenseModal ? 'true' : 'false' }} }">
        <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-4">
            <div>
                <h1 class="font-heading text-2xl md:text-3xl font-bold text-charcoal">Laporan Keuangan</h1>
                <p class="mt-1 text-sm text-muted">Pantau pendapatan, pengeluaran, dan laba usaha Anda.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <form method="GET" class="flex items-center gap-2" x-data="{ custom: {{ $selectedPeriod === 'custom' ? 'true' : 'false' }} }">
                    @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                    @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                    @if(request('transaction_status'))<input type="hidden" name="transaction_status" value="{{ request('transaction_status') }}">@endif
                    <select name="period" x-on:change="custom = $event.target.value === 'custom'; if (!custom) $el.form.submit()"
                            class="rounded-lg border-gray-300 py-2 text-sm font-medium focus:border-gold focus:ring-gold">
                        @foreach($periodLabels as $value => $label)
                            <option value="{{ $value }}" @selected($selectedPeriod === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div x-show="custom" x-cloak class="flex items-center gap-2">
                        <input type="date" name="start_date" value="{{ $startDate->toDateString() }}"
                               class="rounded-lg border-gray-300 py-2 text-sm focus:border-gold focus:ring-gold">
                        <input type="date" name="end_date" value="{{ $endDate->toDateString() }}"
                               class="rounded-lg border-gray-300 py-2 text-sm focus:border-gold focus:ring-gold">
                        <button class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium hover:bg-gray-50">Terapkan</button>
                    </div>
                </form>
                <a href="{{ route('admin.reports.export', $query) }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm font-semibold text-charcoal transition hover:bg-gray-50">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    Export CSV
                </a>
                <button type="button" x-on:click="expenseOpen = true"
                        class="inline-flex items-center gap-2 rounded-lg bg-gold px-4 py-2 text-sm font-bold text-charcoal shadow-sm transition hover:bg-gold/90">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Tambah Pengeluaran
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="mt-5 rounded-lg border border-brandsuccess/30 bg-brandsuccess/10 px-4 py-3 text-sm text-brandsuccess">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mt-5 rounded-lg border border-branddanger/30 bg-branddanger/10 px-4 py-3 text-sm text-branddanger">
                {{ session('error') }}
            </div>
        @endif

        <section class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="flex min-h-36 flex-col rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-muted">Total Pendapatan</p>
                <p class="mt-2 font-heading text-2xl font-bold text-charcoal">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                <p class="mt-auto flex items-center gap-1 text-xs font-semibold {{ $revenueChange !== null && $revenueChange < 0 ? 'text-branddanger' : 'text-brandsuccess' }}">
                    <span class="material-symbols-outlined text-[16px]">{{ $revenueChange !== null && $revenueChange < 0 ? 'trending_down' : 'trending_up' }}</span>
                    @if($revenueChange === null)
                        Belum ada pembanding
                    @else
                        {{ $revenueChange >= 0 ? '+' : '' }}{{ number_format($revenueChange, 1, ',', '.') }}% dari periode lalu
                    @endif
                </p>
            </article>

            <article class="flex min-h-36 flex-col rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-muted">Total Pengeluaran</p>
                <p class="mt-2 font-heading text-2xl font-bold text-charcoal">Rp {{ number_format($expenseTotal, 0, ',', '.') }}</p>
                <p class="mt-auto flex items-center gap-1 text-xs font-semibold {{ $expenseChange !== null && $expenseChange > 0 ? 'text-branddanger' : 'text-brandsuccess' }}">
                    <span class="material-symbols-outlined text-[16px]">{{ $expenseChange !== null && $expenseChange > 0 ? 'trending_up' : 'trending_down' }}</span>
                    @if($expenseChange === null)
                        Belum ada pembanding
                    @else
                        {{ $expenseChange >= 0 ? '+' : '' }}{{ number_format($expenseChange, 1, ',', '.') }}% dari periode lalu
                    @endif
                </p>
            </article>

            <article class="relative flex min-h-36 flex-col overflow-hidden rounded-xl bg-charcoal p-5 shadow-sm">
                <div class="absolute inset-0 bg-gradient-to-br from-gold/15 to-transparent"></div>
                <p class="relative text-[11px] font-semibold uppercase tracking-wider text-cream/60">Laba Bersih</p>
                <p class="relative mt-2 font-heading text-2xl font-bold text-gold">Rp {{ number_format($netProfit, 0, ',', '.') }}</p>
                <p class="relative mt-auto text-xs font-semibold text-cream/60">Margin laba: {{ number_format($profitMargin, 1, ',', '.') }}%</p>
            </article>

            <article class="flex min-h-36 flex-col rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-muted">Total Transaksi</p>
                <p class="mt-2 font-heading text-2xl font-bold text-charcoal">{{ number_format($totalTransactions, 0, ',', '.') }}</p>
                <p class="mt-auto flex items-center gap-1 text-xs font-semibold {{ $transactionChange !== null && $transactionChange < 0 ? 'text-branddanger' : 'text-brandsuccess' }}">
                    <span class="material-symbols-outlined text-[16px]">{{ $transactionChange !== null && $transactionChange < 0 ? 'trending_down' : 'trending_up' }}</span>
                    @if($transactionChange === null)
                        Belum ada pembanding
                    @else
                        {{ $transactionChange >= 0 ? '+' : '' }}{{ number_format($transactionChange, 1, ',', '.') }}% dari periode lalu
                    @endif
                </p>
            </article>
        </section>

        <section class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-3">
            <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm xl:col-span-2">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-heading text-lg font-bold text-charcoal">Arus Keuangan</h2>
                        <p class="text-xs text-muted">{{ $startDate->translatedFormat('d M Y') }} — {{ $endDate->translatedFormat('d M Y') }}</p>
                    </div>
                    <div class="flex items-center gap-3 text-xs font-medium text-muted">
                        <span class="inline-flex items-center gap-1.5"><i class="h-2.5 w-2.5 rounded-full bg-gold"></i>Pendapatan</span>
                        <span class="inline-flex items-center gap-1.5"><i class="h-2.5 w-2.5 rounded-full bg-gray-300"></i>Pengeluaran</span>
                    </div>
                </div>
                <div class="h-72"><canvas id="financialFlowChart"></canvas></div>
            </article>

            <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="font-heading text-lg font-bold text-charcoal">Komposisi Pengeluaran</h2>
                <p class="mt-1 text-xs text-muted">Hanya pengeluaran yang disetujui.</p>
                <div class="relative mx-auto mt-3 h-40 w-40">
                    <canvas id="expenseCompositionChart"></canvas>
                    <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center text-center">
                        <span class="text-[10px] font-semibold uppercase tracking-wide text-muted">Total</span>
                        <span class="font-heading text-sm font-bold text-charcoal">Rp {{ number_format($expenseTotal / 1000000, 1, ',', '.') }} jt</span>
                    </div>
                </div>
                <div class="mt-3 max-h-52 space-y-2 overflow-y-auto pr-1">
                    @foreach($expenseComposition as $item)
                        <div class="flex items-center justify-between gap-2 text-xs">
                            <span class="flex min-w-0 items-center gap-1.5 text-gray-600">
                                <i class="h-2 w-2 flex-none rounded-full" style="background-color: {{ $item['color'] }}"></i>
                                <span class="truncate">{{ $item['label'] }}</span>
                            </span>
                            <span class="whitespace-nowrap font-semibold text-charcoal">Rp {{ number_format($item['amount'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </article>
        </section>

        <section class="mt-5 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-gray-200 p-5 xl:flex-row xl:items-center xl:justify-between">
                <div>
                    <h2 class="font-heading text-lg font-bold text-charcoal">Riwayat Transaksi Keuangan</h2>
                    <p class="mt-1 text-xs text-muted">Pengeluaran menunggu dan ditolak tetap terlihat untuk audit.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.payrolls.index', ['start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-charcoal transition hover:bg-gray-50">
                        <span class="material-symbols-outlined text-[18px]">payments</span> Kelola Gaji Barber
                    </a>
                    <form method="GET" class="flex flex-wrap items-center gap-2">
                        <input type="hidden" name="period" value="{{ $selectedPeriod }}">
                        @if($selectedPeriod === 'custom')
                            <input type="hidden" name="start_date" value="{{ $startDate->toDateString() }}">
                            <input type="hidden" name="end_date" value="{{ $endDate->toDateString() }}">
                        @endif
                        <div class="relative">
                            <span class="material-symbols-outlined pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-[18px] text-gray-400">search</span>
                            <input name="search" value="{{ request('search') }}" placeholder="Cari transaksi..."
                                   class="w-48 rounded-lg border-gray-300 py-2 pl-9 pr-3 text-sm focus:border-gold focus:ring-gold">
                        </div>
                        <select name="category" class="rounded-lg border-gray-300 py-2 text-sm focus:border-gold focus:ring-gold">
                            <option value="">Semua kategori</option>
                            <option value="income" @selected(request('category') === 'income')>Pendapatan Layanan</option>
                            @foreach(\App\Models\Expense::CATEGORIES as $key => $label)
                                <option value="{{ $key }}" @selected(request('category') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <select name="transaction_status" class="rounded-lg border-gray-300 py-2 text-sm focus:border-gold focus:ring-gold">
                            <option value="">Semua status</option>
                            @foreach(\App\Models\Expense::STATUSES as $key => $label)
                                <option value="{{ $key }}" @selected(request('transaction_status') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="rounded-lg border border-gray-300 bg-white p-2 text-gray-600 transition hover:bg-gray-50" title="Terapkan filter">
                            <span class="material-symbols-outlined text-[19px]">filter_list</span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead class="border-b border-gray-200 bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-muted">Tanggal</th>
                            <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-muted">Keterangan</th>
                            <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-muted">Kategori</th>
                            <th class="px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-muted">Metode</th>
                            <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-muted">Pemasukan</th>
                            <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-muted">Pengeluaran</th>
                            <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-muted">Status</th>
                            <th class="px-4 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($transactions as $row)
                            <tr class="transition hover:bg-gray-50">
                                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-charcoal">{{ $row['date']->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-3 text-sm text-charcoal">
                                    <div class="max-w-64 truncate font-medium">{{ $row['description'] }}</div>
                                    @if($row['receipt_path'])
                                        <a href="{{ Storage::url($row['receipt_path']) }}" target="_blank" class="mt-0.5 inline-flex items-center gap-1 text-xs font-medium text-gold hover:underline">
                                            <span class="material-symbols-outlined text-[14px]">attach_file</span> Lihat bukti
                                        </a>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $row['category_label'] }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $row['payment_method_label'] }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-brandsuccess">
                                    {{ $row['income'] ? 'Rp '.number_format($row['income'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold {{ $row['expense'] ? 'text-branddanger' : 'text-gray-400' }}">
                                    {{ $row['expense'] ? 'Rp '.number_format($row['expense'], 0, ',', '.') : '—' }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-center">
                                    @if($row['source'] === 'income')
                                        <span class="inline-flex rounded-full border border-green-200 bg-green-50 px-2 py-1 text-[11px] font-semibold text-green-700">Selesai</span>
                                    @else
                                        <span class="inline-flex rounded-full border px-2 py-1 text-[11px] font-semibold {{ $expenseStatusClasses[$row['status']] }}">{{ $row['status_label'] }}</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    @if($row['source'] === 'expense' && $row['status'] === 'pending')
                                        <div class="flex justify-end gap-1.5">
                                            <form method="POST" action="{{ route('admin.expenses.approve', $row['model']) }}">
                                                @csrf @method('PATCH')
                                                <button class="rounded-md bg-brandsuccess px-2 py-1 text-[11px] font-semibold text-white hover:bg-brandsuccess/90">Setujui</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.expenses.reject', $row['model']) }}" onsubmit="return confirm('Tolak pengeluaran ini?')">
                                                @csrf @method('PATCH')
                                                <button class="rounded-md border border-red-200 px-2 py-1 text-[11px] font-semibold text-branddanger hover:bg-red-50">Tolak</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-sm text-muted">Belum ada transaksi pada periode atau filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex flex-col gap-3 border-t border-gray-200 px-5 py-3 text-xs text-muted sm:flex-row sm:items-center sm:justify-between">
                <span>Menampilkan {{ $transactions->firstItem() ?? 0 }}–{{ $transactions->lastItem() ?? 0 }} dari {{ $transactions->total() }} transaksi</span>
                {{ $transactions->links() }}
            </div>
        </section>

        {{-- Modal input pengeluaran. Selalu pending sehingga tidak langsung mengurangi laba. --}}
        <div x-show="expenseOpen" x-cloak x-on:keydown.escape.window="expenseOpen = false" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="expenseOpen" x-transition.opacity class="fixed inset-0 bg-charcoal/60" x-on:click="expenseOpen = false"></div>
                <section x-show="expenseOpen" x-transition class="relative w-full max-w-xl rounded-2xl bg-white p-6 shadow-2xl">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="font-heading text-xl font-bold text-charcoal">Tambah Pengeluaran</h2>
                            <p class="mt-1 text-sm text-muted">Data akan berstatus menunggu hingga disetujui.</p>
                        </div>
                        <button type="button" x-on:click="expenseOpen = false" class="rounded-lg p-1 text-gray-500 hover:bg-gray-100" aria-label="Tutup">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.expenses.store', request()->only(['period', 'start_date', 'end_date'])) }}" class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @csrf
                        <label class="block">
                            <span class="text-sm font-semibold text-charcoal">Tanggal</span>
                            <input type="date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}" required
                                   class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-gold focus:ring-gold">
                            @error('expense_date')<span class="mt-1 block text-xs text-branddanger">{{ $message }}</span>@enderror
                        </label>
                        <label class="block">
                            <span class="text-sm font-semibold text-charcoal">Kategori</span>
                            <select name="category" required class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-gold focus:ring-gold">
                                <option value="">Pilih kategori</option>
                                @foreach(\App\Models\Expense::CATEGORIES as $key => $label)
                                    <option value="{{ $key }}" @selected(old('category') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')<span class="mt-1 block text-xs text-branddanger">{{ $message }}</span>@enderror
                        </label>
                        <label class="block sm:col-span-2">
                            <span class="text-sm font-semibold text-charcoal">Keterangan</span>
                            <input type="text" name="description" value="{{ old('description') }}" maxlength="255" required placeholder="Contoh: Restock pomade dan sampo"
                                   class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-gold focus:ring-gold">
                            @error('description')<span class="mt-1 block text-xs text-branddanger">{{ $message }}</span>@enderror
                        </label>
                        <label class="block">
                            <span class="text-sm font-semibold text-charcoal">Nominal (Rp)</span>
                            <input type="number" name="amount" value="{{ old('amount') }}" min="1" step="1" required placeholder="0"
                                   class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-gold focus:ring-gold">
                            @error('amount')<span class="mt-1 block text-xs text-branddanger">{{ $message }}</span>@enderror
                        </label>
                        <label class="block">
                            <span class="text-sm font-semibold text-charcoal">Metode pembayaran</span>
                            <select name="payment_method" required class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-gold focus:ring-gold">
                                @foreach(\App\Models\Expense::PAYMENT_METHODS as $key => $label)
                                    <option value="{{ $key }}" @selected(old('payment_method') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('payment_method')<span class="mt-1 block text-xs text-branddanger">{{ $message }}</span>@enderror
                        </label>
                        <label class="block sm:col-span-2">
                            <span class="text-sm font-semibold text-charcoal">Bukti pembayaran <span class="font-normal text-muted">(opsional, JPG/PNG/PDF max. 4 MB)</span></span>
                            <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf" class="mt-1 block w-full text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-gold/15 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-charcoal hover:file:bg-gold/25">
                            @error('receipt')<span class="mt-1 block text-xs text-branddanger">{{ $message }}</span>@enderror
                        </label>
                        <div class="mt-2 flex justify-end gap-3 sm:col-span-2">
                            <button type="button" x-on:click="expenseOpen = false" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-charcoal hover:bg-gray-50">Batal</button>
                            <button class="rounded-lg bg-gold px-4 py-2 text-sm font-bold text-charcoal hover:bg-gold/90">Simpan Pengeluaran</button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const formatRupiah = (value) => 'Rp ' + Number(value || 0).toLocaleString('id-ID');
            const flowCanvas = document.getElementById('financialFlowChart');
            if (flowCanvas) {
                new Chart(flowCanvas, {
                    type: 'bar',
                    data: {
                        labels: @json($chart['labels']),
                        datasets: [
                            { label: 'Pendapatan', data: @json($chart['revenue']), backgroundColor: '#C9A24B', borderRadius: 4, barPercentage: 0.72, categoryPercentage: 0.72 },
                            { label: 'Pengeluaran', data: @json($chart['expenses']), backgroundColor: '#D1D5DB', borderRadius: 4, barPercentage: 0.72, categoryPercentage: 0.72 },
                        ],
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false }, tooltip: { callbacks: { label: (context) => context.dataset.label + ': ' + formatRupiah(context.parsed.y) } } },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: '#8A8A8E', maxRotation: 0, autoSkip: true, maxTicksLimit: 10, font: { size: 11 } } },
                            y: { beginAtZero: true, grid: { color: '#F1F5F9' }, ticks: { color: '#8A8A8E', font: { size: 11 }, callback: (value) => value >= 1000000 ? (value / 1000000) + ' jt' : value / 1000 + ' rb' } },
                        },
                    },
                });
            }

            const compositionCanvas = document.getElementById('expenseCompositionChart');
            if (compositionCanvas) {
                const amounts = @json($expenseComposition->pluck('amount')->values());
                const hasExpense = amounts.some((amount) => Number(amount) > 0);
                new Chart(compositionCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: hasExpense ? @json($expenseComposition->pluck('label')->values()) : ['Belum ada pengeluaran disetujui'],
                        datasets: [{
                            data: hasExpense ? amounts : [1],
                            backgroundColor: hasExpense ? @json($expenseComposition->pluck('color')->values()) : ['#E5E7EB'],
                            borderColor: '#FFFFFF', borderWidth: 3, hoverOffset: 4,
                        }],
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, cutout: '68%',
                        plugins: { legend: { display: false }, tooltip: { enabled: hasExpense, callbacks: { label: (context) => context.label + ': ' + formatRupiah(context.parsed) } } },
                    },
                });
            }
        });
    </script>
@endpush
