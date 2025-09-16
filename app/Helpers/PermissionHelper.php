<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Verifica se o usuário tem uma permissão específica
     */
    public static function hasPermission($permission)
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admin tem todas as permissões
        if ($user->isAdmin()) {
            return true;
        }

        return $user->hasPermission($permission);
    }

    /**
     * Verifica se o usuário tem qualquer uma das permissões fornecidas
     */
    public static function hasAnyPermission($permissions)
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admin tem todas as permissões
        if ($user->isAdmin()) {
            return true;
        }

        return $user->hasAnyPermission($permissions);
    }

    /**
     * Verifica se o usuário tem todas as permissões fornecidas
     */
    public static function hasAllPermissions($permissions)
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admin tem todas as permissões
        if ($user->isAdmin()) {
            return true;
        }

        return $user->hasAllPermissions($permissions);
    }

    /**
     * Verifica se o usuário tem permissão para um módulo específico
     */
    public static function hasModulePermission($module)
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admin tem todas as permissões
        if ($user->isAdmin()) {
            return true;
        }

        $permissions = $user->getAllPermissions();
        return $permissions->where('module', $module)->count() > 0;
    }

    /**
     * Retorna as permissões do usuário agrupadas por módulo
     */
    public static function getUserPermissionsByModule()
    {
        $user = Auth::user();

        if (!$user) {
            return collect();
        }

        return $user->getAllPermissions()->groupBy('module');
    }

    /**
     * Verifica se deve mostrar um elemento baseado em permissão
     */
    public static function canShow($permission)
    {
        return self::hasPermission($permission);
    }

    /**
     * Verifica se deve mostrar um elemento baseado em múltiplas permissões (OR)
     */
    public static function canShowAny($permissions)
    {
        return self::hasAnyPermission($permissions);
    }

    /**
     * Verifica se deve mostrar um elemento baseado em múltiplas permissões (AND)
     */
    public static function canShowAll($permissions)
    {
        return self::hasAllPermissions($permissions);
    }
}
