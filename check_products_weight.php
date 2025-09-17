<?php

require_once 'vendor/autoload.php';

use App\Models\Product;
use App\Models\SaleItem;

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICAÇÃO DE PRODUTOS E PESO ===\n\n";

try {
    // Buscar produtos da empresa
    $products = Product::where('company_id', 1)->take(10)->get(['name', 'unit']);
    
    echo "📦 PRODUTOS E UNIDADES:\n";
    echo str_repeat("-", 80) . "\n";
    
    foreach ($products as $product) {
        echo "Nome: " . $product->name . "\n";
        echo "Unidade: " . $product->unit . "\n";
        echo str_repeat("-", 40) . "\n";
    }
    
    // Verificar se há informações de peso nos nomes
    echo "\n🔍 ANÁLISE DE PESO NOS NOMES:\n";
    echo str_repeat("-", 60) . "\n";
    
    foreach ($products as $product) {
        $name = $product->name;
        
        // Procurar por padrões de peso
        if (preg_match('/(\d+)\s*KG/i', $name, $matches)) {
            echo "✅ {$name} - Peso encontrado: {$matches[1]} KG\n";
        } elseif (preg_match('/(\d+)\s*T\s*(\d+)\s*KG/i', $name, $matches)) {
            $totalKg = ($matches[1] * 1000) + $matches[2];
            echo "✅ {$name} - Peso encontrado: {$totalKg} KG ({$matches[1]}T {$matches[2]}KG)\n";
        } else {
            echo "❌ {$name} - Nenhum peso encontrado\n";
        }
    }
    
    echo "\n💡 SUGESTÃO:\n";
    echo str_repeat("-", 60) . "\n";
    echo "Vou criar uma função para extrair o peso dos nomes dos produtos\n";
    echo "e calcular o total de quilos no romaneio.\n";
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    exit(1);
}
