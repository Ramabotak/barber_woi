<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect ke Google untuk otentikasi.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Callback dari Google setelah otentikasi.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            // Jika gagal, arahkan kembali ke login dengan pesan error
            return redirect()->route('login')
                             ->withErrors(['email' => 'Gagal login dengan Google. Silakan coba lagi.']);
        }

        // Cari user berdasarkan email
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // Jika sudah ada, login
            Auth::login($user);
        } else {
            // Buat user baru (sebagai customer)
            $user = User::create([
                'name'              => $googleUser->getName(),
                'email'             => $googleUser->getEmail(),
                'password'          => bcrypt(uniqid()), // password random, tidak digunakan
                'role'              => 'customer',
                'phone_number'             => null,
                'email_verified_at' => now(),
            ]);

            Auth::login($user);
        }

        // Redirect ke dashboard sesuai role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isBarber()) {
            return redirect()->route('barber.dashboard');
        } else {
            return redirect()->route('customer.dashboard');
        }
    }
}