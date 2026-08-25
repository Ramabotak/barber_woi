@extends('layouts.customer')

@section('title', 'Booking - Barber Woi')

@section('content')
    <div class="mb-8 text-center">
        <p class="mb-2 text-[11px] font-bold uppercase tracking-[0.18em] text-gold">Booking Barber Woi</p>
        <h1 class="font-heading text-2xl font-bold tracking-tight text-charcoal sm:text-3xl">Pilih Barber &amp; Layanan</h1>
        <p class="mt-2 text-sm text-muted">Tentukan preferensi grooming Anda dalam beberapa langkah mudah.</p>
    </div>

    <div class="relative mx-auto mb-9 grid max-w-2xl grid-cols-4 gap-1">
        <div class="absolute left-[12%] right-[12%] top-4 h-px bg-gray-200"></div>
        @foreach(['Barber', 'Layanan', 'Jadwal', 'Konfirmasi'] as $step => $label)
            <div class="relative z-10 flex flex-col items-center" data-progress-step="{{ $step + 1 }}"><span class="flow-circle flex h-8 w-8 items-center justify-center rounded-full border-2 text-xs font-bold {{ $step < 2 ? 'border-charcoal bg-charcoal text-white' : 'border-gray-200 bg-cream text-muted' }}">{{ $step + 1 }}</span><span class="flow-label mt-2 text-center text-[10px] font-semibold {{ $step < 2 ? 'text-charcoal' : 'text-muted' }}">{{ $label }}</span></div>
        @endforeach
    </div>

    @if($errors->any())
        <div class="bg-red-50 text-red-700 p-3 rounded mb-4">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('customer.booking.store') }}" method="POST" id="booking-form">
        @csrf
        <input type="hidden" name="barber_id" id="selected_barber_id">
        <input type="hidden" name="service_id" id="selected_service_id">
        <input type="hidden" name="schedule_id" id="selected_schedule_id">
        <input type="hidden" name="slot_time" id="selected_slot_time">

        {{-- Step 1: Pilih Barber --}}
        <div id="barber-step" class="mb-7">
            <div class="mb-4 flex items-end justify-between border-b border-gray-200 pb-3"><h2 class="font-heading text-lg font-bold text-charcoal">1. Pilih Barber</h2><span class="text-[10px] font-bold uppercase tracking-wider text-muted">Wajib</span></div>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                @forelse($barbers as $barber)
                    <label class="barber-option group relative cursor-pointer overflow-hidden rounded-xl border border-gray-200 bg-white p-3 text-center transition hover:border-gold hover:shadow-md"
                           data-barber-id="{{ $barber->id }}">
                        <input type="radio" name="barber_radio" value="{{ $barber->id }}" class="hidden">
                        <span class="selection-check pointer-events-none absolute right-2 top-2 hidden h-6 w-6 items-center justify-center rounded-full bg-gold text-charcoal"><span class="material-symbols-outlined text-[16px]">check</span></span>
                        <div class="mx-auto mb-3 flex aspect-square w-full items-center justify-center overflow-hidden rounded-lg bg-cream text-charcoal">
                            @if($barber->photo)
                                <img src="{{ Storage::url($barber->photo) }}" class="w-full h-full object-cover" alt="{{ $barber->user->name }}">
                            @else
                                <span class="font-heading text-2xl font-bold">{{ strtoupper(substr($barber->user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <p class="truncate text-sm font-bold text-charcoal">{{ $barber->user->name }}</p>
                        @if($barber->reviews_count > 0)
                            <p class="mt-1 text-xs text-gold">★ {{ number_format($barber->reviews_avg_rating, 1) }} <span class="text-muted">({{ $barber->reviews_count }})</span></p>
                        @else
                            <p class="mt-1 text-xs text-muted">Belum ada ulasan</p>
                        @endif
                        @if($barber->schedules->isNotEmpty())
                            <p class="mt-2 text-[10px] font-semibold leading-tight text-brandsuccess">
                                Hari: {{ $barber->schedules->take(3)->map(fn($s) => \Illuminate\Support\Carbon::parse($s->date)->locale('id')->translatedFormat('D, d M'))->implode(' · ') }}
                            </p>
                        @endif
                    </label>
                @empty
                    <p class="col-span-full text-gray-400 text-sm">Tidak ada barber tersedia.</p>
                @endforelse
            </div>
        </div>

        {{-- Step 2: Pilih Layanan --}}
        <div id="service-step" class="mb-7">
            <div class="mb-4 flex items-end justify-between border-b border-gray-200 pb-3"><h2 class="font-heading text-lg font-bold text-charcoal">2. Pilih Layanan</h2><span class="text-[10px] font-bold uppercase tracking-wider text-muted">Wajib</span></div>
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                @forelse($services as $service)
                    <label class="service-option group relative flex cursor-pointer items-center justify-between rounded-xl border border-gray-200 bg-white p-4 transition hover:border-gold hover:shadow-md"
                           data-service-id="{{ $service->id }}">
                        <input type="radio" name="service_radio" value="{{ $service->id }}" class="hidden">
                        <span class="selection-check pointer-events-none absolute right-3 top-3 hidden h-6 w-6 items-center justify-center rounded-full bg-gold text-charcoal"><span class="material-symbols-outlined text-[16px]">check</span></span>
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-cream text-gold"><span class="material-symbols-outlined">content_cut</span></span>
                            <div><p class="truncate pr-7 text-sm font-bold text-charcoal">{{ $service->service_name }}</p><p class="mt-1 text-xs text-muted"><span class="material-symbols-outlined mr-1 align-[-3px] text-[14px]">schedule</span>{{ $service->duration }} menit</p></div>
                        </div>
                        <p class="ml-3 whitespace-nowrap text-sm font-bold text-gold">Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                    </label>
                @empty
                    <p class="col-span-full text-gray-400 text-sm">Tidak ada layanan tersedia.</p>
                @endforelse
            </div>
        </div>

        <div id="choice-actions" class="mb-7 flex justify-end border-t border-gray-200 pt-5">
            <button type="button" id="schedule-next-btn" disabled class="inline-flex min-h-11 items-center justify-center rounded-lg bg-charcoal px-5 py-3 text-sm font-bold text-white transition hover:bg-charcoal/90 disabled:cursor-not-allowed disabled:opacity-40">Lanjut Pilih Jadwal <span class="material-symbols-outlined ml-2 text-[18px]">arrow_forward</span></button>
        </div>

        {{-- Step 3: Pilih Hari & Jam --}}
        <div id="schedule-step" class="mb-7 hidden rounded-xl border border-gray-200 bg-white p-5 sm:p-6">
            <div class="mb-4 flex items-start justify-between gap-4"><div><button type="button" id="back-to-choices" class="mb-2 inline-flex items-center text-xs font-bold text-muted hover:text-charcoal"><span class="material-symbols-outlined mr-1 text-[16px]">arrow_back</span>Ubah pilihan</button><h2 class="font-heading text-lg font-bold text-charcoal">3. Pilih Hari & Jam</h2></div><span class="rounded-full bg-gold/10 px-2.5 py-1 text-[10px] font-bold text-brandwarning">WIB</span></div>
            <p class="text-xs text-gray-400 mb-3">Pilih hari, lalu pilih jam (tiap 30 menit) yang tersedia.</p>
            <div id="schedule-container">
                <p class="text-gray-400 text-sm">Pilih barber terlebih dahulu untuk melihat jadwal tersedia.</p>
            </div>
        </div>

        {{-- Step 4: Konfirmasi --}}
        <div id="confirmation-step" class="hidden rounded-xl border border-gray-200 bg-white p-5 sm:p-6">
            <h2 class="font-heading mb-2 text-lg font-bold text-charcoal">4. Konfirmasi</h2>
            <p class="text-sm text-gray-500 mb-4">Pastikan barber, layanan, dan jam yang dipilih sudah benar sebelum konfirmasi.</p>
            <div id="summary-box" class="hidden mb-4 rounded-xl border border-gold/20 bg-gold/10 p-4 text-sm"></div>
            <button type="submit" id="submit-btn" disabled
                    class="rounded-lg bg-gold px-6 py-3 text-sm font-bold text-charcoal transition hover:bg-[#dbb45d] disabled:cursor-not-allowed disabled:opacity-40">
                Konfirmasi Booking
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    const barberOptions = document.querySelectorAll('.barber-option');
    const serviceOptions = document.querySelectorAll('.service-option');
    const scheduleContainer = document.getElementById('schedule-container');
    const submitBtn = document.getElementById('submit-btn');
    const summaryBox = document.getElementById('summary-box');
    const scheduleNextBtn = document.getElementById('schedule-next-btn');
    const barberStep = document.getElementById('barber-step');
    const serviceStep = document.getElementById('service-step');
    const choiceActions = document.getElementById('choice-actions');
    const scheduleStep = document.getElementById('schedule-step');
    const confirmationStep = document.getElementById('confirmation-step');

    const selectedBarberInput = document.getElementById('selected_barber_id');
    const selectedServiceInput = document.getElementById('selected_service_id');
    const selectedScheduleInput = document.getElementById('selected_schedule_id');
    const selectedSlotTimeInput = document.getElementById('selected_slot_time');

    const state = {
        barberName: '',
        serviceName: '',
        selectedScheduleId: null,
        selectedScheduleLabel: '',
        selectedSlot: '',
        selectedSlotLabel: '',
    };

    function checkReadyToSubmit() {
        const choicesReady = selectedBarberInput.value && selectedServiceInput.value;
        const bookingReady = choicesReady && selectedScheduleInput.value && selectedSlotTimeInput.value;
        scheduleNextBtn.disabled = !choicesReady;
        submitBtn.disabled = !bookingReady;
        if (bookingReady) confirmationStep.classList.remove('hidden');
        renderSummary();
    }

    function updateProgress(activeStep) {
        document.querySelectorAll('[data-progress-step]').forEach((item) => {
            const step = Number(item.dataset.progressStep);
            const circle = item.querySelector('.flow-circle');
            const label = item.querySelector('.flow-label');
            circle.className = 'flow-circle flex h-8 w-8 items-center justify-center rounded-full border-2 text-xs font-bold ' + (step <= activeStep ? 'border-charcoal bg-charcoal text-white' : 'border-gray-200 bg-cream text-muted');
            label.className = 'flow-label mt-2 text-center text-[10px] font-semibold ' + (step <= activeStep ? 'text-charcoal' : 'text-muted');
        });
    }

    scheduleNextBtn.addEventListener('click', () => {
        barberStep.classList.add('hidden');
        serviceStep.classList.add('hidden');
        choiceActions.classList.add('hidden');
        scheduleStep.classList.remove('hidden');
        updateProgress(3);
        scheduleStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    document.getElementById('back-to-choices').addEventListener('click', () => {
        scheduleStep.classList.add('hidden');
        confirmationStep.classList.add('hidden');
        barberStep.classList.remove('hidden');
        serviceStep.classList.remove('hidden');
        choiceActions.classList.remove('hidden');
        updateProgress(2);
        barberStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    function renderSummary() {
        if (selectedBarberInput.value && selectedServiceInput.value && selectedScheduleInput.value && selectedSlotTimeInput.value) {
            summaryBox.classList.remove('hidden');
            summaryBox.innerHTML = `<p class="mb-3 text-[10px] font-bold uppercase tracking-wider text-brandwarning">Ringkasan Booking</p><div class="grid gap-2 text-xs sm:grid-cols-3"><p><span class="block text-muted">Barber</span><span class="font-bold text-charcoal">${state.barberName}</span></p><p><span class="block text-muted">Layanan</span><span class="font-bold text-charcoal">${state.serviceName}</span></p><p><span class="block text-muted">Jadwal</span><span class="font-bold text-charcoal">${state.selectedScheduleLabel}, ${state.selectedSlotLabel}</span></p></div>`;
        } else {
            summaryBox.classList.add('hidden');
        }
    }

    barberOptions.forEach(option => {
        option.addEventListener('click', () => {
            barberOptions.forEach(o => {
                o.classList.remove('border-gold', 'bg-gold/5', 'ring-1', 'ring-gold');
                o.querySelector('.selection-check')?.classList.add('hidden');
                o.querySelector('.selection-check')?.classList.remove('flex');
            });
            option.classList.add('border-gold', 'bg-gold/5', 'ring-1', 'ring-gold');
            option.querySelector('.selection-check')?.classList.remove('hidden');
            option.querySelector('.selection-check')?.classList.add('flex');

            const barberId = option.dataset.barberId;
            state.barberName = option.querySelector('p').textContent.trim();
            selectedBarberInput.value = barberId;
            selectedScheduleInput.value = '';
            selectedSlotTimeInput.value = '';

            scheduleContainer.innerHTML = '<div class="flex items-center gap-2 py-5 text-sm text-muted"><span class="material-symbols-outlined animate-spin text-gold">progress_activity</span>Memuat jadwal tersedia...</div>';

            fetch(`{{ url('customer/booking/barber') }}/${barberId}/schedules`)
                .then(res => res.json())
                .then(schedules => {
                    if (schedules.length === 0) {
                        scheduleContainer.innerHTML = '<div class="rounded-lg border border-dashed border-gray-300 bg-cream px-4 py-6 text-center text-sm text-muted">Barber ini belum punya jadwal tersedia.</div>';
                        return;
                    }

                    let html = '<div class="space-y-3">';
                    schedules.forEach(s => {
                        const hasAvailable = s.slots.some(sl => sl.available);
                        html += `
                            <div class="schedule-day overflow-hidden rounded-xl border border-gray-200 bg-white ${hasAvailable ? 'cursor-pointer hover:border-gold transition-colors' : 'opacity-60'}"
                                 data-schedule-id="${s.id}" data-date-label="${s.date_label}">
                                <div class="day-toggle flex items-center justify-between px-4 py-4">
                                    <div><p class="text-sm font-bold text-charcoal">${s.date_label}</p><p class="mt-1 text-[10px] font-medium text-muted">Pilih waktu yang tersedia</p></div>
                                    <span class="rounded-full ${hasAvailable ? 'bg-brandsuccess/10 text-brandsuccess' : 'bg-gray-100 text-muted'} px-2.5 py-1 text-[10px] font-bold">${hasAvailable ? 'Pilih jam' : 'Slot penuh'}</span>
                                </div>
                                <div class="slot-list hidden grid grid-cols-3 gap-2 border-t border-gray-100 bg-cream/60 px-4 pb-4 pt-3 sm:grid-cols-4 md:grid-cols-5"></div>
                            </div>`;
                    });
                    html += '</div>';
                    scheduleContainer.innerHTML = html;

                    // Isi tombol slot untuk setiap hari
                    document.querySelectorAll('.schedule-day').forEach(day => {
                        const schedule = schedules.find(s => String(s.id) === day.dataset.scheduleId);
                        const slotList = day.querySelector('.slot-list');
                        schedule.slots.forEach(sl => {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.dataset.slotTime = sl.time;
                            btn.dataset.slotLabel = sl.label;
                            btn.textContent = sl.label;
                            btn.className = 'slot-option rounded-lg border px-2 py-2.5 text-xs font-semibold transition-colors ' +
                                (sl.available
                                    ? 'border-gray-200 bg-white text-charcoal hover:border-gold hover:bg-gold/5'
                                    : 'cursor-not-allowed border-gray-100 bg-gray-100 text-gray-300 line-through');
                            if (!sl.available) btn.disabled = true;
                            slotList.appendChild(btn);
                        });
                    });

                    // Klik hari untuk membuka/menutup daftar slot
                    document.querySelectorAll('.schedule-day').forEach(day => {
                        day.addEventListener('click', () => {
                            const slotList = day.querySelector('.slot-list');
                            const toggleText = day.querySelector('.day-toggle span');
                            const isOpen = !slotList.classList.contains('hidden');
                            // tutup yang lain
                            document.querySelectorAll('.schedule-day').forEach(d => {
                                if (d !== day) d.querySelector('.slot-list').classList.add('hidden');
                            });
                            slotList.classList.toggle('hidden');
                            if (toggleText) {
                                toggleText.textContent = isOpen
                                    ? (day.querySelectorAll('.slot-option:not(:disabled)').length ? 'Pilih jam &darr;' : 'Slot penuh')
                                    : 'Tutup &uarr;';
                            }
                        });
                    });

                    // Klik slot untuk memilih
                    document.querySelectorAll('.slot-option').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            if (btn.disabled) return;
                            e.stopPropagation();

                            // hapus pilihan slot lain
                            document.querySelectorAll('.slot-option').forEach(b => {
                                b.classList.remove('border-gold', 'bg-gold', 'text-charcoal', 'ring-1', 'ring-gold');
                                b.classList.add('border-gray-200', 'bg-white');
                            });
                            btn.classList.remove('border-gray-200', 'bg-white');
                            btn.classList.add('border-gold', 'bg-gold', 'text-charcoal', 'ring-1', 'ring-gold');

                            const day = btn.closest('.schedule-day');
                            selectedScheduleInput.value = day.dataset.scheduleId;
                            state.selectedScheduleLabel = day.dataset.dateLabel;
                            state.selectedSlot = btn.dataset.slotTime;
                            state.selectedSlotLabel = btn.dataset.slotLabel;
                            selectedSlotTimeInput.value = btn.dataset.slotTime;
                            checkReadyToSubmit();
                            updateProgress(4);
                            confirmationStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        });
                    });
                })
                .catch(() => {
                    scheduleContainer.innerHTML = '<p class="text-red-500 text-sm">Gagal memuat jadwal. Coba lagi.</p>';
                });

            checkReadyToSubmit();
        });
    });

    serviceOptions.forEach(option => {
        option.addEventListener('click', () => {
            serviceOptions.forEach(o => {
                o.classList.remove('border-gold', 'bg-gold/5', 'ring-1', 'ring-gold');
                o.querySelector('.selection-check')?.classList.add('hidden');
                o.querySelector('.selection-check')?.classList.remove('flex');
            });
            option.classList.add('border-gold', 'bg-gold/5', 'ring-1', 'ring-gold');
            option.querySelector('.selection-check')?.classList.remove('hidden');
            option.querySelector('.selection-check')?.classList.add('flex');
            state.serviceName = option.querySelector('p').textContent.trim();
            selectedServiceInput.value = option.dataset.serviceId;
            checkReadyToSubmit();
        });
    });
</script>
@endpush
