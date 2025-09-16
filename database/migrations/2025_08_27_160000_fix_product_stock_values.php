<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ajustar produtos que ficaram com estoque padrão incorreto
        // Esta migração serve para corrigir produtos que não tiveram seus estoques
        // definidos corretamente na migração anterior
        
        echo "Verificando produtos com estoque que pode precisar de ajuste...\n";
        
        $produtosComEstoquePadrao = DB::table('products')
            ->where('stock_quantity', 5)
            ->where('min_stock', 5)
            ->get();
            
        if ($produtosComEstoquePadrao->count() > 0) {
            echo "Encontrados " . $produtosComEstoquePadrao->count() . " produtos com estoque padrão.\n";
            echo "Estes produtos terão seu estoque zerado para permitir configuração manual:\n";
            
            foreach ($produtosComEstoquePadrao as $produto) {
                echo "- " . $produto->name . " (ID: " . $produto->id . ")\n";
            }
            
            // Zerar o estoque atual destes produtos para forçar configuração manual
            DB::table('products')
                ->where('stock_quantity', 5)
                ->where('min_stock', 5)
                ->update([
                    'stock_quantity' => 0,
                    'min_stock' => 1
                ]);
                
            echo "Estoques ajustados. Configure manualmente através do sistema.\n";
        } else {
            echo "Nenhum produto necessita de ajuste.\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não há reversão necessária para esta correção
    }
};
