<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;

class CheckRolePermissions extends Command
{
    protected $signature = 'check:role-permissions {company_id} {role_name}';
    protected $description = 'Check permissions of a specific role';

    public function handle()
    {
        $companyId = $this->argument('company_id');
        $roleName = $this->argument('role_name');

        $role = Role::where('company_id', $companyId)
                   ->where('name', $roleName)
                   ->with('permissions')
                   ->first();

        if (!$role) {
            $this->error("Role '{$roleName}' not found for company {$companyId}!");
            return 1;
        }

        $this->info("Role: {$role->name} (Company: {$role->company_id})");
        $this->info("Description: {$role->description}");
        $this->info("Active: " . ($role->is_active ? 'YES' : 'NO'));
        $this->info("Total permissions: {$role->permissions->count()}");

        $this->info("\nPermissions by module:");
        foreach ($role->permissions->groupBy('module') as $module => $permissions) {
            $this->info("  {$module}:");
            foreach ($permissions as $permission) {
                $this->line("    - {$permission->slug}");
            }
        }

        return 0;
    }
}
