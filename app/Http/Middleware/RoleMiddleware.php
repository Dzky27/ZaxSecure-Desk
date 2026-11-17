<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request and enforce a server-side role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role  The required role (e.g. "admin" or "user").
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // The 'auth' middleware guarantees an authenticated user. Guard here for
        // defense in depth (e.g. if routes are ever registered without auth).
        if (! $request->user()) {
            abort(403, 'Halaman ini hanya dapat diakses oleh pengguna yang sudah login.');
        }

        if ($role === 'admin' && ! $request->user()->isAdmin()) {
            abort(403, 'Halaman ini hanya dapat diakses oleh administrator.');
        }

        if ($role === 'user' && ! $request->user()->isUser()) {
            abort(403, 'Halaman ini hanya dapat diakses oleh pengguna biasa.');
        }

        return $next($request);
    }
}
