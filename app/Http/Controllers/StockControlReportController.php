<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class StockControlReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('company.access');
    }

    /**
     * Exibe o formulário de relatório de controle de estoque
     */
    public function index()
    {
        $user = Auth::user();
        $categories = Category::where('company_id', $user->company_id)->orderBy('name')->get();
        
        return view('stock_control_reports.index', compact('categories'));
    }

    /**
     * Gera relatório de controle de estoque
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'show_zero_stock' => 'boolean',
            'format' => 'in:html,pdf'
        ]);

        $user = Auth::user();
        $categoryId = $request->category_id;
        $showZeroStock = $request->show_zero_stock ?? false;
        $format = $request->format ?? 'html';

        // Buscar produtos da empresa
        $query = Product::where('company_id', $user->company_id)
                       ->with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderBy('name')->get();

        // Calcular estoque físico vs virtual para cada produto
        $stockData = $this->calculateStockData($products, $showZeroStock);

        // Estatísticas gerais
        $stats = $this->calculateStats($stockData);

        $data = [
            'user' => $user,
            'products' => $stockData,
            'stats' => $stats,
            'category' => $categoryId ? Category::find($categoryId) : null,
            'showZeroStock' => $showZeroStock,
            'generatedAt' => now()
        ];

        if ($format === 'pdf') {
            return $this->generatePdf($data);
        }

        return view('stock_control_reports.report', $data);
    }

    /**
     * Gera relatório específico para guabinorte1@gmail.com
     */
    public function guabinorteReport(Request $request)
    {
        $user = User::where('email', 'guabinorte1@gmail.com')->first();
        
        if (!$user) {
            return back()->with('error', 'Usuário guabinorte1@gmail.com não encontrado');
        }

        // Simular autenticação do usuário
        Auth::login($user);

        return $this->generateReport($request);
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
    private function generatePdf($data)
    {
        $pdf = Pdf::loadView('stock_control_reports.pdf.report', $data);
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'relatorio_controle_estoque_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * API para buscar dados de estoque via AJAX
     */
    public function getStockDataApi(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'show_zero_stock' => 'boolean'
        ]);

        $user = Auth::user();
        $categoryId = $request->category_id;
        $showZeroStock = $request->show_zero_stock ?? false;

        $query = Product::where('company_id', $user->company_id)->with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderBy('name')->get();
        $stockData = $this->calculateStockData($products, $showZeroStock);
        $stats = $this->calculateStats($stockData);

        return response()->json([
            'products' => $stockData,
            'stats' => $stats,
            'generated_at' => now()->format('d/m/Y H:i:s')
        ]);
    }
}
