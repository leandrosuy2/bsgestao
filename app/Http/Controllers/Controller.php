<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if ($user && !$user->isAdmin()) {
                $company = $user->company;
                $now = now();
                if ($company) {
                    // Se não está ativa ou trial acabou e não pagou
                    if (!$company->is_active || ($company->trial_end && $now->greaterThan($company->trial_end) && (!$company->paid_until || $now->greaterThan($company->paid_until)))) {
                        return redirect()->route('payment.notice');
                    }
                }
            }
            return $next($request);
        });
    }
}
