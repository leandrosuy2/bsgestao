<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class ListUsersAndRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listar usuários e papéis disponíveis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== USUÁRIOS ===');
        $users = User::with('company')->get();

        $this->table(
            ['ID', 'Nome', 'Email', 'Empresa', 'Status', 'Papéis'],
            $users->map(function ($user) {
                $roles = $user->roles->pluck('name')->implode(', ');
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->company ? $user->company->name : 'N/A',
                    $user->is_active ? 'Ativo' : 'Inativo',
                    $roles ?: 'Nenhum'
                ];
            })
        );

                $this->info('\n=== PAPÉIS ===');
        $roles = Role::all();

        $this->table(
            ['ID', 'Nome', 'Empresa ID', 'Status', 'Permissões'],
            $roles->map(function ($role) {
                return [
                    $role->id,
                    $role->name,
                    $role->company_id,
                    $role->is_active ? 'Ativo' : 'Inativo',
                    $role->permissions->count()
                ];
            })
        );

        return 0;
    }
}
