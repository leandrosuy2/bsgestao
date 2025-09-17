<?php

require_once 'vendor/autoload.php';

use App\Models\Product;

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICAÇÃO DE PRODUTOS ESPECÍFICOS ===\n\n";

try {
    // Buscar produtos específicos que aparecem nas vendas
    $productNames = [
        'DO SITIO AVES INICIAL NATURAL T 20 KG',
        'DO SITIO FRANGO NATURAL T 20 KG', 
        'PROAVE FRANGOS T 20 KG'
    ];
    
    echo "📦 PRODUTOS DAS VENDAS:\n";
    echo str_repeat("-", 80) . "\n";
    
    foreach ($productNames as $name) {
        $product = Product::where('company_id', 1)->where('name', 'like', '%' . $name . '%')->first();
        
        if ($product) {
            echo "✅ Encontrado: {$product->name}\n";
            echo "   Unidade: {$product->unit}\n";
            
            // Extrair peso do nome
            if (preg_match('/(\d+)\s*KG/i', $product->name, $matches)) {
                echo "   Peso: {$matches[1]} KG\n";
            } elseif (preg_match('/(\d+)\s*T\s*(\d+)\s*KG/i', $product->name, $matches)) {
                $totalKg = ($matches[1] * 1000) + $matches[2];
                echo "   Peso: {$totalKg} KG ({$matches[1]}T {$matches[2]}KG)\n";
            } else {
                echo "   Peso: Não encontrado no nome\n";
            }
        } else {
            echo "❌ Não encontrado: {$name}\n";
        }
        echo str_repeat("-", 40) . "\n";
    }
    
    echo "\n💡 IMPLEMENTAÇÃO:\n";
    echo str_repeat("-", 60) . "\n";
    echo "Vou criar uma função para extrair peso dos nomes dos produtos\n";
    echo "e adicionar o cálculo de total de quilos no romaneio.\n";
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    exit(1);
}
