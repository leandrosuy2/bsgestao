<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Payable;
use App\Models\Receivable;
use App\Models\Employee;
use App\Models\TimeClock;
use App\Models\Payroll;
use App\Models\Vacation;
use App\Models\Leave;
use App\Models\Benefit;
use App\Models\Payslip;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Executar seeder de permissões primeiro
        $this->call([
            PermissionSeeder::class,
            DefaultRolesSeeder::class,
            UserRoleSeeder::class,
        ]);
        // Empresas
        $empresaAtiva = Company::create([
            'name' => 'Empresa Ativa',
            'email' => 'contato@ativa.com',
            'trial_start' => now()->subDays(2),
            'trial_end' => now()->addDays(3),
            'is_active' => true,
            'paid_until' => null,
        ]);
        $empresaExpirada = Company::create([
            'name' => 'Empresa Expirada',
            'email' => 'contato@expirada.com',
            'trial_start' => now()->subDays(10),
            'trial_end' => now()->subDays(5),
            'is_active' => true,
            'paid_until' => null,
        ]);
        $empresaPaga = Company::create([
            'name' => 'Empresa Paga',
            'email' => 'contato@paga.com',
            'trial_start' => now()->subDays(10),
            'trial_end' => now()->subDays(5),
            'is_active' => true,
            'paid_until' => now()->addDays(30),
        ]);
        $empresaBloqueada = Company::create([
            'name' => 'Empresa Bloqueada',
            'email' => 'contato@bloqueada.com',
            'trial_start' => now()->subDays(10),
            'trial_end' => now()->subDays(5),
            'is_active' => false,
            'paid_until' => null,
        ]);

        // Usuários admin global
        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@empresa.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'company_id' => null,
        ]);

        // Usuários de cada empresa
        User::factory()->create([
            'name' => 'Usuário Ativo',
            'email' => 'ativo@empresa.com',
            'password' => bcrypt('123456'),
            'role' => 'user',
            'company_id' => $empresaAtiva->id,
        ]);
        User::factory()->create([
            'name' => 'Usuário Expirado',
            'email' => 'expirado@empresa.com',
            'password' => bcrypt('123456'),
            'role' => 'user',
            'company_id' => $empresaExpirada->id,
        ]);
        User::factory()->create([
            'name' => 'Usuário Paga',
            'email' => 'paga@empresa.com',
            'password' => bcrypt('123456'),
            'role' => 'user',
            'company_id' => $empresaPaga->id,
        ]);
        User::factory()->create([
            'name' => 'Usuário Bloqueado',
            'email' => 'bloqueado@empresa.com',
            'password' => bcrypt('123456'),
            'role' => 'user',
            'company_id' => $empresaBloqueada->id,
        ]);

        // Funcionários da Empresa Ativa
        Employee::create([
            'name' => 'João Silva',
            'cpf' => '123.456.789-00',
            'email' => 'joao@ativa.com',
            'phone' => '(11) 99999-9999',
            'role' => 'Gerente',
            'admission_date' => Carbon::now()->subYear(),
            'username' => 'joao.silva',
            'password' => bcrypt('123456'),
            'permission_level' => 'administrador',
            'company_id' => $empresaAtiva->id,
        ]);

        Employee::create([
            'name' => 'Maria Santos',
            'cpf' => '987.654.321-00',
            'email' => 'maria@ativa.com',
            'phone' => '(11) 88888-8888',
            'role' => 'Estoquista',
            'admission_date' => Carbon::now()->subMonths(6),
            'username' => 'maria.santos',
            'password' => bcrypt('123456'),
            'permission_level' => 'operador',
            'company_id' => $empresaAtiva->id,
        ]);

        // Funcionários da Empresa Paga
        Employee::create([
            'name' => 'Pedro Costa',
            'cpf' => '111.222.333-44',
            'email' => 'pedro@paga.com',
            'phone' => '(11) 77777-7777',
            'role' => 'Vendedor',
            'admission_date' => Carbon::now()->subMonths(3),
            'username' => 'pedro.costa',
            'password' => bcrypt('123456'),
            'permission_level' => 'operador',
            'company_id' => $empresaPaga->id,
        ]);

        // Categorias da Empresa Ativa
        $categoriasAtiva = [
            ['name' => 'Eletrônicos', 'code' => 'ELET', 'description' => 'Produtos eletrônicos', 'company_id' => $empresaAtiva->id],
            ['name' => 'Informática', 'code' => 'INFO', 'description' => 'Produtos de informática', 'company_id' => $empresaAtiva->id],
            ['name' => 'Escritório', 'code' => 'ESCR', 'description' => 'Material de escritório', 'company_id' => $empresaAtiva->id],
            ['name' => 'Limpeza', 'code' => 'LIMP', 'description' => 'Produtos de limpeza', 'company_id' => $empresaAtiva->id],
            ['name' => 'Alimentação', 'code' => 'ALIM', 'description' => 'Produtos alimentícios', 'company_id' => $empresaAtiva->id],
        ];

        foreach ($categoriasAtiva as $cat) {
            Category::create($cat);
        }

        // Categorias da Empresa Paga
        $categoriasPaga = [
            ['name' => 'Vestuário', 'code' => 'VEST', 'description' => 'Roupas e acessórios', 'company_id' => $empresaPaga->id],
            ['name' => 'Calçados', 'code' => 'CALC', 'description' => 'Sapatos e tênis', 'company_id' => $empresaPaga->id],
            ['name' => 'Bolsas', 'code' => 'BOLS', 'description' => 'Bolsas e mochilas', 'company_id' => $empresaPaga->id],
        ];

        foreach ($categoriasPaga as $cat) {
            Category::create($cat);
        }

        // Produtos da Empresa Ativa
        $produtosAtiva = [
            ['name' => 'Notebook Dell', 'internal_code' => 'NB001', 'category_id' => 2, 'unit' => 'un', 'cost_price' => 2500.00, 'sale_price' => 3200.00, 'min_stock' => 3, 'company_id' => $empresaAtiva->id],
            ['name' => 'Mouse Wireless', 'internal_code' => 'MS001', 'category_id' => 2, 'unit' => 'un', 'cost_price' => 25.00, 'sale_price' => 45.00, 'min_stock' => 15, 'company_id' => $empresaAtiva->id],
            ['name' => 'Teclado Mecânico', 'internal_code' => 'TC001', 'category_id' => 2, 'unit' => 'un', 'cost_price' => 120.00, 'sale_price' => 180.00, 'min_stock' => 8, 'company_id' => $empresaAtiva->id],
            ['name' => 'Papel A4', 'internal_code' => 'PP001', 'category_id' => 3, 'unit' => 'pacote', 'cost_price' => 15.00, 'sale_price' => 25.00, 'min_stock' => 20, 'company_id' => $empresaAtiva->id],
            ['name' => 'Caneta Bic', 'internal_code' => 'CN001', 'category_id' => 3, 'unit' => 'un', 'cost_price' => 1.50, 'sale_price' => 3.00, 'min_stock' => 50, 'company_id' => $empresaAtiva->id],
            ['name' => 'Detergente', 'internal_code' => 'DT001', 'category_id' => 4, 'unit' => 'l', 'cost_price' => 8.00, 'sale_price' => 12.00, 'min_stock' => 10, 'company_id' => $empresaAtiva->id],
            ['name' => 'Café em Pó', 'internal_code' => 'CF001', 'category_id' => 5, 'unit' => 'kg', 'cost_price' => 12.00, 'sale_price' => 18.00, 'min_stock' => 5, 'company_id' => $empresaAtiva->id],
        ];

        foreach ($produtosAtiva as $prod) {
            Product::create($prod);
        }

        // Produtos da Empresa Paga
        $produtosPaga = [
            ['name' => 'Camiseta Básica', 'internal_code' => 'CAM001', 'category_id' => 6, 'unit' => 'un', 'cost_price' => 15.00, 'sale_price' => 35.00, 'min_stock' => 20, 'company_id' => $empresaPaga->id],
            ['name' => 'Tênis Esportivo', 'internal_code' => 'TEN001', 'category_id' => 7, 'unit' => 'un', 'cost_price' => 80.00, 'sale_price' => 150.00, 'min_stock' => 10, 'company_id' => $empresaPaga->id],
            ['name' => 'Bolsa Feminina', 'internal_code' => 'BOL001', 'category_id' => 8, 'unit' => 'un', 'cost_price' => 45.00, 'sale_price' => 89.00, 'min_stock' => 8, 'company_id' => $empresaPaga->id],
        ];

        foreach ($produtosPaga as $prod) {
            Product::create($prod);
        }

        // Movimentações de estoque da Empresa Ativa
        $movimentacoesAtiva = [
            ['product_id' => 1, 'user_id' => 2, 'type' => 'entrada', 'quantity' => 5, 'date' => Carbon::now()->subDays(5), 'notes' => 'Compra inicial', 'movement_reason' => 'compra'],
            ['product_id' => 1, 'user_id' => 2, 'type' => 'saida', 'quantity' => 2, 'date' => Carbon::now()->subDays(3), 'notes' => 'Venda', 'movement_reason' => 'venda'],
            ['product_id' => 2, 'user_id' => 2, 'type' => 'entrada', 'quantity' => 20, 'date' => Carbon::now()->subDays(10), 'notes' => 'Compra', 'movement_reason' => 'compra'],
            ['product_id' => 2, 'user_id' => 2, 'type' => 'saida', 'quantity' => 5, 'date' => Carbon::now()->subDays(2), 'notes' => 'Venda', 'movement_reason' => 'venda'],
            ['product_id' => 3, 'user_id' => 2, 'type' => 'entrada', 'quantity' => 10, 'date' => Carbon::now()->subDays(7), 'notes' => 'Compra', 'movement_reason' => 'compra'],
            ['product_id' => 4, 'user_id' => 2, 'type' => 'entrada', 'quantity' => 30, 'date' => Carbon::now()->subDays(15), 'notes' => 'Compra', 'movement_reason' => 'compra'],
            ['product_id' => 4, 'user_id' => 2, 'type' => 'saida', 'quantity' => 10, 'date' => Carbon::now()->subDays(1), 'notes' => 'Uso interno', 'movement_reason' => 'ajuste'],
        ];

        foreach ($movimentacoesAtiva as $mov) {
            StockMovement::create($mov);
        }

        // Movimentações de estoque da Empresa Paga
        $movimentacoesPaga = [
            ['product_id' => 8, 'user_id' => 4, 'type' => 'entrada', 'quantity' => 25, 'date' => Carbon::now()->subDays(3), 'notes' => 'Compra inicial', 'movement_reason' => 'compra'],
            ['product_id' => 9, 'user_id' => 4, 'type' => 'entrada', 'quantity' => 15, 'date' => Carbon::now()->subDays(2), 'notes' => 'Compra', 'movement_reason' => 'compra'],
            ['product_id' => 10, 'user_id' => 4, 'type' => 'entrada', 'quantity' => 12, 'date' => Carbon::now()->subDays(1), 'notes' => 'Compra', 'movement_reason' => 'compra'],
        ];

        foreach ($movimentacoesPaga as $mov) {
            StockMovement::create($mov);
        }

        // Contas a pagar da Empresa Ativa
        $contasPagarAtiva = [
            ['descricao' => 'Fornecedor Eletrônicos Ltda', 'pessoa' => 'Fornecedor Eletrônicos', 'categoria' => 'Fornecedores', 'valor' => 5000.00, 'data_vencimento' => Carbon::now()->addDays(5), 'status' => 'pendente', 'forma_pagamento' => 'PIX', 'criado_por' => 1, 'company_id' => $empresaAtiva->id],
            ['descricao' => 'Aluguel Escritório', 'pessoa' => 'Imobiliária Central', 'categoria' => 'Despesas Fixas', 'valor' => 2500.00, 'data_vencimento' => Carbon::now()->addDays(3), 'status' => 'pendente', 'forma_pagamento' => 'Transferência', 'criado_por' => 1, 'company_id' => $empresaAtiva->id],
            ['descricao' => 'Energia Elétrica', 'pessoa' => 'Companhia Energética', 'categoria' => 'Serviços Públicos', 'valor' => 800.00, 'data_vencimento' => Carbon::now()->subDays(2), 'status' => 'pago', 'data_pagamento' => Carbon::now()->subDays(1), 'forma_pagamento' => 'Boleto', 'criado_por' => 1, 'company_id' => $empresaAtiva->id],
            ['descricao' => 'Internet', 'pessoa' => 'Provedor Net', 'categoria' => 'Serviços Públicos', 'valor' => 150.00, 'data_vencimento' => Carbon::now()->addDays(10), 'status' => 'pendente', 'forma_pagamento' => 'Cartão', 'criado_por' => 1, 'company_id' => $empresaAtiva->id],
        ];

        foreach ($contasPagarAtiva as $conta) {
            Payable::create($conta);
        }

        // Contas a pagar da Empresa Paga
        $contasPagarPaga = [
            ['descricao' => 'Fornecedor de Roupas', 'pessoa' => 'Fornecedor Fashion', 'categoria' => 'Fornecedores', 'valor' => 3000.00, 'data_vencimento' => Carbon::now()->addDays(7), 'status' => 'pendente', 'forma_pagamento' => 'PIX', 'criado_por' => 3, 'company_id' => $empresaPaga->id],
            ['descricao' => 'Aluguel Loja', 'pessoa' => 'Shopping Center', 'categoria' => 'Despesas Fixas', 'valor' => 4000.00, 'data_vencimento' => Carbon::now()->addDays(2), 'status' => 'pendente', 'forma_pagamento' => 'Transferência', 'criado_por' => 3, 'company_id' => $empresaPaga->id],
        ];

        foreach ($contasPagarPaga as $conta) {
            Payable::create($conta);
        }

        // Contas a receber da Empresa Ativa
        $contasReceberAtiva = [
            ['descricao' => 'Venda Cliente A', 'pessoa' => 'Cliente A Ltda', 'categoria' => 'Vendas', 'valor' => 3200.00, 'data_vencimento' => Carbon::now()->addDays(7), 'status' => 'pendente', 'forma_recebimento' => 'PIX', 'criado_por' => 1, 'company_id' => $empresaAtiva->id],
            ['descricao' => 'Venda Cliente B', 'pessoa' => 'Cliente B S/A', 'categoria' => 'Vendas', 'valor' => 1800.00, 'data_vencimento' => Carbon::now()->subDays(1), 'status' => 'recebido', 'data_recebimento' => Carbon::now(), 'forma_recebimento' => 'Transferência', 'criado_por' => 1, 'company_id' => $empresaAtiva->id],
            ['descricao' => 'Serviço Consultoria', 'pessoa' => 'Empresa C', 'categoria' => 'Serviços', 'valor' => 2500.00, 'data_vencimento' => Carbon::now()->addDays(15), 'status' => 'pendente', 'forma_recebimento' => 'Boleto', 'criado_por' => 1, 'company_id' => $empresaAtiva->id],
            ['descricao' => 'Venda Cliente D', 'pessoa' => 'Cliente D', 'categoria' => 'Vendas', 'valor' => 900.00, 'data_vencimento' => Carbon::now()->addDays(2), 'status' => 'pendente', 'forma_recebimento' => 'PIX', 'criado_por' => 1, 'company_id' => $empresaAtiva->id],
        ];

        foreach ($contasReceberAtiva as $conta) {
            Receivable::create($conta);
        }

        // Contas a receber da Empresa Paga
        $contasReceberPaga = [
            ['descricao' => 'Venda Loja Shopping', 'pessoa' => 'Shopping Center', 'categoria' => 'Vendas', 'valor' => 2500.00, 'data_vencimento' => Carbon::now()->addDays(5), 'status' => 'pendente', 'forma_recebimento' => 'PIX', 'criado_por' => 3, 'company_id' => $empresaPaga->id],
            ['descricao' => 'Venda Cliente VIP', 'pessoa' => 'Cliente VIP', 'categoria' => 'Vendas', 'valor' => 1200.00, 'data_vencimento' => Carbon::now()->subDays(2), 'status' => 'recebido', 'data_recebimento' => Carbon::now()->subDays(1), 'forma_recebimento' => 'Cartão', 'criado_por' => 3, 'company_id' => $empresaPaga->id],
        ];

        foreach ($contasReceberPaga as $conta) {
            Receivable::create($conta);
        }

        // TimeClocks (ponto) da Empresa Ativa
        TimeClock::create([
            'employee_id' => 1,
            'data' => now()->subDays(1)->toDateString(),
            'hora_entrada' => '08:00',
            'hora_intervalo_inicio' => '12:00',
            'hora_intervalo_fim' => '13:00',
            'hora_saida' => '17:00',
            'observacao' => 'Dia normal',
        ]);
        TimeClock::create([
            'employee_id' => 2,
            'data' => now()->subDays(1)->toDateString(),
            'hora_entrada' => '09:00',
            'hora_intervalo_inicio' => '12:30',
            'hora_intervalo_fim' => '13:30',
            'hora_saida' => '18:00',
            'observacao' => 'Chegou atrasado',
        ]);

        // TimeClocks (ponto) da Empresa Paga
        TimeClock::create([
            'employee_id' => 3,
            'data' => now()->subDays(1)->toDateString(),
            'hora_entrada' => '08:30',
            'hora_intervalo_inicio' => '12:00',
            'hora_intervalo_fim' => '13:00',
            'hora_saida' => '17:30',
            'observacao' => 'Dia normal',
        ]);

        // Payrolls (folha de pagamento)
        $payroll1 = Payroll::create([
            'employee_id' => 1,
            'competencia' => now()->format('m/Y'),
            'salario_base' => 5000,
            'descontos' => 500,
            'adicionais' => 200,
            'total_liquido' => 4700,
            'status' => 'pago',
            'data_pagamento' => now()->toDateString(),
            'observacao' => 'Folha do mês',
        ]);
        $payroll2 = Payroll::create([
            'employee_id' => 2,
            'competencia' => now()->format('m/Y'),
            'salario_base' => 3000,
            'descontos' => 200,
            'adicionais' => 100,
            'total_liquido' => 2900,
            'status' => 'pendente',
            'observacao' => 'Folha do mês',
        ]);

        // Vacations (férias)
        Vacation::create([
            'employee_id' => 1,
            'data_inicio' => now()->addDays(10)->toDateString(),
            'data_fim' => now()->addDays(30)->toDateString(),
            'dias' => 20,
            'status' => 'aprovada',
            'data_solicitacao' => now()->subDays(5)->toDateString(),
            'data_aprovacao' => now()->toDateString(),
            'observacao' => 'Férias programadas',
        ]);

        // Leaves (licenças)
        Leave::create([
            'employee_id' => 2,
            'tipo' => 'Licença médica',
            'data_inicio' => now()->subDays(3)->toDateString(),
            'data_fim' => now()->addDays(2)->toDateString(),
            'dias' => 5,
            'status' => 'aprovada',
            'observacao' => 'Atestado médico',
        ]);

        // Benefits (benefícios)
        Benefit::create([
            'employee_id' => 1,
            'tipo' => 'Vale Transporte',
            'valor' => 200,
            'status' => 'ativo',
            'data_inicio' => now()->subMonths(6)->toDateString(),
            'observacao' => 'Benefício mensal',
        ]);
        Benefit::create([
            'employee_id' => 2,
            'tipo' => 'Plano de Saúde',
            'valor' => 400,
            'status' => 'ativo',
            'data_inicio' => now()->subMonths(3)->toDateString(),
            'observacao' => 'Plano familiar',
        ]);

        // Payslips (holerites)
        Payslip::create([
            'employee_id' => 1,
            'payroll_id' => $payroll1->id,
            'competencia' => now()->format('m/Y'),
            'arquivo' => 'holerites/joao_silva_' . now()->format('m_Y') . '.pdf',
            'data_geracao' => now()->toDateString(),
            'observacao' => 'Holerite digital',
        ]);
    }
}
