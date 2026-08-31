@php
    $status = [
        'pending' => ['Menunggu persetujuan', 'schedule', 'bg-brandwarning/10 text-brandwarning border-brandwarning/20'],
        'accepted' => ['Menunggu pembayaran', 'payments', 'bg-brandwarning/10 text-brandwarning border-brandwarning/20'],
        'paid' => ['Siap masuk antrean', 'verified', 'bg-brandsuccess/10 text-brandsuccess border-brandsuccess/20'],
        'waiting' => ['Menunggu giliran', 'hourglass_top', 'bg-[#eef1ff] text-[#4557a8] border-[#cfd7ff]'],
        'late' => ['Perlu perhatian', 'priority_high', 'bg-red-50 text-branddanger border-red-200'],
        'serving' => ['Sedang dilayani', 'content_cut', 'bg-charcoal text-gold border-charcoal'],
        'completed' => ['Selesai', 'task_alt', 'bg-gray-100 text-muted border-gray-200'],
        'cancelled' => ['Dibatalkan', 'cancel', 'bg-gray-100 text-muted border-gray-200'],
    ][$booking->status] ?? ['Status booking', 'info', 'bg-gray-100 text-muted border-gray-200'];
@endphp

<a href="{{ route('customer.booking.show', $booking) }}" class="group relative overflow-hidden border border-charcoal/10 bg-white p-5 transition duration-200 hover:-translate-y-0.5 hover:border-charcoal/25 hover:shadow-[0_14px_32px_rgba(23,23,22,.08)]">
    <div class="absolute inset-y-0 left-0 w-1 bg-gold"></div>
    <div class="flex items-start justify-between gap-4 pl-2">
        <div class="min-w-0"><p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-muted">{{ $booking->booking_code }}</p><h2 class="mt-2 truncate font-heading text-lg font-extrabold tracking-tight text-charcoal">{{ $booking->service->service_name }}</h2></div>
        <span class="inline-flex shrink-0 items-center gap-1.5 border px-2.5 py-1.5 text-[10px] font-extrabold {{ $status[2] }}"><span class="material-symbols-outlined text-[15px]">{{ $status[1] }}</span>{{ $status[0] }}</span>
    </div>
    <div class="mt-5 grid grid-cols-[auto_1fr] gap-x-3 gap-y-3 border-y border-charcoal/[0.08] py-4 pl-2 text-xs">
        <span class="material-symbols-outlined text-[18px] text-brandwarning">calendar_month</span>
        <p class="text-muted">
            <span class="font-bold text-charcoal">{{ $booking->schedule?->date?->translatedFormat('l, d M Y') ?? 'Jadwal belum ditentukan' }}</span>
            @if ($booking->slot_time)
                <span class="text-charcoal/35"> · </span>{{ $booking->slot_time->format('H:i') }} WIB
            @endif
        </p>
        <span class="material-symbols-outlined text-[18px] text-brandwarning">content_cut</span><p class="truncate text-muted"><span class="font-bold text-charcoal">{{ $booking->barber->user->name }}</span><span class="text-charcoal/35"> · </span>{{ $booking->barber->experience ?: 'Barber pilihan Anda' }}</p>
    </div>
    <div class="mt-4 flex items-center justify-between pl-2"><span class="text-xs font-medium text-muted">Antrean <span class="ml-1 font-heading text-lg font-extrabold text-charcoal">#{{ str_pad($booking->queue_number, 2, '0', STR_PAD_LEFT) }}</span></span><span class="inline-flex items-center gap-1 text-xs font-extrabold text-charcoal transition group-hover:text-brandwarning">Detail<span class="material-symbols-outlined text-[17px] transition-transform group-hover:translate-x-0.5">arrow_forward</span></span></div>
</a>
