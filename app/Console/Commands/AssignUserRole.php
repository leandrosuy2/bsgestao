<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class AssignUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-role {user_id} {role_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atribuir um papel a um usuário';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $roleId = $this->argument('role_id');

        $user = User::find($userId);
        $role = Role::find($roleId);

        if (!$user) {
            $this->error("Usuário com ID {$userId} não encontrado!");
            return 1;
        }

        if (!$role) {
            $this->error("Papel com ID {$roleId} não encontrado!");
            return 1;
        }

        // Verificar se o papel pertence à mesma empresa do usuário
        if ($user->company_id !== $role->company_id) {
            $this->error("O papel não pertence à mesma empresa do usuário!");
            return 1;
        }

        // Verificar se já tem o papel
        if ($user->roles()->where('role_id', $roleId)->exists()) {
            $this->warn("O usuário já possui este papel!");
            return 0;
        }

        // Atribuir o papel
        $user->roles()->attach($roleId);

        $this->info("Papel '{$role->name}' atribuído com sucesso ao usuário '{$user->name}'!");

        return 0;
    }
}
