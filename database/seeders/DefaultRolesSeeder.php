<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            // Papel: Estoquista
            $estoquista = Role::create([
                'company_id' => $company->id,
                'name' => 'Estoquista',
                'description' => 'Responsável pelo controle de estoque, produtos e movimentações',
                'is_active' => true,
            ]);

            // Permissões do Estoquista
            $permissionsEstoquista = [
                'view-products', 'create-products', 'edit-products',
                'view-categories', 'create-categories', 'edit-categories',
                'view-inventory', 'move-inventory', 'view-movements',
                'inventory-reports'
            ];

            $estoquista->permissions()->attach(
                Permission::whereIn('slug', $permissionsEstoquista)->pluck('id')
            );

            // Papel: Financeiro
            $financeiro = Role::create([
                'company_id' => $company->id,
                'name' => 'Financeiro',
                'description' => 'Responsável pelo controle financeiro, contas a pagar e receber',
                'is_active' => true,
            ]);

            // Permissões do Financeiro
            $permissionsFinanceiro = [
                'view-finance', 'view-payables', 'create-payables', 'edit-payables', 'delete-payables',
                'view-receivables', 'create-receivables', 'edit-receivables', 'delete-receivables',
                'view-reports', 'financial-reports'
            ];

            $financeiro->permissions()->attach(
                Permission::whereIn('slug', $permissionsFinanceiro)->pluck('id')
            );

            // Papel: Administrativo
            $administrativo = Role::create([
                'company_id' => $company->id,
                'name' => 'Administrativo',
                'description' => 'Acesso completo ao sistema, pode gerenciar usuários e papéis',
                'is_active' => true,
            ]);

            // Permissões do Administrativo (todas as permissões)
            $administrativo->permissions()->attach(Permission::all());
        }
    }
}
