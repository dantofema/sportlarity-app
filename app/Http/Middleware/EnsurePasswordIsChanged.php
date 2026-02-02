<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Si el usuario está autenticado y requiere cambiar contraseña
        if ($user && $user->password_change_required) {
            // Permitir acceso solo a la página de cambio de contraseña y logout
            if (! $request->routeIs('filament.admin.auth.change-password') &&
                ! $request->routeIs('filament.admin.auth.logout')) {
                return redirect()->route('filament.admin.auth.change-password');
            }
        }

        return $next($request);
    }
}
