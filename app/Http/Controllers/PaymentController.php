<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\MidtransService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        protected MidtransService $midtransService,
        protected NotificationService $notificationService
    ) {
    }

    public function show(Request $request, Booking $booking): View|RedirectResponse
    {
        abort_unless($booking->customer_id === $request->user()->id, 403);

        if ($booking->status !== 'accepted') {
            return redirect()->route('customer.booking.show', $booking)
                ->with('error', 'Pembayaran tersedia setelah booking diterima oleh barber.');
        }

        if ($booking->payment && $booking->payment->status === 'paid') {
            return redirect()->route('customer.booking.show', $booking)->with('success', 'Booking ini sudah dibayar.');
        }

        return view('customer.payment.show', [
            'booking' => $booking,
            'payment' => $booking->payment,
            'clientKey' => config('midtrans.client_key'),
            'isProduction' => config('midtrans.is_production'),
            'paymentDriver' => $this->midtransService->driver(),
            'availableMethods' => $this->midtransService->availableMethods(),
        ]);
    }

    public function chooseMethod(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->customer_id === $request->user()->id, 403);

        abort_unless($booking->status === 'accepted', 422, 'Booking harus diterima barber sebelum pembayaran dibuat.');

        $validated = $request->validate([
            'method' => ['required', 'in:' . implode(',', $this->midtransService->availableMethods())],
            // This is generated in the browser by Midtrans.js. Never accept raw card fields here.
            'token_id' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            if ($this->midtransService->driver() === 'core') {
                $this->midtransService->createCoreTransaction($booking, $validated['method'], [
                    'token_id' => $validated['token_id'] ?? null,
                ]);
            } else {
                $this->midtransService->createSnapTransaction($booking, $validated['method']);
            }
        } catch (\Throwable $e) {
            Log::error('Midtrans transaction error: ' . $e->getMessage());
            return back()->with('error', $this->midtransService->errorMessage($e));
        }

        return redirect()->route('customer.payment.show', $booking);
    }

    // Webhook Midtrans, tidak pakai auth/CSRF (dikecualikan di bootstrap/app.php).
    public function callback(Request $request): JsonResponse
    {
        try {
            $result = $this->midtransService->normalizeNotification($request->all());
        } catch (\Throwable $e) {
            Log::error('Midtrans callback error: ' . $e->getMessage());
            return response()->json(['message' => 'invalid notification'], 400);
        }

        $applied = $this->applyPaymentResult($result);

        if (!$applied) {
            return response()->json(['message' => 'payment not found'], 404);
        }

        return response()->json(['message' => 'ok']);
    }

    /**
     * Cek status transaksi langsung ke API Midtrans (bukan nunggu webhook).
     * Dipanggil otomatis begitu customer kembali dari popup Snap, dan bisa juga
     * dipencet manual. Berguna terutama saat testing di localhost karena
     * webhook Midtrans tidak bisa menjangkau alamat non-publik.
     */
    public function checkStatus(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->customer_id === $request->user()->id, 403);

        abort_unless(in_array($booking->status, ['accepted', 'paid'], true), 422, 'Status pembayaran belum tersedia untuk booking ini.');

        if (!$booking->payment || !$booking->payment->transaction_id) {
            return redirect()->route('customer.booking.show', $booking)
                ->with('error', 'Belum ada transaksi pembayaran untuk booking ini.');
        }

        try {
            $result = $this->midtransService->getStatus($booking->payment->transaction_id);
            $this->applyPaymentResult($result);
        } catch (\Throwable $e) {
            Log::error('Midtrans check status error: ' . $e->getMessage());
            return redirect()->route('customer.booking.show', $booking)
                ->with('error', 'Gagal mengecek status pembayaran ke Midtrans. Coba lagi beberapa saat.');
        }

        return redirect()->route('customer.booking.show', $booking)
            ->with('success', 'Status pembayaran berhasil disinkronkan.');
    }

    /**
     * Sandbox helper: Midtrans documents browser simulators for VA, rather than
     * a merchant-callable settlement API. Never expose this route in production.
     */
    public function openSandboxVaSimulator(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->customer_id === $request->user()->id, 403);
        abort_if(app()->environment('production') || config('midtrans.is_production'), 404);

        $payment = $booking->payment;
        abort_unless($payment && $payment->status === 'pending', 422, 'Tidak ada pembayaran VA yang dapat disimulasikan.');

        $paymentType = data_get($payment->payment_data, 'payment_type');
        abort_unless(in_array($paymentType, ['bank_transfer', 'echannel'], true), 422, 'Simulator hanya tersedia untuk Virtual Account dan Mandiri Bill.');

        $bank = data_get($payment->payment_data, 'instruction.bank')
            ?? data_get($payment->payment_data, 'va_numbers.0.bank')
            ?? ($paymentType === 'echannel' ? 'mandiri' : null);
        $urls = [
            'bca' => 'https://simulator.sandbox.midtrans.com/bca/va/index',
            'bni' => 'https://simulator.sandbox.midtrans.com/bni/va/index',
            'bri' => 'https://simulator.sandbox.midtrans.com/openapi/va/index?bank=bri',
            'permata' => 'https://simulator.sandbox.midtrans.com/openapi/va/index?bank=permata',
            'mandiri' => 'https://simulator.sandbox.midtrans.com/openapi/va/index?bank=mandiri',
        ];

        abort_unless(isset($urls[$bank]), 422, 'Simulator untuk bank ini belum tersedia.');
        return redirect()->away($urls[$bank]);
    }

    protected function applyPaymentResult(array $result): bool
    {
        $payment = Payment::where('transaction_id', $result['order_id'])->first();
        if (!$payment) {
            return false;
        }

        $payment = $this->midtransService->applyGatewayResponse($payment, $result);

        $booking = $payment->booking;
        if ($payment->status === 'paid' && $booking->status === 'accepted') {
            // Booking baru dapat dibayar setelah disetujui barber. Saat lunas,
            // booking langsung siap masuk antrean layanan.
            $booking->update(['status' => 'paid']);
            $this->notificationService->notifyBookingPaid($booking);
        }

        return true;
    }
}
