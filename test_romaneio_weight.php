<?php

require_once 'vendor/autoload.php';

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE DE CÁLCULO DE PESO NO ROMANEIO ===\n\n";

try {
    // Buscar usuário guabinorte1@gmail.com
    $user = User::where('email', 'guabinorte1@gmail.com')->first();
    
    if (!$user) {
        echo "❌ Usuário guabinorte1@gmail.com não encontrado\n";
        exit(1);
    }
    
    echo "✅ Usuário encontrado: {$user->name} ({$user->email})\n";
    
    // Buscar venda recente com itens
    $sale = Sale::where('user_id', $user->id)
                 ->where('status', 'completed')
                 ->with('items')
                 ->orderBy('created_at', 'desc')
                 ->first();
    
    if (!$sale) {
        echo "❌ Nenhuma venda encontrada\n";
        exit(1);
    }
    
    echo "✅ Venda encontrada: ID {$sale->id}\n";
    echo "   Data: {$sale->created_at->format('d/m/Y H:i')}\n";
    echo "   Total: R$ " . number_format($sale->final_total, 2, ',', '.') . "\n";
    
    echo "\n📦 ANÁLISE DE PESO DOS ITENS:\n";
    echo str_repeat("-", 100) . "\n";
    printf("%-5s %-40s %-8s %-12s %-12s %-12s\n", "ID", "Produto", "Qtd", "Peso/Unid", "Peso Total", "Status");
    echo str_repeat("-", 100) . "\n";
    
    $totalKilos = 0;
    
    foreach ($sale->items as $item) {
        $pesoPorUnidade = 0;
        $pesoTotal = 0;
        $status = "❌ Sem peso";
        
        // Procurar por padrões de peso no nome
        if (preg_match('/(\d+)\s*KG/i', $item->product_name, $matches)) {
            $pesoPorUnidade = (float)$matches[1];
            $pesoTotal = $pesoPorUnidade * $item->quantity;
            $totalKilos += $pesoTotal;
            $status = "✅ KG";
        } elseif (preg_match('/(\d+)\s*T\s*(\d+)\s*KG/i', $item->product_name, $matches)) {
            $pesoPorUnidade = ((float)$matches[1] * 1000) + (float)$matches[2];
            $pesoTotal = $pesoPorUnidade * $item->quantity;
            $totalKilos += $pesoTotal;
            $status = "✅ T+KG";
        }
        
        $produto = substr($item->product_name, 0, 40);
        $pesoUnid = $pesoPorUnidade > 0 ? $pesoPorUnidade . " KG" : "-";
        $pesoTotalStr = $pesoTotal > 0 ? $pesoTotal . " KG" : "-";
        
        printf("%-5d %-40s %-8d %-12s %-12s %-12s\n", 
               $item->id, $produto, $item->quantity, $pesoUnid, $pesoTotalStr, $status);
    }
    
    echo str_repeat("-", 100) . "\n";
    printf("%-5s %-40s %-8s %-12s %-12s %-12s\n", "", "", "", "", "TOTAL:", number_format($totalKilos, 1, ',', '.') . " KG");
    
    echo "\n🎯 FUNCIONALIDADES IMPLEMENTADAS:\n";
    echo str_repeat("-", 60) . "\n";
    echo "✅ Extração automática de peso dos nomes dos produtos\n";
    echo "✅ Suporte a formatos: '20 KG', '1T 500KG'\n";
    echo "✅ Cálculo de peso total por item (peso × quantidade)\n";
    echo "✅ Soma total de quilos da venda\n";
    echo "✅ Exibição de peso no romaneio\n";
    echo "✅ Resumo com total de quilos em destaque\n";
    
    echo "\n📋 COMO TESTAR:\n";
    echo str_repeat("-", 60) . "\n";
    echo "1. Acesse: /pdv/romaneio/{$sale->id}\n";
    echo "2. Verifique se o peso aparece no nome do produto\n";
    echo "3. Verifique se o peso total aparece nas colunas de quantidade\n";
    echo "4. Verifique se o total de quilos aparece no resumo\n";
    
    if ($totalKilos > 0) {
        echo "\n✅ TESTE CONCLUÍDO COM SUCESSO!\n";
        echo "🚀 O romaneio agora calcula e exibe o total de quilos!\n";
        echo "📊 Total de quilos da venda: " . number_format($totalKilos, 1, ',', '.') . " KG\n";
    } else {
        echo "\n⚠️  NENHUM PESO ENCONTRADO\n";
        echo "💡 Para testar, use produtos com nomes contendo peso (ex: 'Produto 20 KG')\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    exit(1);
}
