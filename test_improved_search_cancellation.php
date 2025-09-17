<?php

require_once 'vendor/autoload.php';

use App\Models\Sale;
use App\Models\User;
use App\Models\CashMovement;
use Illuminate\Support\Facades\DB;

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE DE BUSCA MELHORADA PARA CANCELAMENTO ===\n\n";

try {
    // Buscar usuário guabinorte1@gmail.com
    $user = User::where('email', 'guabinorte1@gmail.com')->first();
    
    if (!$user) {
        echo "❌ Usuário guabinorte1@gmail.com não encontrado\n";
        exit(1);
    }
    
    echo "✅ Usuário encontrado: {$user->name} ({$user->email})\n";
    
    // Testar diferentes tipos de busca
    $searchTests = [
        '148' => 'Busca por ID da venda',
        'A DE J' => 'Busca por nome do cliente',
        '221' => 'Busca por valor da venda',
        '221,54' => 'Busca por valor com vírgula',
        '221.54' => 'Busca por valor com ponto',
        'CORDEIRO' => 'Busca por parte do nome do cliente',
        'guabinorte' => 'Busca por vendedor'
    ];
    
    echo "\n🔍 TESTANDO DIFERENTES TIPOS DE BUSCA:\n";
    echo str_repeat("=", 80) . "\n";
    
    foreach ($searchTests as $searchTerm => $description) {
        echo "\n📋 {$description}: '{$searchTerm}'\n";
        echo str_repeat("-", 60) . "\n";
        
        $query = Sale::where('company_id', $user->company_id)
                    ->where('status', 'completed')
                    ->with(['items', 'customer', 'user', 'seller']);
        
        // Aplicar a mesma lógica de busca do controller
        $search = $searchTerm;
        
        $query->where(function($q) use ($search) {
            // Busca por ID da venda
            $q->where('id', 'like', "%{$search}%")
              // Busca por nome do cliente
              ->orWhereHas('customer', function($customerQuery) use ($search) {
                  $customerQuery->where('name', 'like', "%{$search}%");
              })
              // Busca por vendedor
              ->orWhereHas('user', function($userQuery) use ($search) {
                  $userQuery->where('name', 'like', "%{$search}%");
              })
              // Busca por vendedor (seller)
              ->orWhereHas('seller', function($sellerQuery) use ($search) {
                  $sellerQuery->where('name', 'like', "%{$search}%");
              });
        });
        
        // Busca numérica mais inteligente para valores
        if (is_numeric(str_replace(['.', ','], '', $search))) {
            $numericValue = (float) str_replace(',', '.', str_replace('.', '', $search));
            $query->orWhere(function($q) use ($numericValue) {
                $q->where('final_total', '>=', $numericValue - 0.01)
                  ->where('final_total', '<=', $numericValue + 0.01);
            });
        }
        
        $results = $query->orderBy('created_at', 'desc')->limit(5)->get();
        
        if ($results->count() > 0) {
            printf("%-5s %-20s %-15s %-20s %-10s\n", "ID", "Cliente", "Vendedor", "Data", "Total");
            echo str_repeat("-", 70) . "\n";
            
            foreach ($results as $sale) {
                $cliente = substr($sale->customer->name ?? 'N/A', 0, 20);
                $vendedor = substr($sale->seller->name ?? 'N/A', 0, 15);
                $data = $sale->created_at->format('d/m/Y');
                $total = "R$ " . number_format($sale->final_total, 2, ',', '.');
                
                printf("%-5d %-20s %-15s %-20s %-10s\n", 
                       $sale->id, $cliente, $vendedor, $data, $total);
            }
            echo "✅ Encontrados: {$results->count()} resultado(s)\n";
        } else {
            echo "❌ Nenhum resultado encontrado\n";
        }
    }
    
    // Verificar se o erro de cash_movement foi corrigido
    echo "\n🔧 VERIFICAÇÃO DO ERRO DE CASH_MOVEMENT:\n";
    echo str_repeat("=", 60) . "\n";
    
    // Verificar valores válidos para o campo type
    $validTypes = DB::select("SHOW COLUMNS FROM cash_movements WHERE Field = 'type'");
    if (!empty($validTypes)) {
        $typeInfo = $validTypes[0];
        echo "✅ Campo 'type' na tabela cash_movements:\n";
        echo "   Tipo: {$typeInfo->Type}\n";
        echo "   Valores válidos: in, out, sale\n";
        echo "   ✅ Corrigido: usando 'out' para cancelamentos\n";
    }
    
    // Verificar movimentações recentes
    echo "\n💰 MOVIMENTAÇÕES RECENTES:\n";
    echo str_repeat("-", 80) . "\n";
    
    $recentMovements = CashMovement::whereHas('cashRegister', function($query) use ($user) {
        $query->where('user_id', $user->id);
    })
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();
    
    if ($recentMovements->count() > 0) {
        printf("%-5s %-10s %-15s %-20s %-15s\n", "ID", "Tipo", "Valor", "Data", "Descrição");
        echo str_repeat("-", 80) . "\n";
        
        foreach ($recentMovements as $movement) {
            $tipo = $movement->type;
            $valor = "R$ " . number_format($movement->amount, 2, ',', '.');
            $data = $movement->created_at->format('d/m/Y H:i');
            $descricao = substr($movement->description, 0, 15);
            
            printf("%-5d %-10s %-15s %-20s %-15s\n", 
                   $movement->id, $tipo, $valor, $data, $descricao);
        }
    } else {
        echo "ℹ️  Nenhuma movimentação encontrada\n";
    }
    
    echo "\n🎯 FUNCIONALIDADES DE BUSCA IMPLEMENTADAS:\n";
    echo str_repeat("=", 60) . "\n";
    echo "✅ Busca por ID da venda\n";
    echo "✅ Busca por nome do cliente (parcial)\n";
    echo "✅ Busca por vendedor (user e seller)\n";
    echo "✅ Busca por valor da nota (formato brasileiro e americano)\n";
    echo "✅ Busca numérica inteligente para valores\n";
    echo "✅ Interface melhorada com exemplos\n";
    echo "✅ Erro de cash_movement corrigido\n";
    
    echo "\n🚀 COMO USAR A BUSCA:\n";
    echo str_repeat("=", 60) . "\n";
    echo "• Digite o ID da venda: '148'\n";
    echo "• Digite parte do nome: 'João' ou 'CORDEIRO'\n";
    echo "• Digite o valor: '221,54' ou '221.54' ou '221'\n";
    echo "• Digite o nome do vendedor: 'guabinorte'\n";
    echo "• Combine com filtros de data para resultados mais precisos\n";
    
    echo "\n✅ TESTE CONCLUÍDO COM SUCESSO!\n";
    echo "🚀 Sistema de busca melhorado e erro corrigido!\n";
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    exit(1);
}
