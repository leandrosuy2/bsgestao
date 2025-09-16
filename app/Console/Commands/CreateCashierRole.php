<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Console\Command;

class CreateCashierRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:create-cashier';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria o papel de Frente de Caixa com as permissões necessárias';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Criando papel de Frente de Caixa...');

        // Criar o papel
        $role = Role::firstOrCreate(
            ['name' => 'Frente de Caixa'],
            [
                'company_id' => 1, // Empresa padrão
                'description' => 'Responsável pelo atendimento no caixa e PDV',
                'is_active' => true
            ]
        );

        // Permissões necessárias para Frente de Caixa
        $permissions = [
            'view-products',        // Visualizar produtos
            'view-categories',      // Visualizar categorias
            'view-cash-register',   // Visualizar caixa
            'open-cash-register',   // Abrir caixa
            'close-cash-register',  // Fechar caixa
            'move-cash-register',   // Movimentar caixa
            'pdv',                  // Acessar PDV
            'quotes',               // Gerenciar orçamentos
            'view-reports',         // Visualizar relatórios básicos
        ];

        // Buscar as permissões
        $permissionModels = Permission::whereIn('slug', $permissions)->get();

        if ($permissionModels->count() !== count($permissions)) {
            $this->warn('Algumas permissões não foram encontradas. Verifique se o PermissionSeeder foi executado.');
        }

        // Atribuir permissões ao papel
        $role->permissions()->sync($permissionModels->pluck('id'));

        $this->info("Papel 'Frente de Caixa' criado com sucesso!");
        $this->info("ID do papel: {$role->id}");
        $this->info("Permissões atribuídas: " . $permissionModels->count());

        $this->table(
            ['Permissão', 'Descrição'],
            $permissionModels->map(function ($permission) {
                return [$permission->name, $permission->description];
            })->toArray()
        );

        return 0;
    }
}
