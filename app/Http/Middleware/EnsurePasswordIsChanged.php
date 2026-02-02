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
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Si el usuario está autenticado y requiere cambiar contraseña
        if ($user && $user->password_change_required) {
            // Rutas permitidas cuando se requiere cambio de contraseña
            $allowedRoutes = [
                'filament.admin.pages.change-password',
                'filament.admin.auth.logout',
                'livewire.update', // Para las actualizaciones de Livewire
                'livewire.message', // Para los mensajes de Livewire
            ];

            // Permitir requests de Livewire y las rutas específicas
            if (! $request->routeIs($allowedRoutes) &&
                ! str_starts_with($request->path(), 'livewire/')) {
                return redirect()->route('filament.admin.pages.change-password');
            }
        }

        return $next($request);
    }
}
