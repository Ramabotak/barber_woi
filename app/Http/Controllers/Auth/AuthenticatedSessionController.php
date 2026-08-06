<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @throws ValidationException
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Validasi role (tambahan dari form login)
        $request->validate([
            'role' => ['required', 'in:customer,barber,admin'],
        ]);

        // Autentikasi kredensial (email & password) lewat LoginRequest
        $request->authenticate();

        // Regenerasi session untuk keamanan
        $request->session()->regenerate();

        // Ambil user yang sudah login
        $user = Auth::user();

        // Cek kesesuaian role yang dipilih dengan role user sebenarnya
        if ($user->role !== $request->role) {
            Auth::logout(); // logout dulu
            throw ValidationException::withMessages([
                'role' => 'Peran yang dipilih tidak sesuai dengan akun Anda.',
            ]);
        }

        // Redirect sesuai role
        return $this->redirectByRole($user);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Redirect pengguna berdasarkan role.
     */
    protected function redirectByRole($user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        } elseif ($user->isBarber()) {
            return redirect()->intended(route('barber.dashboard'));
        } else {
            return redirect()->intended(route('customer.dashboard'));
        }
    }
}