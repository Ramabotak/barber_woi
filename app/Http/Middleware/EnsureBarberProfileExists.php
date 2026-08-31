<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Barber;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBarberProfileExists
{
    /**
     * Ensure barber user memiliki profile Barber.
     * Jika belum ada, buat secara otomatis.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isBarber() && !$user->barber) {
            Barber::firstOrCreate(
                ['user_id' => $user->id],
                ['status' => 'aktif']
            );
        }

        return $next($request);
    }
}
