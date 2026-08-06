<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Admin\BarberController as AdminBarberController;


//  splash screen
Route::get('/', function () {
    return redirect()->route('splash');
});

// Splash screen 
Route::get('/splash', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->isAdmin()) return redirect()->route('admin.dashboard');
        elseif ($user->isBarber()) return redirect()->route('barber.dashboard');
        else return redirect()->route('customer.dashboard');
    }
    return view('splash');
})->name('splash');

// Google OAuth
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);



Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Customer Dashboard
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/customer/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
});

// Barber Dashboard
Route::middleware(['auth', 'role:barber'])->group(function () {
    Route::get('/barber/dashboard', [BarberController::class, 'dashboard'])->name('barber.dashboard');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // CRUD Barber
    Route::get('/barbers', [AdminBarberController::class, 'index'])->name('barbers.index');
    Route::get('/barbers/create', [AdminBarberController::class, 'create'])->name('barbers.create');
    Route::post('/barbers', [AdminBarberController::class, 'store'])->name('barbers.store');
    Route::get('/barbers/{barber}/edit', [AdminBarberController::class, 'edit'])->name('barbers.edit');
    Route::put('/barbers/{barber}', [AdminBarberController::class, 'update'])->name('barbers.update');
    Route::delete('/barbers/{barber}', [AdminBarberController::class, 'destroy'])->name('barbers.destroy');

    // CRUD Service (BARU)
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
});


require __DIR__.'/auth.php';