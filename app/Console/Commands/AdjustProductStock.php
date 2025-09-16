<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class AdjustProductStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:adjust {--list : Listar produtos com estoque baixo} {--zero : Zerar estoques críticos} {--help-setup : Mostrar ajuda para configuração}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gerenciar estoques de produtos - listar, ajustar e corrigir';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('help-setup')) {
            $this->showHelp();
            return;
        }

        if ($this->option('list')) {
            $this->listLowStockProducts();
            return;
        }

        if ($this->option('zero')) {
            $this->zeroLowStockProducts();
            return;
        }

        $this->showDefaultMenu();
    }

    private function showHelp()
    {
        $this->info('=== SISTEMA DE CONTROLE DE ESTOQUE ===');
        $this->line('');
        $this->info('Para configurar estoques corretamente:');
        $this->line('1. Use: php artisan stock:adjust --list (para ver produtos com estoque baixo)');
        $this->line('2. Configure estoques através do painel web em /products');
        $this->line('3. Defina sempre o "Estoque Atual" (quantidade real) e "Estoque Mínimo" (alerta)');
        $this->line('');
        $this->info('Comandos disponíveis:');
        $this->line('--list     : Lista produtos com estoque baixo');
        $this->line('--zero     : Zera estoques problemáticos (use com cuidado)');
        $this->line('--help-setup : Mostra esta ajuda');
    }

    private function listLowStockProducts()
    {
        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'min_stock')
                                  ->orWhere('stock_quantity', 0)
                                  ->get();

        if ($lowStockProducts->isEmpty()) {
            $this->info('✅ Todos os produtos têm estoque adequado!');
            return;
        }

        $this->warn('⚠️  Produtos com estoque baixo ou zerado:');
        $this->line('');

        $headers = ['ID', 'Nome', 'Estoque Atual', 'Estoque Mínimo', 'Status'];
        $rows = [];

        foreach ($lowStockProducts as $product) {
            $status = $product->stock_quantity == 0 ? 'ZERADO' : 
                     ($product->stock_quantity <= $product->min_stock ? 'BAIXO' : 'OK');
            
            $rows[] = [
                $product->id,
                substr($product->name, 0, 40),
                $product->stock_quantity . ' ' . $product->unit,
                $product->min_stock . ' ' . $product->unit,
                $status
            ];
        }

        $this->table($headers, $rows);
        
        $this->line('');
        $this->info('Para editar: acesse http://localhost:8000/products/{id}/edit');
    }

    private function zeroLowStockProducts()
    {
        $this->warn('⚠️  Esta operação zerará estoques problemáticos!');
        
        if (!$this->confirm('Tem certeza que deseja continuar?')) {
            $this->info('Operação cancelada.');
            return;
        }

        $affected = Product::where('stock_quantity', 5)
                          ->where('min_stock', 5)
                          ->update([
                              'stock_quantity' => 0,
                              'min_stock' => 1
                          ]);

        $this->info("✅ {$affected} produtos foram ajustados.");
        $this->line('Configure os estoques corretos através do painel web.');
    }

    private function showDefaultMenu()
    {
        $this->info('=== GERENCIADOR DE ESTOQUE ===');
        $this->line('');
        $this->line('Use as opções:');
        $this->line('--list        : Ver produtos com estoque baixo');
        $this->line('--help-setup  : Ajuda completa do sistema');
        $this->line('');
        $this->info('Exemplo: php artisan stock:adjust --list');
    }
}
