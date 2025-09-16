<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Admin tem todas as permissões
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Verificar se o usuário tem a permissão específica
        if (!$user->hasPermission($permission)) {
            abort(403, 'Acesso negado. Você não tem permissão para acessar este recurso.');
        }

        return $next($request);
    }
}
