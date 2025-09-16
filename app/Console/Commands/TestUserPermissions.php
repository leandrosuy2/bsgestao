<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestUserPermissions extends Command
{
    protected $signature = 'test:user-permissions {user_id}';
    protected $description = 'Test user permissions';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return 1;
        }

        $this->info("Testing permissions for user: {$user->name}");

        $permissions = [
            'view-products',
            'view-categories',
            'view-movements',
            'view-payables',
            'view-receivables',
            'financial-reports',
            'view-employees',
            'view-reports',
            'manage-roles',
            'view-users',
            'time-clock',
            'payroll',
            'vacations',
            'leaves',
            'benefits'
        ];

        foreach ($permissions as $permission) {
            $hasPermission = $user->hasPermission($permission);
            $status = $hasPermission ? 'YES' : 'NO';
            $this->line("  {$permission}: {$status}");
        }

        return 0;
    }
}