<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Cek apakah user sudah login dan rolenya sesuai
        if (!$request->user() || $request->user()->role !== $role) {
            // Jika tidak sesuai, tampilkan halaman 403 (Forbidden)
            abort(403, 'Akses ditolak. Anda tidak memiliki izin.');
        }

        return $next($request);
    }
}