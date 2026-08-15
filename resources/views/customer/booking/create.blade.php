@extends('layouts.customer')

@section('title', 'Booking - Barber Woi')

@section('content')
    <h1 class="text-2xl font-bold text-brand-navy mb-6">Buat Booking</h1>

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
        <div class="bg-white rounded-xl shadow p-6 mb-4">
            <h2 class="font-semibold text-brand-navy mb-4">1. Pilih Barber</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @forelse($barbers as $barber)
                    <label class="barber-option cursor-pointer border-2 border-gray-200 rounded-lg p-3 text-center hover:border-brand-gold transition-colors"
                           data-barber-id="{{ $barber->id }}">
                        <input type="radio" name="barber_radio" value="{{ $barber->id }}" class="hidden">
                        <div class="w-14 h-14 mx-auto rounded-full bg-brand-navy text-white flex items-center justify-center overflow-hidden mb-2">
                            @if($barber->photo)
                                <img src="{{ Storage::url($barber->photo) }}" class="w-full h-full object-cover" alt="{{ $barber->user->name }}">
                            @else
                                <span class="font-bold">{{ strtoupper(substr($barber->user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <p class="text-sm font-medium">{{ $barber->user->name }}</p>
                        @if($barber->reviews_count > 0)
                            <p class="text-xs text-amber-500 mt-1">★ {{ number_format($barber->reviews_avg_rating, 1) }} <span class="text-gray-400">({{ $barber->reviews_count }})</span></p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Belum ada ulasan</p>
                        @endif
                        @if($barber->schedules->isNotEmpty())
                            <p class="text-[10px] text-green-600 mt-1 leading-tight">
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
        <div class="bg-white rounded-xl shadow p-6 mb-4">
            <h2 class="font-semibold text-brand-navy mb-4">2. Pilih Layanan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @forelse($services as $service)
                    <label class="service-option cursor-pointer border-2 border-gray-200 rounded-lg p-3 flex justify-between items-center hover:border-brand-gold transition-colors"
                           data-service-id="{{ $service->id }}">
                        <input type="radio" name="service_radio" value="{{ $service->id }}" class="hidden">
                        <div>
                            <p class="font-medium text-sm">{{ $service->service_name }}</p>
                            <p class="text-xs text-gray-400">{{ $service->duration }} menit</p>
                        </div>
                        <p class="text-brand-gold font-bold text-sm">Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                    </label>
                @empty
                    <p class="col-span-full text-gray-400 text-sm">Tidak ada layanan tersedia.</p>
                @endforelse
            </div>
        </div>

        {{-- Step 3: Pilih Hari & Jam --}}
        <div class="bg-white rounded-xl shadow p-6 mb-4">
            <h2 class="font-semibold text-brand-navy mb-4">3. Pilih Hari & Jam</h2>
            <p class="text-xs text-gray-400 mb-3">Pilih hari, lalu pilih jam (tiap 30 menit) yang tersedia.</p>
            <div id="schedule-container">
                <p class="text-gray-400 text-sm">Pilih barber terlebih dahulu untuk melihat jadwal tersedia.</p>
            </div>
        </div>

        {{-- Step 4: Konfirmasi --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-brand-navy mb-4">4. Konfirmasi</h2>
            <p class="text-sm text-gray-500 mb-4">Pastikan barber, layanan, dan jam yang dipilih sudah benar sebelum konfirmasi.</p>
            <div id="summary-box" class="hidden bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4 text-sm"></div>
            <button type="submit" id="submit-btn" disabled
                    class="bg-brand-gold text-brand-navy px-6 py-3 rounded-lg font-semibold disabled:opacity-40 disabled:cursor-not-allowed hover:bg-amber-400">
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
        submitBtn.disabled = !(selectedBarberInput.value && selectedServiceInput.value && selectedScheduleInput.value && selectedSlotTimeInput.value);
        renderSummary();
    }

    function renderSummary() {
        if (selectedBarberInput.value && selectedServiceInput.value && selectedScheduleInput.value && selectedSlotTimeInput.value) {
            summaryBox.classList.remove('hidden');
            summaryBox.innerHTML = `
                <p><span class="text-gray-500">Barber:</span> <span class="font-semibold">${state.barberName}</span></p>
                <p><span class="text-gray-500">Layanan:</span> <span class="font-semibold">${state.serviceName}</span></p>
                <p><span class="text-gray-500">Jadwal:</span> <span class="font-semibold">${state.selectedScheduleLabel}, ${state.selectedSlotLabel}</span></p>
            `;
        } else {
            summaryBox.classList.add('hidden');
        }
    }

    barberOptions.forEach(option => {
        option.addEventListener('click', () => {
            barberOptions.forEach(o => o.classList.remove('border-brand-gold', 'bg-amber-50'));
            option.classList.add('border-brand-gold', 'bg-amber-50');

            const barberId = option.dataset.barberId;
            state.barberName = option.querySelector('.text-sm.font-medium').textContent.trim();
            selectedBarberInput.value = barberId;
            selectedScheduleInput.value = '';
            selectedSlotTimeInput.value = '';

            scheduleContainer.innerHTML = '<p class="text-gray-400 text-sm">Memuat jadwal...</p>';

            fetch(`{{ url('customer/booking/barber') }}/${barberId}/schedules`)
                .then(res => res.json())
                .then(schedules => {
                    if (schedules.length === 0) {
                        scheduleContainer.innerHTML = '<p class="text-gray-400 text-sm">Barber ini belum punya jadwal tersedia.</p>';
                        return;
                    }

                    let html = '<div class="space-y-2">';
                    schedules.forEach(s => {
                        const hasAvailable = s.slots.some(sl => sl.available);
                        html += `
                            <div class="schedule-day border-2 border-gray-200 rounded-lg overflow-hidden ${hasAvailable ? 'cursor-pointer hover:border-brand-gold transition-colors' : 'opacity-60'}"
                                 data-schedule-id="${s.id}" data-date-label="${s.date_label}">
                                <div class="day-toggle px-4 py-3 flex items-center justify-between">
                                    <p class="font-medium text-sm">${s.date_label}</p>
                                    <span class="text-xs text-gray-400">${hasAvailable ? 'Pilih jam &darr;' : 'Slot penuh'}</span>
                                </div>
                                <div class="slot-list hidden px-4 pb-4 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2"></div>
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
                            btn.className = 'slot-option text-xs px-2 py-2 rounded-lg border transition-colors ' +
                                (sl.available
                                    ? 'border-gray-200 text-gray-700 hover:border-brand-gold hover:bg-amber-50'
                                    : 'border-gray-100 text-gray-300 cursor-not-allowed bg-gray-50');
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
                                b.classList.remove('border-brand-gold', 'bg-amber-50', 'font-semibold');
                            });
                            btn.classList.add('border-brand-gold', 'bg-amber-50', 'font-semibold');

                            const day = btn.closest('.schedule-day');
                            selectedScheduleInput.value = day.dataset.scheduleId;
                            state.selectedScheduleLabel = day.dataset.dateLabel;
                            state.selectedSlot = btn.dataset.slotTime;
                            state.selectedSlotLabel = btn.dataset.slotLabel;
                            selectedSlotTimeInput.value = btn.dataset.slotTime;
                            checkReadyToSubmit();
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
            serviceOptions.forEach(o => o.classList.remove('border-brand-gold', 'bg-amber-50'));
            option.classList.add('border-brand-gold', 'bg-amber-50');
            state.serviceName = option.querySelector('.font-medium').textContent.trim();
            selectedServiceInput.value = option.dataset.serviceId;
            checkReadyToSubmit();
        });
    });
</script>
@endpush
