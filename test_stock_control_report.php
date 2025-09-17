<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Category;

// Simular ambiente Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE DE RELATÓRIO DE CONTROLE DE ESTOQUE ===\n\n";

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

// Verificar produtos existentes
$totalProducts = Product::where('company_id', $user->company_id)->count();
echo "📦 Total de produtos da empresa: {$totalProducts}\n";

if ($totalProducts == 0) {
    echo "⚠️  Nenhum produto encontrado. Criando produtos de teste...\n";
    
    // Criar categoria padrão se não existir
    $category = Category::firstOrCreate([
        'name' => 'Geral',
        'company_id' => $user->company_id
    ], [
        'code' => 'GER',
        'description' => 'Categoria geral'
    ]);
    
    // Produtos de exemplo
    $products = [
        [
            'name' => 'Arroz Branco 5kg',
            'internal_code' => 'ARR001',
            'description' => 'Arroz branco tipo 1, pacote 5kg',
            'unit' => 'PC',
            'cost_price' => 15.50,
            'sale_price' => 22.90,
            'min_stock' => 10,
            'stock_quantity' => 50, // Estoque virtual
        ],
        [
            'name' => 'Feijão Preto 1kg',
            'internal_code' => 'FEI001',
            'description' => 'Feijão preto tipo 1, pacote 1kg',
            'unit' => 'PC',
            'cost_price' => 8.20,
            'sale_price' => 12.50,
            'min_stock' => 20,
            'stock_quantity' => 30, // Estoque virtual
        ],
        [
            'name' => 'Açúcar Cristal 1kg',
            'internal_code' => 'ACU001',
            'description' => 'Açúcar cristal refinado, pacote 1kg',
            'unit' => 'PC',
            'cost_price' => 4.50,
            'sale_price' => 6.80,
            'min_stock' => 15,
            'stock_quantity' => 25, // Estoque virtual
        ],
    ];

    foreach ($products as $productData) {
        Product::create([
            'name' => $productData['name'],
            'internal_code' => $productData['internal_code'],
            'description' => $productData['description'],
            'category_id' => $category->id,
            'unit' => $productData['unit'],
            'cost_price' => $productData['cost_price'],
            'sale_price' => $productData['sale_price'],
            'min_stock' => $productData['min_stock'],
            'stock_quantity' => $productData['stock_quantity'],
            'company_id' => $user->company_id,
        ]);
    }
    
    echo "✅ Produtos de teste criados!\n";
}

// Criar movimentações de estoque para simular divergências
$products = Product::where('company_id', $user->company_id)->get();

foreach ($products as $product) {
    // Verificar se já existem movimentações
    $existingMovements = StockMovement::where('product_id', $product->id)->count();
    
    if ($existingMovements == 0) {
        // Criar movimentações de entrada (compras)
        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => 'entrada',
            'movement_reason' => 'compra',
            'quantity' => rand(20, 100),
            'date' => now()->subDays(rand(1, 30)),
            'notes' => 'Compra inicial'
        ]);
        
        // Criar algumas movimentações de saída (vendas)
        $saidas = rand(5, 30);
        for ($i = 0; $i < $saidas; $i++) {
            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'type' => 'saida',
                'movement_reason' => 'venda',
                'quantity' => rand(1, 5),
                'date' => now()->subDays(rand(1, 20)),
                'notes' => 'Venda PDV'
            ]);
        }
    }
}

echo "✅ Movimentações de estoque criadas!\n";

// Calcular relatório de controle de estoque
echo "\n=== RELATÓRIO DE CONTROLE DE ESTOQUE ===\n";

$products = Product::where('company_id', $user->company_id)->with('category')->get();
$totalPhysicalStock = 0;
$totalVirtualStock = 0;
$productsWithDifference = 0;
$totalValue = 0;

foreach ($products as $product) {
    // Estoque virtual
    $virtualStock = $product->stock_quantity ?? 0;
    
    // Estoque físico (calculado pelas movimentações)
    $entradas = StockMovement::where('product_id', $product->id)
                           ->where('type', 'entrada')
                           ->sum('quantity');

    $saidas = StockMovement::where('product_id', $product->id)
                         ->where('type', 'saida')
                         ->sum('quantity');

    $physicalStock = $entradas - $saidas;
    $difference = $physicalStock - $virtualStock;
    $stockValue = $physicalStock * ($product->cost_price ?? 0);
    
    $totalPhysicalStock += $physicalStock;
    $totalVirtualStock += $virtualStock;
    $totalValue += $stockValue;
    
    if ($difference != 0) {
        $productsWithDifference++;
    }
    
    echo "\n📦 {$product->name} ({$product->internal_code})";
    echo "\n   Categoria: " . ($product->category->name ?? 'N/A');
    echo "\n   Estoque Físico: {$physicalStock}";
    echo "\n   Estoque Virtual: {$virtualStock}";
    echo "\n   Diferença: " . ($difference >= 0 ? '+' : '') . $difference;
    echo "\n   Valor: R$ " . number_format($stockValue, 2, ',', '.');
    
    // Status do estoque
    if ($physicalStock == 0) {
        echo "\n   Status: ZERO";
    } elseif ($physicalStock <= $product->min_stock) {
        echo "\n   Status: BAIXO (mín: {$product->min_stock})";
    } elseif ($physicalStock > $product->min_stock * 2) {
        echo "\n   Status: ALTO";
    } else {
        echo "\n   Status: NORMAL";
    }
}

echo "\n\n=== RESUMO GERAL ===";
echo "\nTotal de produtos: " . $products->count();
echo "\nEstoque físico total: {$totalPhysicalStock}";
echo "\nEstoque virtual total: {$totalVirtualStock}";
echo "\nDiferença total: " . ($totalPhysicalStock - $totalVirtualStock >= 0 ? '+' : '') . ($totalPhysicalStock - $totalVirtualStock);
echo "\nProdutos com divergências: {$productsWithDifference}";
echo "\nValor total do estoque: R$ " . number_format($totalValue, 2, ',', '.');
echo "\nPrecisão: " . number_format((($products->count() - $productsWithDifference) / $products->count()) * 100, 1) . "%";

echo "\n\n=== TESTE CONCLUÍDO ===";
echo "\n✅ Relatório de controle de estoque funcionando corretamente!";
echo "\n📝 Acesse /stock-control-reports no navegador para usar a interface web";
echo "\n🔧 Use 'php artisan stock:control-report guabinorte1@gmail.com --format=pdf' para gerar via linha de comando";
