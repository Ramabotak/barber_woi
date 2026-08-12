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

        {{-- Step 3: Pilih Jadwal --}}
        <div class="bg-white rounded-xl shadow p-6 mb-4">
            <h2 class="font-semibold text-brand-navy mb-4">3. Pilih Jadwal</h2>
            <div id="schedule-container">
                <p class="text-gray-400 text-sm">Pilih barber terlebih dahulu untuk melihat jadwal tersedia.</p>
            </div>
        </div>

        {{-- Step 4: Konfirmasi --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-semibold text-brand-navy mb-4">4. Konfirmasi</h2>
            <p class="text-sm text-gray-500 mb-4">Pastikan barber, layanan, dan jadwal yang dipilih sudah benar sebelum konfirmasi.</p>
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

    const selectedBarberInput = document.getElementById('selected_barber_id');
    const selectedServiceInput = document.getElementById('selected_service_id');
    const selectedScheduleInput = document.getElementById('selected_schedule_id');

    function checkReadyToSubmit() {
        submitBtn.disabled = !(selectedBarberInput.value && selectedServiceInput.value && selectedScheduleInput.value);
    }

    barberOptions.forEach(option => {
        option.addEventListener('click', () => {
            barberOptions.forEach(o => o.classList.remove('border-brand-gold', 'bg-amber-50'));
            option.classList.add('border-brand-gold', 'bg-amber-50');

            const barberId = option.dataset.barberId;
            selectedBarberInput.value = barberId;
            selectedScheduleInput.value = '';

            scheduleContainer.innerHTML = '<p class="text-gray-400 text-sm">Memuat jadwal...</p>';

            fetch(`{{ url('customer/booking/barber') }}/${barberId}/schedules`)
                .then(res => res.json())
                .then(schedules => {
                    if (schedules.length === 0) {
                        scheduleContainer.innerHTML = '<p class="text-gray-400 text-sm">Barber ini belum punya jadwal tersedia.</p>';
                        return;
                    }

                    let html = '<div class="grid grid-cols-2 md:grid-cols-4 gap-3">';
                    schedules.forEach(s => {
                        const date = new Date(s.date);
                        const dateLabel = date.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short' });
                        html += `
                            <label class="schedule-option cursor-pointer border-2 border-gray-200 rounded-lg p-3 text-center hover:border-brand-gold transition-colors" data-schedule-id="${s.id}">
                                <input type="radio" name="schedule_radio" value="${s.id}" class="hidden">
                                <p class="text-sm font-medium">${dateLabel}</p>
                                <p class="text-xs text-gray-400">${s.start_time.substring(0,5)} - ${s.end_time.substring(0,5)}</p>
                            </label>`;
                    });
                    html += '</div>';
                    scheduleContainer.innerHTML = html;

                    document.querySelectorAll('.schedule-option').forEach(option => {
                        option.addEventListener('click', () => {
                            document.querySelectorAll('.schedule-option').forEach(o => o.classList.remove('border-brand-gold', 'bg-amber-50'));
                            option.classList.add('border-brand-gold', 'bg-amber-50');
                            selectedScheduleInput.value = option.dataset.scheduleId;
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
            selectedServiceInput.value = option.dataset.serviceId;
            checkReadyToSubmit();
        });
    });
</script>
@endpush
