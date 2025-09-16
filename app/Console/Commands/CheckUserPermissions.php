<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUserPermissions extends Command
{
    protected $signature = 'user:check-permissions {user_id}';
    protected $description = 'Check user permissions and modules';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return 1;
        }

        $this->info("User: {$user->name} ({$user->email})");
        $this->info("Company ID: {$user->company_id}");
        $this->info("Role: {$user->role}");

        $permissions = $user->getAllPermissions();
        $this->info("Total permissions: {$permissions->count()}");

        $modules = $permissions->pluck('module')->unique();
        $this->info("Modules: " . $modules->implode(', '));

        $this->info("\nPermissions by module:");
        foreach ($modules as $module) {
            $modulePermissions = $permissions->where('module', $module);
            $this->info("  {$module}: {$modulePermissions->count()} permissions");
            foreach ($modulePermissions as $permission) {
                $this->line("    - {$permission->slug}");
            }
        }

        return 0;
    }
}
