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

        if ($booking->payment && $booking->payment->status === 'paid') {
            return redirect()->route('customer.booking.show', $booking)->with('success', 'Booking ini sudah dibayar.');
        }

        return view('customer.payment.show', [
            'booking' => $booking,
            'payment' => $booking->payment,
            'clientKey' => config('midtrans.client_key'),
            'isProduction' => config('midtrans.is_production'),
        ]);
    }

    public function chooseMethod(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->customer_id === $request->user()->id, 403);

        $validated = $request->validate([
            'method' => ['required', 'in:qris,bca,bni,bri,permata'],
        ]);

        try {
            // Kanal Core API akun sandbox ini belum aktif (Midtrans HTTP 402).
            // Tetap gunakan Snap yang sudah aktif agar pembayaran customer tidak terblokir.
            $this->midtransService->createSnapTransaction($booking, $validated['method']);
        } catch (\Throwable $e) {
            Log::error('Midtrans Snap transaction error: ' . $e->getMessage());
            return back()->with('error', 'Pembayaran belum dapat disiapkan. Silakan coba kembali dalam beberapa saat.');
        }

        return redirect()->route('customer.payment.show', $booking);
    }

    // Webhook Midtrans, tidak pakai auth/CSRF (dikecualikan di bootstrap/app.php).
    public function callback(Request $request): JsonResponse
    {
        try {
            $result = $this->midtransService->handleCallback($request->all());
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

    protected function applyPaymentResult(array $result): bool
    {
        $payment = Payment::where('transaction_id', $result['order_id'])->first();
        if (!$payment) {
            return false;
        }

        $payment->status = $result['status'];
        $payment->payment_method = $result['payment_type'] ?? $payment->payment_method;
        if ($result['status'] === 'paid' && !$payment->paid_at) {
            $payment->paid_at = now();
        }
        $payment->save();

        $booking = $payment->booking;
        if ($result['status'] === 'paid' && $booking->status === 'pending') {
            // Pembayaran sukses, booking jadi 'paid' (menunggu konfirmasi barber
            // di halaman Booking Masuk). Barber yang menerima (Terima) yang
            // mengubahnya jadi 'accepted' agar masuk antrean.
            $booking->update(['status' => 'paid']);
            $this->notificationService->notifyBookingPaid($booking);
        }

        return true;
    }
}
