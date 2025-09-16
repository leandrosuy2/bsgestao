<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait BelongsToCompany
{
    /**
     * Scope para filtrar por empresa do usuário logado
     */
    public function scopeForCurrentCompany($query)
    {
        $user = Auth::user();

        if ($user && $user->company_id) {
            return $query->where('company_id', $user->company_id);
        }

        return $query;
    }

    /**
     * Verificar se o registro pertence à empresa do usuário
     */
    public function belongsToCurrentCompany()
    {
        $user = Auth::user();

        if (!$user || !$user->company_id) {
            return false;
        }

        return $this->company_id === $user->company_id;
    }

    /**
     * Boot do trait
     */
    protected static function bootBelongsToCompany()
    {
        static::creating(function ($model) {
            if (!$model->company_id && Auth::user()) {
                $model->company_id = Auth::user()->company_id;
            }
        });
    }
}
