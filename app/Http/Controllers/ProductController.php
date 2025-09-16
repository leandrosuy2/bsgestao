<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('company.access');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Product::where('company_id', $user->company_id)
                       ->with('category');

        // Implementar busca se houver termo de pesquisa
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('internal_code', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('category', function($categoryQuery) use ($searchTerm) {
                      $categoryQuery->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Filtro por categoria
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filtro por status de estoque
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->whereColumn('stock_quantity', '<=', 'min_stock')
                          ->where('stock_quantity', '>', 0);
                    break;
                case 'zero':
                    $query->where('stock_quantity', 0);
                    break;
                case 'good':
                    $query->whereColumn('stock_quantity', '>', 'min_stock');
                    break;
            }
        }

        $products = $query->orderBy('name')
                         ->paginate(10)
                         ->appends($request->query());
        
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $categories = Category::where('company_id', $user->company_id)->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'internal_code' => 'required|string|max:100|unique:products,internal_code',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string|max:20',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        $validated['company_id'] = $user->company_id;
        Product::create($validated);
        return redirect()->route('products.index')->with('success', 'Produto cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $user = Auth::user();

        // Verificar se o produto pertence à empresa do usuário
        if ($product->company_id !== $user->company_id && $user->role !== 'admin') {
            abort(403, 'Acesso negado. Você só pode editar produtos da sua empresa.');
        }

        $categories = Category::where('company_id', $user->company_id)->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $user = Auth::user();

        // Verificar se o produto pertence à empresa do usuário
        if ($product->company_id !== $user->company_id && $user->role !== 'admin') {
            abort(403, 'Acesso negado. Você só pode atualizar produtos da sua empresa.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'internal_code' => 'required|string|max:100|unique:products,internal_code,' . $product->id,
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string|max:20',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);
        $product->update($validated);
        return redirect()->route('products.index')->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $user = Auth::user();

        // Verificar se o produto pertence à empresa do usuário
        if ($product->company_id !== $user->company_id && $user->role !== 'admin') {
            abort(403, 'Acesso negado. Você só pode remover produtos da sua empresa.');
        }

        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produto removido com sucesso!');
    }
}
