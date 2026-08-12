<?php

use App\Http\Controllers\Admin\BarberController as AdminBarberController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Barber\BookingController as BarberBookingController;
use App\Http\Controllers\Barber\DashboardController as BarberDashboardController;
use App\Http\Controllers\Barber\ScheduleController as BarberScheduleController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Splash screen
Route::get('/', function () {
    return redirect()->route('splash');
});

Route::get('/splash', function () {
    // FIX: sebelumnya Auth::check() dipakai tanpa import facade Auth (fatal error).
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isBarber()) {
            return redirect()->route('barber.dashboard');
        }

        return redirect()->route('customer.dashboard');
    }

    return view('splash');
})->name('splash');

// Google OAuth
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

Route::middleware('auth')->group(function () {
    // Dipakai oleh layout Breeze bawaan (navigation.blade.php) yang manggil
    // route('dashboard') secara generik. Kita redirect ke dashboard sesuai role.
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isBarber()) {
            return redirect()->route('barber.dashboard');
        }

        return redirect()->route('customer.dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Notifications (shared: customer, barber, admin)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/latest', [NotificationController::class, 'latest'])->name('latest');
    Route::patch('{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
    Route::patch('read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
});

/*
|--------------------------------------------------------------------------
| Customer
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    Route::get('/booking/create', [CustomerBookingController::class, 'create'])->name('booking.create');
    Route::get('/booking/barber/{barber}/schedules', [CustomerBookingController::class, 'availableSchedules'])->name('booking.schedules');
    Route::post('/booking', [CustomerBookingController::class, 'store'])->name('booking.store');
    Route::get('/booking', [CustomerBookingController::class, 'index'])->name('booking.index');
    Route::get('/booking/{booking}', [CustomerBookingController::class, 'show'])->name('booking.show');

    Route::post('/booking/{booking}/review', [ReviewController::class, 'store'])->name('booking.review');
    Route::put('/booking/{booking}/review', [ReviewController::class, 'update'])->name('booking.review.update');
    Route::delete('/booking/{booking}/review', [ReviewController::class, 'destroy'])->name('booking.review.destroy');

    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::get('/booking/barber/{barber}/reviews', [ReviewController::class, 'barberReviews'])->name('booking.barber.reviews');

    Route::get('/payment/{booking}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{booking}/check', [PaymentController::class, 'checkStatus'])->name('payment.check');
});

/*
|--------------------------------------------------------------------------
| Barber
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:barber'])->prefix('barber')->name('barber.')->group(function () {
    Route::get('/dashboard', [BarberDashboardController::class, 'index'])->name('dashboard');

    Route::get('/booking/incoming', [BarberBookingController::class, 'incoming'])->name('booking.incoming');
    Route::patch('/booking/{booking}/accept', [BarberBookingController::class, 'accept'])->name('booking.accept');
    Route::patch('/booking/{booking}/reject', [BarberBookingController::class, 'reject'])->name('booking.reject');

    Route::get('/queue', [BarberBookingController::class, 'activeQueue'])->name('queue');
    Route::patch('/booking/{booking}/start', [BarberBookingController::class, 'startService'])->name('booking.start');
    Route::patch('/booking/{booking}/finish', [BarberBookingController::class, 'finishService'])->name('booking.finish');
    Route::patch('/booking/{booking}/late', [BarberBookingController::class, 'markLate'])->name('booking.late');

    Route::get('/schedule', [BarberScheduleController::class, 'index'])->name('schedule.index');
    Route::post('/schedule', [BarberScheduleController::class, 'store'])->name('schedule.store');
    Route::delete('/schedule/{schedule}', [BarberScheduleController::class, 'destroy'])->name('schedule.destroy');
    Route::patch('/schedule/{schedule}/close', [BarberScheduleController::class, 'closeShift'])->name('schedule.close');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // CRUD Barber
    Route::get('/barbers', [AdminBarberController::class, 'index'])->name('barbers.index');
    Route::get('/barbers/create', [AdminBarberController::class, 'create'])->name('barbers.create');
    Route::post('/barbers', [AdminBarberController::class, 'store'])->name('barbers.store');
    Route::get('/barbers/{barber}/edit', [AdminBarberController::class, 'edit'])->name('barbers.edit');
    Route::put('/barbers/{barber}', [AdminBarberController::class, 'update'])->name('barbers.update');
    Route::delete('/barbers/{barber}', [AdminBarberController::class, 'destroy'])->name('barbers.destroy');

    // CRUD Service
    // FIX: sebelumnya ServiceController dipakai tanpa import (fatal error / class not found).
    Route::get('/services', [AdminServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [AdminServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [AdminServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}/edit', [AdminServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [AdminServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [AdminServiceController::class, 'destroy'])->name('services.destroy');

    // Kelola Booking + refund
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::patch('/bookings/{booking}/refund', [AdminBookingController::class, 'refund'])->name('bookings.refund');
    Route::patch('/bookings/{booking}/force-complete', [AdminBookingController::class, 'forceComplete'])->name('bookings.force-complete');

    // Laporan
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Ulasan Customer
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    // Pengaturan
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
});

/*
|--------------------------------------------------------------------------
| Midtrans Webhook (dikecualikan dari CSRF di bootstrap/app.php)
|--------------------------------------------------------------------------
*/
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

require __DIR__.'/auth.php';
