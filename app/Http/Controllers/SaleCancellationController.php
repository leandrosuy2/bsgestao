<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\CashRegister;
use App\Models\CashMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaleCancellationController extends Controller
{
    /**
     * Listar vendas para cancelamento
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Buscar vendas da empresa do usuário
        $query = Sale::where('company_id', $user->company_id)
                    ->where('status', 'completed')
                    ->with(['items', 'customer', 'user', 'seller'])
                    ->orderBy('created_at', 'desc');
        
        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            
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
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $sales = $query->paginate(20);
        
        return view('sales.cancellation.index', compact('sales'));
    }
    
    /**
     * Mostrar detalhes da venda para cancelamento
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $sale = Sale::where('company_id', $user->company_id)
                   ->where('id', $id)
                   ->where('status', 'completed')
                   ->with(['items.product', 'customer', 'user', 'seller', 'payments'])
                   ->firstOrFail();
        
        return view('sales.cancellation.show', compact('sale'));
    }
    
    /**
     * Cancelar venda
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'confirm' => 'required|accepted'
        ]);
        
        $user = Auth::user();
        
        $sale = Sale::where('company_id', $user->company_id)
                   ->where('id', $id)
                   ->where('status', 'completed')
                   ->with(['items.product', 'cashRegister'])
                   ->firstOrFail();
        
        try {
            DB::transaction(function () use ($sale, $request, $user) {
                // Reverter estoque dos produtos
                foreach ($sale->items as $item) {
                    if ($item->product_id) {
                        $product = Product::where('company_id', $user->company_id)
                                         ->find($item->product_id);
                        if ($product) {
                            $product->stock_quantity += $item->quantity;
                            $product->save();
                        }
                    }
                }
                
                // Criar movimentação de caixa negativa
                if ($sale->cashRegister) {
                    $sale->cashRegister->movements()->create([
                        'user_id' => $user->id,
                        'type' => 'out',
                        'amount' => $sale->final_total,
                        'description' => 'Cancelamento de venda #' . $sale->id . ' - Motivo: ' . $request->reason,
                    ]);
                }
                
                // Marcar venda como cancelada
                $sale->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancellation_reason' => $request->reason,
                    'cancelled_by' => $user->id
                ]);
                
                // Log da operação
                Log::info('Venda cancelada pelo sistema', [
                    'sale_id' => $sale->id,
                    'user_id' => $user->id,
                    'reason' => $request->reason,
                    'total' => $sale->final_total
                ]);
            });
            
            return redirect()->route('sales.cancellation.index')
                           ->with('success', 'Venda #' . $sale->id . ' cancelada com sucesso!');
            
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar venda', [
                'sale_id' => $id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Erro ao cancelar venda: ' . $e->getMessage());
        }
    }
    
    /**
     * API para cancelar venda via AJAX
     */
    public function cancelAjax(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);
        
        $user = Auth::user();
        
        $sale = Sale::where('company_id', $user->company_id)
                   ->where('id', $id)
                   ->where('status', 'completed')
                   ->with(['items.product', 'cashRegister'])
                   ->firstOrFail();
        
        try {
            DB::transaction(function () use ($sale, $request, $user) {
                // Reverter estoque dos produtos
                foreach ($sale->items as $item) {
                    if ($item->product_id) {
                        $product = Product::where('company_id', $user->company_id)
                                         ->find($item->product_id);
                        if ($product) {
                            $product->stock_quantity += $item->quantity;
                            $product->save();
                        }
                    }
                }
                
                // Criar movimentação de caixa negativa
                if ($sale->cashRegister) {
                    $sale->cashRegister->movements()->create([
                        'user_id' => $user->id,
                        'type' => 'out',
                        'amount' => $sale->final_total,
                        'description' => 'Cancelamento de venda #' . $sale->id . ' - Motivo: ' . $request->reason,
                    ]);
                }
                
                // Marcar venda como cancelada
                $sale->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancellation_reason' => $request->reason,
                    'cancelled_by' => $user->id
                ]);
                
                // Log da operação
                Log::info('Venda cancelada via AJAX', [
                    'sale_id' => $sale->id,
                    'user_id' => $user->id,
                    'reason' => $request->reason,
                    'total' => $sale->final_total
                ]);
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Venda #' . $sale->id . ' cancelada com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar venda via AJAX', [
                'sale_id' => $id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cancelar venda: ' . $e->getMessage()
            ], 500);
        }
    }
}