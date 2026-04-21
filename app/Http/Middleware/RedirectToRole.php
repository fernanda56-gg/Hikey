<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectToRole
{
    /* Función para redirigir al usuario a su documentación dependiendo del rol que tenga */
    protected array $roles = [
        'admin',
        'manager',
        'team-leader',
        'user',
    ];

    public function handle(Request $request, Closure $next)
    {
        /* 1-Comprobar que el usuario haya iniciado sesión */
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        /* 2-Se obtiene el rol del usuario */
        $user_rol = Auth::user()->getRoleNames()->first();

        /* 3-Se comprueba que el rol exista de lo contrario le mandara error 403 */
        if (! in_array($user_rol, $this->roles)) {
            abort(403, 'No tienes los suficientes permisos para acceder al apartado de documentación');
        }

        /* 4-Se comprueba que el usuario tiene un rol por ende lo manda a su docs respectivos */
        if(str_starts_with($request->path(), "docs/{$user_rol}")) {
            return $next($request);
        }

        /* 5-Lo manda a la vista inicial de la documentación */
        return redirect("docs/{$user_rol}/overview");
    }
}
