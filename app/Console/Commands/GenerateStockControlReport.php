<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateStockControlReport extends Command
{
    protected $signature = 'stock:control-report {email} {--category_id=} {--show-zero-stock} {--format=pdf}';
    protected $description = 'Gera relatório de controle de estoque para um usuário específico';

    public function handle()
    {
        $email = $this->argument('email');
        $categoryId = $this->option('category_id');
        $showZeroStock = $this->option('show-zero-stock');
        $format = $this->option('format');

        $this->info("Gerando relatório de controle de estoque para: {$email}");
        $this->info("Categoria: " . ($categoryId ?: 'Todas'));
        $this->info("Incluir estoque zero: " . ($showZeroStock ? 'Sim' : 'Não'));
        $this->info("Formato: {$format}");

        // Buscar usuário
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuário não encontrado com o email: {$email}");
            return 1;
        }

        $this->info("Usuário encontrado: {$user->name}");

        // Buscar produtos da empresa
        $query = Product::where('company_id', $user->company_id)->with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderBy('name')->get();

        $this->info("Produtos encontrados: " . $products->count());

        // Calcular dados de estoque
        $stockData = $this->calculateStockData($products, $showZeroStock);
        $stats = $this->calculateStats($stockData);

        $this->info("Total de produtos analisados: " . $stats['total_products']);
        $this->info("Produtos com divergências: " . $stats['products_with_difference']);
        $this->info("Valor total do estoque: R$ " . number_format($stats['total_value'], 2, ',', '.'));

        if ($format === 'pdf') {
            $this->generatePdf($user, $stockData, $stats, $categoryId, $showZeroStock);
        } else {
            $this->displayReport($user, $stockData, $stats);
        }

        return 0;
    }

    /**
     * Calcula dados de estoque para os produtos
     */
    private function calculateStockData($products, $showZeroStock = false)
    {
        $stockData = [];

        foreach ($products as $product) {
            // Estoque virtual (campo stock_quantity da tabela products)
            $virtualStock = $product->stock_quantity ?? 0;

            // Estoque físico (calculado pelas movimentações)
            $entradas = StockMovement::where('product_id', $product->id)
                                   ->where('type', 'entrada')
                                   ->sum('quantity');

            $saidas = StockMovement::where('product_id', $product->id)
                                 ->where('type', 'saida')
                                 ->sum('quantity');

            $physicalStock = $entradas - $saidas;

            // Diferença entre estoque físico e virtual
            $difference = $physicalStock - $virtualStock;

            // Status do estoque
            $status = $this->getStockStatus($physicalStock, $product->min_stock);

            // Calcular valor do estoque
            $stockValue = $physicalStock * ($product->cost_price ?? 0);

            $productData = [
                'product' => $product,
                'category' => $product->category,
                'virtual_stock' => $virtualStock,
                'physical_stock' => $physicalStock,
                'difference' => $difference,
                'status' => $status,
                'min_stock' => $product->min_stock,
                'stock_value' => $stockValue,
                'cost_price' => $product->cost_price ?? 0,
                'sale_price' => $product->sale_price ?? 0,
                'entradas' => $entradas,
                'saidas' => $saidas
            ];

            // Filtrar produtos com estoque zero se necessário
            if (!$showZeroStock && $physicalStock == 0) {
                continue;
            }

            $stockData[] = $productData;
        }

        // Ordenar por diferença (maiores diferenças primeiro)
        usort($stockData, function($a, $b) {
            return abs($b['difference']) <=> abs($a['difference']);
        });

        return $stockData;
    }

    /**
     * Calcula estatísticas gerais do estoque
     */
    private function calculateStats($stockData)
    {
        $totalProducts = count($stockData);
        $totalPhysicalStock = array_sum(array_column($stockData, 'physical_stock'));
        $totalVirtualStock = array_sum(array_column($stockData, 'virtual_stock'));
        $totalDifference = $totalPhysicalStock - $totalVirtualStock;
        $totalValue = array_sum(array_column($stockData, 'stock_value'));

        $productsWithDifference = array_filter($stockData, function($item) {
            return $item['difference'] != 0;
        });

        $lowStockProducts = array_filter($stockData, function($item) {
            return $item['physical_stock'] <= $item['min_stock'] && $item['physical_stock'] > 0;
        });

        $zeroStockProducts = array_filter($stockData, function($item) {
            return $item['physical_stock'] == 0;
        });

        return [
            'total_products' => $totalProducts,
            'total_physical_stock' => $totalPhysicalStock,
            'total_virtual_stock' => $totalVirtualStock,
            'total_difference' => $totalDifference,
            'total_value' => $totalValue,
            'products_with_difference' => count($productsWithDifference),
            'low_stock_products' => count($lowStockProducts),
            'zero_stock_products' => count($zeroStockProducts),
            'accuracy_percentage' => $totalProducts > 0 ? (($totalProducts - count($productsWithDifference)) / $totalProducts) * 100 : 100
        ];
    }

    /**
     * Determina o status do estoque
     */
    private function getStockStatus($physicalStock, $minStock)
    {
        if ($physicalStock == 0) {
            return 'zero';
        } elseif ($physicalStock <= $minStock) {
            return 'low';
        } elseif ($physicalStock > $minStock * 2) {
            return 'high';
        } else {
            return 'normal';
        }
    }

    /**
     * Gera PDF do relatório
     */
    private function generatePdf($user, $stockData, $stats, $categoryId, $showZeroStock)
    {
        $category = $categoryId ? Category::find($categoryId) : null;
        
        $data = [
            'user' => $user,
            'products' => $stockData,
            'stats' => $stats,
            'category' => $category,
            'showZeroStock' => $showZeroStock,
            'generatedAt' => now()
        ];

        $pdf = Pdf::loadView('stock_control_reports.pdf.report', $data);
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'relatorio_controle_estoque_' . str_replace('@', '_', $user->email) . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        $filepath = storage_path('app/' . $filename);
        
        $pdf->save($filepath);
        
        $this->info("PDF gerado com sucesso: {$filepath}");
    }

    /**
     * Exibe relatório no terminal
     */
    private function displayReport($user, $stockData, $stats)
    {
        $this->info("\n=== RELATÓRIO DE CONTROLE DE ESTOQUE ===");
        $this->info("Usuário: {$user->name} ({$user->email})");
        $this->info("Empresa: " . ($user->company->name ?? 'N/A'));
        $this->info("Total de produtos: " . $stats['total_products']);
        $this->info("Estoque físico total: " . $stats['total_physical_stock']);
        $this->info("Estoque virtual total: " . $stats['total_virtual_stock']);
        $this->info("Diferença total: " . ($stats['total_difference'] >= 0 ? '+' : '') . $stats['total_difference']);
        $this->info("Valor total: R$ " . number_format($stats['total_value'], 2, ',', '.'));
        $this->info("Produtos com divergências: " . $stats['products_with_difference']);
        $this->info("Precisão: " . number_format($stats['accuracy_percentage'], 1) . "%");
        
        if ($stats['low_stock_products'] > 0) {
            $this->warn("Produtos com estoque baixo: " . $stats['low_stock_products']);
        }
        
        if ($stats['zero_stock_products'] > 0) {
            $this->warn("Produtos sem estoque: " . $stats['zero_stock_products']);
        }
        
        if (count($stockData) > 0) {
            $this->info("\n=== TOP 10 MAIORES DIVERGÊNCIAS ===");
            $topDivergences = array_slice($stockData, 0, 10);
            
            foreach ($topDivergences as $item) {
                if ($item['difference'] != 0) {
                    $statusLabels = [
                        'normal' => 'Normal',
                        'low' => 'Baixo',
                        'high' => 'Alto',
                        'zero' => 'Zero'
                    ];
                    
                    $categoryName = $item['category'] ? $item['category']->name : 'N/A';
                    $this->info("- {$item['product']->name} ({$categoryName})");
                    $this->info("  Físico: {$item['physical_stock']} | Virtual: {$item['virtual_stock']} | Diferença: " . ($item['difference'] >= 0 ? '+' : '') . $item['difference']);
                    $this->info("  Status: {$statusLabels[$item['status']]} | Valor: R$ " . number_format($item['stock_value'], 2, ',', '.'));
                    $this->info("");
                }
            }
        }
        
        $this->info("\n=== RECOMENDAÇÕES ===");
        if ($stats['products_with_difference'] > 0) {
            $this->warn("⚠️  Realizar inventário físico para corrigir as divergências encontradas");
        }
        if ($stats['low_stock_products'] > 0) {
            $this->warn("⚠️  Repor estoque de " . $stats['low_stock_products'] . " produtos com estoque baixo");
        }
        if ($stats['zero_stock_products'] > 0) {
            $this->info("ℹ️  Verificar " . $stats['zero_stock_products'] . " produtos sem estoque");
        }
    }
}
