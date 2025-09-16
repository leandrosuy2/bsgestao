<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\PermissionHelper;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configurar Carbon para português brasileiro
        Carbon::setLocale('pt_BR');
        setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR', 'portuguese');
        
        // Registrar helper de permissões como diretiva Blade
        Blade::if('can', function ($permission) {
            return PermissionHelper::hasPermission($permission);
        });

        Blade::if('canAny', function ($permissions) {
            return PermissionHelper::hasAnyPermission($permissions);
        });

        Blade::if('canAll', function ($permissions) {
            return PermissionHelper::hasAllPermissions($permissions);
        });

        Blade::if('canModule', function ($module) {
            return PermissionHelper::hasModulePermission($module);
        });
    }
}
