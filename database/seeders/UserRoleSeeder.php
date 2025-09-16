<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        foreach ($users as $user) {
            if ($user->roles()->count() === 0) {
                // Tenta pegar o papel Administrativo da empresa
                $role = Role::where('company_id', $user->company_id)
                    ->where('name', 'Administrativo')
                    ->first();
                // Se não existir, pega o primeiro papel da empresa
                if (!$role) {
                    $role = Role::where('company_id', $user->company_id)->first();
                }
                if ($role) {
                    $user->roles()->attach($role->id);
                    $this->command->info("Usuário {$user->name} recebeu o papel {$role->name}");
                } else {
                    $this->command->warn("Usuário {$user->name} não tem papel disponível na empresa {$user->company_id}");
                }
            }
        }
    }
}
