<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Módulo de Produtos
            [
                'name' => 'Visualizar Produtos',
                'slug' => 'view-products',
                'module' => 'products',
                'description' => 'Permite visualizar a lista de produtos'
            ],
            [
                'name' => 'Criar Produtos',
                'slug' => 'create-products',
                'module' => 'products',
                'description' => 'Permite criar novos produtos'
            ],
            [
                'name' => 'Editar Produtos',
                'slug' => 'edit-products',
                'module' => 'products',
                'description' => 'Permite editar produtos existentes'
            ],
            [
                'name' => 'Excluir Produtos',
                'slug' => 'delete-products',
                'module' => 'products',
                'description' => 'Permite excluir produtos'
            ],

            // Módulo de Categorias
            [
                'name' => 'Visualizar Categorias',
                'slug' => 'view-categories',
                'module' => 'categories',
                'description' => 'Permite visualizar a lista de categorias'
            ],
            [
                'name' => 'Criar Categorias',
                'slug' => 'create-categories',
                'module' => 'categories',
                'description' => 'Permite criar novas categorias'
            ],
            [
                'name' => 'Editar Categorias',
                'slug' => 'edit-categories',
                'module' => 'categories',
                'description' => 'Permite editar categorias existentes'
            ],
            [
                'name' => 'Excluir Categorias',
                'slug' => 'delete-categories',
                'module' => 'categories',
                'description' => 'Permite excluir categorias'
            ],

            // Módulo de Estoque
            [
                'name' => 'Visualizar Estoque',
                'slug' => 'view-inventory',
                'module' => 'inventory',
                'description' => 'Permite visualizar o estoque atual'
            ],
            [
                'name' => 'Movimentar Estoque',
                'slug' => 'move-inventory',
                'module' => 'inventory',
                'description' => 'Permite fazer movimentações de estoque'
            ],
            [
                'name' => 'Visualizar Movimentações',
                'slug' => 'view-movements',
                'module' => 'inventory',
                'description' => 'Permite visualizar histórico de movimentações'
            ],

            // Módulo Financeiro
            [
                'name' => 'Visualizar Financeiro',
                'slug' => 'view-finance',
                'module' => 'finance',
                'description' => 'Permite visualizar informações financeiras'
            ],
            [
                'name' => 'Visualizar Contas a Pagar',
                'slug' => 'view-payables',
                'module' => 'finance',
                'description' => 'Permite visualizar contas a pagar'
            ],
            [
                'name' => 'Criar Contas a Pagar',
                'slug' => 'create-payables',
                'module' => 'finance',
                'description' => 'Permite criar contas a pagar'
            ],
            [
                'name' => 'Editar Contas a Pagar',
                'slug' => 'edit-payables',
                'module' => 'finance',
                'description' => 'Permite editar contas a pagar'
            ],
            [
                'name' => 'Excluir Contas a Pagar',
                'slug' => 'delete-payables',
                'module' => 'finance',
                'description' => 'Permite excluir contas a pagar'
            ],
            [
                'name' => 'Visualizar Contas a Receber',
                'slug' => 'view-receivables',
                'module' => 'finance',
                'description' => 'Permite visualizar contas a receber'
            ],
            [
                'name' => 'Criar Contas a Receber',
                'slug' => 'create-receivables',
                'module' => 'finance',
                'description' => 'Permite criar contas a receber'
            ],
            [
                'name' => 'Editar Contas a Receber',
                'slug' => 'edit-receivables',
                'module' => 'finance',
                'description' => 'Permite editar contas a receber'
            ],
            [
                'name' => 'Excluir Contas a Receber',
                'slug' => 'delete-receivables',
                'module' => 'finance',
                'description' => 'Permite excluir contas a receber'
            ],

            // Módulo de Relatórios
            [
                'name' => 'Visualizar Relatórios',
                'slug' => 'view-reports',
                'module' => 'reports',
                'description' => 'Permite visualizar relatórios'
            ],
            [
                'name' => 'Relatórios de Estoque',
                'slug' => 'inventory-reports',
                'module' => 'reports',
                'description' => 'Permite visualizar relatórios de estoque'
            ],
            [
                'name' => 'Relatórios Financeiros',
                'slug' => 'financial-reports',
                'module' => 'reports',
                'description' => 'Permite visualizar relatórios financeiros'
            ],

            // Módulo de Funcionários
            [
                'name' => 'Visualizar Funcionários',
                'slug' => 'view-employees',
                'module' => 'employees',
                'description' => 'Permite visualizar lista de funcionários'
            ],
            [
                'name' => 'Criar Funcionários',
                'slug' => 'create-employees',
                'module' => 'employees',
                'description' => 'Permite criar novos funcionários'
            ],
            [
                'name' => 'Editar Funcionários',
                'slug' => 'edit-employees',
                'module' => 'employees',
                'description' => 'Permite editar funcionários'
            ],
            [
                'name' => 'Excluir Funcionários',
                'slug' => 'delete-employees',
                'module' => 'employees',
                'description' => 'Permite excluir funcionários'
            ],

            // Módulo de RH
            [
                'name' => 'Visualizar RH',
                'slug' => 'view-hr',
                'module' => 'hr',
                'description' => 'Permite visualizar módulo de RH'
            ],
            [
                'name' => 'Ponto Eletrônico',
                'slug' => 'time-clock',
                'module' => 'hr',
                'description' => 'Permite gerenciar ponto eletrônico'
            ],
            [
                'name' => 'Férias',
                'slug' => 'vacations',
                'module' => 'hr',
                'description' => 'Permite gerenciar férias'
            ],
            [
                'name' => 'Licenças',
                'slug' => 'leaves',
                'module' => 'hr',
                'description' => 'Permite gerenciar licenças'
            ],
            [
                'name' => 'Folha de Pagamento',
                'slug' => 'payroll',
                'module' => 'hr',
                'description' => 'Permite gerenciar folha de pagamento'
            ],
            [
                'name' => 'Benefícios',
                'slug' => 'benefits',
                'module' => 'hr',
                'description' => 'Permite gerenciar benefícios'
            ],

            // Módulo de Usuários e Papéis
            [
                'name' => 'Visualizar Usuários',
                'slug' => 'view-users',
                'module' => 'users',
                'description' => 'Permite visualizar lista de usuários'
            ],
            [
                'name' => 'Criar Usuários',
                'slug' => 'create-users',
                'module' => 'users',
                'description' => 'Permite criar novos usuários'
            ],
            [
                'name' => 'Editar Usuários',
                'slug' => 'edit-users',
                'module' => 'users',
                'description' => 'Permite editar usuários'
            ],
            [
                'name' => 'Excluir Usuários',
                'slug' => 'delete-users',
                'module' => 'users',
                'description' => 'Permite excluir usuários'
            ],
            [
                'name' => 'Gerenciar Papéis',
                'slug' => 'manage-roles',
                'module' => 'users',
                'description' => 'Permite gerenciar papéis e permissões'
            ],

            // Módulo de Frente de Caixa
            [
                'name' => 'Visualizar Caixa',
                'slug' => 'view-cash-register',
                'module' => 'cash-register',
                'description' => 'Permite visualizar informações do caixa'
            ],
            [
                'name' => 'Abrir Caixa',
                'slug' => 'open-cash-register',
                'module' => 'cash-register',
                'description' => 'Permite abrir o caixa'
            ],
            [
                'name' => 'Fechar Caixa',
                'slug' => 'close-cash-register',
                'module' => 'cash-register',
                'description' => 'Permite fechar o caixa'
            ],
            [
                'name' => 'Movimentar Caixa',
                'slug' => 'move-cash-register',
                'module' => 'cash-register',
                'description' => 'Permite fazer movimentações no caixa'
            ],
            [
                'name' => 'PDV',
                'slug' => 'pdv',
                'module' => 'cash-register',
                'description' => 'Permite acessar o PDV'
            ],
            [
                'name' => 'Orçamentos',
                'slug' => 'quotes',
                'module' => 'cash-register',
                'description' => 'Permite gerenciar orçamentos'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
