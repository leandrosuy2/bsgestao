<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permission;

class CheckPayslipsPermission extends Command
{
    protected $signature = 'check:payslips-permission';
    protected $description = 'Check if payslips permission exists';

    public function handle()
    {
        $permissions = Permission::where('module', 'hr')->get();

        $this->info("HR permissions:");
        foreach ($permissions as $permission) {
            $this->line("  - {$permission->slug}");
        }

        $payslipsPermission = Permission::where('module', 'hr')->where('slug', 'like', '%payslip%')->first();

        if ($payslipsPermission) {
            $this->info("Payslips permission found: {$payslipsPermission->slug}");
        } else {
            $this->error("No payslips permission found!");
        }

        return 0;
    }
}
