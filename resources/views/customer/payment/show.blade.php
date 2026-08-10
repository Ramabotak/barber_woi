@extends('layouts.customer')

@section('title', 'Pembayaran - Barber Woi')

@section('content')
    <div class="max-w-lg mx-auto">
        <h1 class="text-2xl font-bold text-brand-navy mb-6 text-center">Pembayaran</h1>

        <div class="bg-white rounded-xl shadow p-6 mb-4">
            <div class="flex justify-between items-center mb-4 pb-4 border-b">
                <div>
                    <p class="text-xs text-gray-400">Kode Booking</p>
                    <p class="font-bold text-brand-navy">{{ $booking->booking_code }}</p>
                </div>
                <span class="px-3 py-1 text-xs rounded-full bg-amber-100 text-amber-800 font-semibold">
                    Menunggu Pembayaran
                </span>
            </div>

            <dl class="space-y-2 text-sm mb-4">
                <div class="flex justify-between"><dt class="text-gray-500">Barber</dt><dd>{{ $booking->barber->user->name }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Layanan</dt><dd>{{ $booking->service->service_name }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Jadwal</dt><dd>{{ $booking->schedule?->date?->format('d M Y') }}, {{ \Carbon\Carbon::parse($booking->schedule->start_time)->format('H:i') }}</dd></div>
            </dl>

            <div class="flex justify-between items-center pt-4 border-t">
                <p class="font-semibold">Total Bayar</p>
                <p class="text-2xl font-bold text-brand-gold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
            </div>
        </div>

        <button id="pay-button"
                class="w-full bg-brand-gold text-brand-navy px-6 py-3 rounded-lg font-semibold hover:bg-amber-400 transition-colors">
            Bayar Sekarang
        </button>

        <p class="text-xs text-gray-400 text-center mt-3">
            Pembayaran diproses aman lewat Midtrans. Setelah pembayaran berhasil, booking Anda akan otomatis dikonfirmasi.
        </p>

        <div id="pay-error" class="hidden bg-red-50 text-red-700 p-3 rounded mt-4 text-sm"></div>

        <form id="check-status-form" action="{{ route('customer.payment.check', $booking) }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript"
            src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ $clientKey }}"></script>
    <script>
        document.getElementById('pay-button').addEventListener('click', function () {
            const btn = this;
            btn.disabled = true;
            btn.textContent = 'Memuat...';

            snap.pay(@json($payment->snap_token), {
                onSuccess: function () {
                    document.getElementById('check-status-form').submit();
                },
                onPending: function () {
                    document.getElementById('check-status-form').submit();
                },
                onError: function () {
                    const errorBox = document.getElementById('pay-error');
                    errorBox.textContent = 'Pembayaran gagal diproses. Silakan coba lagi.';
                    errorBox.classList.remove('hidden');
                    btn.disabled = false;
                    btn.textContent = 'Bayar Sekarang';
                },
                onClose: function () {
                    btn.disabled = false;
                    btn.textContent = 'Bayar Sekarang';
                }
            });
        });
    </script>
@endpush
