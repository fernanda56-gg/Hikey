<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        foreach ($guards as $guard) {
            if (auth()->guard($guard)->check()) {
                // Si el usuario ya está autenticado, lo dirige a pagina principal (/home)
                return redirect('/home');
            }
        }

        return $next($request);
    }
}
