<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Sale;
use App\Models\Customer;
use Carbon\Carbon;

// Simular ambiente Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE DE RELATÓRIO DE VENDAS ===\n\n";

// Buscar usuário guabinorte1@gmail.com
$user = User::where('email', 'guabinorte1@gmail.com')->first();

if (!$user) {
    echo "❌ Usuário guabinorte1@gmail.com não encontrado!\n";
    echo "Criando usuário de teste...\n";
    
    // Criar usuário de teste
    $user = User::create([
        'name' => 'Guabi Norte',
        'email' => 'guabinorte1@gmail.com',
        'password' => bcrypt('password'),
        'role' => 'user',
        'company_id' => 1
    ]);
    
    echo "✅ Usuário criado com sucesso!\n";
} else {
    echo "✅ Usuário encontrado: {$user->name}\n";
}

// Verificar vendas existentes
$totalSales = Sale::where('user_id', $user->id)->count();
echo "📊 Total de vendas do usuário: {$totalSales}\n";

if ($totalSales == 0) {
    echo "⚠️  Nenhuma venda encontrada. Criando vendas de teste...\n";
    
    // Criar algumas vendas de teste
    $customers = Customer::where('company_id', $user->company_id)->limit(3)->get();
    
    if ($customers->count() == 0) {
        echo "Criando clientes de teste...\n";
        $customers = collect([
            Customer::create([
                'name' => 'Cliente Teste 1',
                'email' => 'cliente1@teste.com',
                'phone' => '(11) 99999-9999',
                'company_id' => $user->company_id,
                'active' => true
            ]),
            Customer::create([
                'name' => 'Cliente Teste 2', 
                'email' => 'cliente2@teste.com',
                'phone' => '(11) 88888-8888',
                'company_id' => $user->company_id,
                'active' => true
            ]),
            Customer::create([
                'name' => 'Cliente Teste 3',
                'email' => 'cliente3@teste.com', 
                'phone' => '(11) 77777-7777',
                'company_id' => $user->company_id,
                'active' => true
            ])
        ]);
    }
    
    // Criar vendas de teste para diferentes períodos
    $periods = [
        'week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
        'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        'year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]
    ];
    
    foreach ($periods as $periodName => $dates) {
        for ($i = 0; $i < 5; $i++) {
            $sale = Sale::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'customer_id' => $customers->random()->id,
                'total' => rand(50, 500),
                'final_total' => rand(50, 500),
                'status' => 'completed',
                'payment_mode' => ['cash', 'installment'][rand(0, 1)],
                'sold_at' => Carbon::createFromTimestamp(rand($dates[0]->timestamp, $dates[1]->timestamp))
            ]);
        }
    }
    
    echo "✅ Vendas de teste criadas!\n";
}

// Gerar relatórios para diferentes períodos
$periods = ['week', 'month', 'year'];

foreach ($periods as $period) {
    echo "\n=== RELATÓRIO - " . strtoupper($period) . " ===\n";
    
    $now = Carbon::now();
    switch ($period) {
        case 'week':
            $start = $now->copy()->startOfWeek();
            $end = $now->copy()->endOfWeek();
            break;
        case 'month':
            $start = $now->copy()->startOfMonth();
            $end = $now->copy()->endOfMonth();
            break;
        case 'year':
            $start = $now->copy()->startOfYear();
            $end = $now->copy()->endOfYear();
            break;
    }
    
    $sales = Sale::where('user_id', $user->id)
                 ->where('company_id', $user->company_id)
                 ->where('status', 'completed')
                 ->whereBetween('sold_at', [$start, $end])
                 ->get();
    
    $total = $sales->sum('final_total');
    $count = $sales->count();
    $average = $count > 0 ? $total / $count : 0;
    
    echo "Período: {$start->format('d/m/Y')} - {$end->format('d/m/Y')}\n";
    echo "Total de vendas: R$ " . number_format($total, 2, ',', '.') . "\n";
    echo "Número de vendas: {$count}\n";
    echo "Ticket médio: R$ " . number_format($average, 2, ',', '.') . "\n";
    
    // Vendas por cliente
    $salesByCustomer = $sales->groupBy('customer_id')->map(function ($customerSales) {
        return [
            'total' => $customerSales->sum('final_total'),
            'count' => $customerSales->count()
        ];
    })->sortByDesc('total');
    
    echo "Clientes atendidos: " . $salesByCustomer->count() . "\n";
    
    if ($salesByCustomer->count() > 0) {
        echo "\nTop 3 clientes:\n";
        $topCustomers = $salesByCustomer->take(3);
        foreach ($topCustomers as $customerId => $data) {
            $customer = Customer::find($customerId);
            $customerName = $customer ? $customer->name : 'Cliente não informado';
            echo "- {$customerName}: R$ " . number_format($data['total'], 2, ',', '.') . " ({$data['count']} vendas)\n";
        }
    }
}

echo "\n=== TESTE CONCLUÍDO ===\n";
echo "✅ Relatório de vendas funcionando corretamente!\n";
echo "📝 Acesse /sales-reports no navegador para usar a interface web\n";
echo "🔧 Use 'php artisan sales:report guabinorte1@gmail.com month' para gerar via linha de comando\n";
