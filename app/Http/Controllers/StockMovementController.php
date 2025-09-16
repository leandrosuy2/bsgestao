<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockMovementController extends Controller
{
    public function __construct()
    {
        $this->middleware('company.access');
    }

    public function index()
    {
        $user = Auth::user();
        $movements = StockMovement::whereHas('product', function($query) use ($user) {
                            $query->where('company_id', $user->company_id);
                        })
                        ->with(['product', 'user'])
                        ->orderByDesc('date')
                        ->paginate(15);
        return view('stock_movements.index', compact('movements'));
    }

    public function create()
    {
        $user = Auth::user();
        $products = Product::where('company_id', $user->company_id)->get();
        $users = User::where('company_id', $user->company_id)->get();
        return view('stock_movements.create', compact('products', 'users'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:entrada,saida',
            'movement_reason' => 'required|in:compra,devolucao,ajuste,venda,perda',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Verificar se o produto pertence à empresa do usuário
        $product = Product::where('company_id', $user->company_id)->findOrFail($validated['product_id']);

        StockMovement::create($validated);
        return redirect()->route('stock_movements.index')->with('success', 'Movimentação registrada com sucesso!');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $movement = StockMovement::whereHas('product', function($query) use ($user) {
                            $query->where('company_id', $user->company_id);
                        })->findOrFail($id);

        $movement->delete();
        return redirect()->route('stock_movements.index')->with('success', 'Movimentação removida com sucesso!');
    }
}
