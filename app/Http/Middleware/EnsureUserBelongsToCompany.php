<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserBelongsToCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Se não há usuário logado, redirecionar para login
        if (!$user) {
            return redirect()->route('login');
        }

        // Se o usuário não tem empresa associada e não é admin, redirecionar para dashboard
        if (!$user->company_id && $user->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Usuário não possui empresa associada.');
        }

        // Verificar se o usuário está tentando acessar dados de outra empresa
        $requestedCompanyId = $request->route('company') ?? $request->input('company_id');

        if ($requestedCompanyId && $requestedCompanyId != $user->company_id) {
            // Se for admin, permitir acesso
            if ($user->role === 'admin') {
                return $next($request);
            }

            // Se não for admin, negar acesso
            abort(403, 'Acesso negado. Você só pode acessar dados da sua própria empresa.');
        }

        return $next($request);
    }
}
