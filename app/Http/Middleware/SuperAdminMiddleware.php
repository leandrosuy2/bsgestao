<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar se o usuário está logado
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Verificar se é o super administrador (ID = 1)
        if (Auth::id() !== 1) {
            abort(403, 'Acesso negado. Esta área é restrita ao Super Administrador.');
        }

        return $next($request);
    }
}
