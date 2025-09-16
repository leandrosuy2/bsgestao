<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function estoqueAtual(Request $request)
    {
        $categoryId = $request->input('category_id');
        $categories = \App\Models\Category::orderBy('name')->get();
        $productsQuery = \App\Models\Product::with('category');
        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }
        $products = $productsQuery->get();
        $estoques = [];
        foreach ($products as $product) {
            $entradas = \App\Models\StockMovement::where('product_id', $product->id)->where('type', 'entrada')->sum('quantity');
            $saidas = \App\Models\StockMovement::where('product_id', $product->id)->where('type', 'saida')->sum('quantity');
            $saldo = $entradas - $saidas;
            $estoques[] = [
                'produto' => $product,
                'categoria' => $product->category,
                'saldo' => $saldo,
                'min_stock' => $product->min_stock,
            ];
        }
        return view('reports.estoque_atual', compact('categories', 'estoques', 'categoryId'));
    }

    public function historicoMovimentacoes(Request $request)
    {
        $products = \App\Models\Product::orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        $query = \App\Models\StockMovement::with(['product.category', 'user']);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('category_id')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('date_start')) {
            $query->where('date', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->where('date', '<=', $request->date_end);
        }
        $movements = $query->orderByDesc('date')->paginate(20);
        return view('reports.historico_movimentacoes', compact('products', 'categories', 'movements', 'request'));
    }

    public function alertaEstoque(Request $request)
    {
        $products = \App\Models\Product::with('category')->get();
        $produtosCriticos = [];
        foreach ($products as $product) {
            $entradas = \App\Models\StockMovement::where('product_id', $product->id)->where('type', 'entrada')->sum('quantity');
            $saidas = \App\Models\StockMovement::where('product_id', $product->id)->where('type', 'saida')->sum('quantity');
            $saldo = $entradas - $saidas;
            if ($saldo < $product->min_stock) {
                $produtosCriticos[] = [
                    'produto' => $product,
                    'categoria' => $product->category,
                    'saldo' => $saldo,
                    'min_stock' => $product->min_stock,
                ];
            }
        }
        return view('reports.alerta_estoque', compact('produtosCriticos'));
    }

    public function produtosMaisMovimentados(Request $request)
    {
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');
        $movementsQuery = \App\Models\StockMovement::query();
        if ($dateStart) {
            $movementsQuery->where('date', '>=', $dateStart);
        }
        if ($dateEnd) {
            $movementsQuery->where('date', '<=', $dateEnd);
        }
        $movements = $movementsQuery
            ->selectRaw('product_id, SUM(quantity) as total')
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->with('product.category')
            ->get();
        return view('reports.produtos_mais_movimentados', compact('movements', 'dateStart', 'dateEnd'));
    }

    public function index()
    {
        return view('reports.index');
    }
}
