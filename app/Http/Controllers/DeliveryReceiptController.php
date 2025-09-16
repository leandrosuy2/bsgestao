<?php

namespace App\Http\Controllers;

use App\Models\DeliveryReceipt;
use App\Models\DeliveryReceiptItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DeliveryReceiptController extends Controller
{
    public function index()
    {
        $receipts = DeliveryReceipt::where('company_id', Auth::user()->company_id)
            ->with(['user', 'items'])
            ->orderBy('delivery_date', 'desc')
            ->paginate(20);

        return view('delivery_receipts.index', compact('receipts'));
    }

    public function create()
    {
        // Gerar próximo número de romaneio baseado no ID
        $lastId = DeliveryReceipt::where('company_id', Auth::user()->company_id)
            ->max('id') ?? 0;
        
        $nextNumber = 'ROM-' . str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);

        // Buscar produtos da empresa para o dropdown
        $products = \App\Models\Product::where('company_id', Auth::user()->company_id)
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('delivery_receipts.create_modern', compact('nextNumber', 'products'));
    }

    public function store(Request $request)
    {
        // Verificar se o usuário tem company_id válido
        $user = Auth::user();
        if (!$user || !$user->company_id) {
            return back()->with('error', 'Usuário não tem empresa associada. Entre em contato com o administrador.')->withInput();
        }

        // DEBUG: Ver todos os dados recebidos
        \Log::info('===== DADOS COMPLETOS RECEBIDOS =====');
        \Log::info('Request completo:', $request->all());
        \Log::info('Items especificamente:', $request->get('items', []));
        
        // Validar primeiro se tem items
        if (!$request->has('items') || empty($request->items)) {
            return back()->with('error', 'É necessário adicionar pelo menos um produto ao romaneio.')->withInput();
        }

        $request->validate([
            'receipt_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('delivery_receipts')->where(function ($query) use ($user) {
                    return $query->where('company_id', $user->company_id);
                })
            ],
            'delivery_date' => 'required|date',
            'supplier_cnpj' => 'required|string|max:18',
            'supplier_name' => 'required|string|max:255',
            'supplier_contact' => 'nullable|string|max:255',
            'supplier_phone' => 'nullable|string|max:20',
            'supplier_state' => 'nullable|string|max:2',
            'supplier_city' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.product_code' => 'nullable|string|max:100',
            'items.*.expected_quantity' => 'required|numeric|min:0.01',
            'items.*.received_quantity' => 'nullable|numeric|min:0',
            'items.*.checked' => 'nullable|boolean',
            'items.*.notes' => 'nullable|string'
        ]);

        try {
            $receiptId = null;
            
            DB::transaction(function () use ($request, $user, &$receiptId) {
                $receipt = DeliveryReceipt::create([
                    'company_id' => $user->company_id,
                    'user_id' => $user->id,
                    'receipt_number' => $request->receipt_number,
                    'delivery_date' => $request->delivery_date,
                    'supplier_cnpj' => $request->supplier_cnpj,
                    'supplier_name' => $request->supplier_name,
                    'supplier_contact' => $request->supplier_contact,
                    'notes' => $request->notes,
                    'status' => 'pending'
                ]);

                foreach ($request->items as $item) {
                    DeliveryReceiptItem::create([
                        'delivery_receipt_id' => $receipt->id,
                        'product_name' => $item['product_name'],
                        'product_code' => $item['product_code'] ?? null,
                        'expected_quantity' => (float) $item['expected_quantity'],
                        'received_quantity' => (float) ($item['received_quantity'] ?? 0),
                        'quantity' => (float) $item['expected_quantity'], // para compatibilidade
                        'checked' => isset($item['checked']) && $item['checked'] ? true : false,
                        'notes' => $item['notes'] ?? null
                    ]);
                }

                $receipt->updateProgress();

                // Armazenar ID do recibo para redirecionamento
                $receiptId = $receipt->id;
            });

            return redirect()->route('delivery_receipts.show', $receiptId)
                ->with('success', 'Romaneio criado com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao criar romaneio: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(DeliveryReceipt $deliveryReceipt)
    {
        // Verificar se o romaneio pertence à empresa do usuário
        if ($deliveryReceipt->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $deliveryReceipt->load(['user', 'items']);
        
        return view('delivery_receipts.show', compact('deliveryReceipt'));
    }

    public function edit(DeliveryReceipt $deliveryReceipt)
    {
        // Verificar se o romaneio pertence à empresa do usuário
        if ($deliveryReceipt->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $deliveryReceipt->load('items');
        
        return view('delivery_receipts.edit', compact('deliveryReceipt'));
    }

    public function update(Request $request, DeliveryReceipt $deliveryReceipt)
    {
        // Verificar se o romaneio pertence à empresa do usuário
        if ($deliveryReceipt->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $request->validate([
            'receipt_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('delivery_receipts')->where(function ($query) {
                    return $query->where('company_id', Auth::user()->company_id);
                })->ignore($deliveryReceipt->id)
            ],
            'delivery_date' => 'required|date',
            'supplier_cnpj' => 'nullable|string|max:20',
            'supplier_name' => 'nullable|string|max:255',
            'supplier_state' => 'nullable|string|max:2',
            'supplier_city' => 'nullable|string|max:100',
            'carrier' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,completed,cancelled',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.checked' => 'boolean'
        ]);

        try {
            DB::transaction(function () use ($request, $deliveryReceipt) {
                $deliveryReceipt->update([
                    'receipt_number' => $request->receipt_number,
                    'delivery_date' => $request->delivery_date,
                    'supplier_cnpj' => $request->supplier_cnpj,
                    'supplier_name' => $request->supplier_name,
                    'supplier_state' => $request->supplier_state,
                    'supplier_city' => $request->supplier_city,
                    'carrier' => $request->carrier,
                    'notes' => $request->notes,
                    'status' => $request->status
                ]);

                // Deletar itens existentes e recriar
                $deliveryReceipt->items()->delete();

                foreach ($request->items as $item) {
                    DeliveryReceiptItem::create([
                        'delivery_receipt_id' => $deliveryReceipt->id,
                        'product_name' => $item['product_name'],
                        'quantity' => $item['quantity'],
                        'checked' => $item['checked'] ?? false,
                        'notes' => $item['notes'] ?? null
                    ]);
                }
            });

            return redirect()->route('delivery_receipts.show', $deliveryReceipt)
                ->with('success', 'Romaneio atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao atualizar romaneio: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(DeliveryReceipt $deliveryReceipt)
    {
        // Verificar se o romaneio pertence à empresa do usuário
        if ($deliveryReceipt->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $deliveryReceipt->delete();

        return redirect()->route('delivery_receipts.index')
            ->with('success', 'Romaneio excluído com sucesso!');
    }

    public function updateStatus(Request $request, DeliveryReceipt $deliveryReceipt)
    {
        // Verificar se o romaneio pertence à empresa do usuário
        if ($deliveryReceipt->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,completed,cancelled'
        ]);

        $deliveryReceipt->update(['status' => $request->status]);

        $statusNames = [
            'pending' => 'pendente',
            'completed' => 'concluído',
            'cancelled' => 'cancelado'
        ];

        return redirect()->back()
            ->with('success', "Status alterado para {$statusNames[$request->status]} com sucesso!");
    }

    public function updateItemCheck(Request $request, DeliveryReceipt $deliveryReceipt, DeliveryReceiptItem $item)
    {
        // Verificar se o romaneio pertence à empresa do usuário
        if ($deliveryReceipt->company_id !== Auth::user()->company_id || $item->delivery_receipt_id !== $deliveryReceipt->id) {
            abort(403);
        }

        $request->validate([
            'checked' => 'required|boolean'
        ]);

        $item->update(['checked' => $request->checked]);

        return response()->json(['success' => true]);
    }

    /**
     * Toggle item checked status
     */
    public function toggleItem(DeliveryReceipt $deliveryReceipt, DeliveryReceiptItem $item)
    {
        try {
            // Verificar se o item pertence ao romaneio
            if ($item->delivery_receipt_id !== $deliveryReceipt->id) {
                return response()->json(['success' => false, 'message' => 'Item não encontrado'], 404);
            }

            // Verificar se o romaneio não está finalizado
            if ($deliveryReceipt->status === 'finalized') {
                return response()->json(['success' => false, 'message' => 'Romaneio já finalizado'], 400);
            }

            // Toggle do status
            $item->checked = !$item->checked;
            $item->save();

            // Atualizar progresso do romaneio
            $deliveryReceipt->updateProgress();

            return response()->json(['success' => true, 'checked' => $item->checked]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Finalize delivery receipt
     */
    public function finalize(DeliveryReceipt $deliveryReceipt)
    {
        try {
            // Verificar se já está finalizado
            if ($deliveryReceipt->status === 'finalized') {
                return response()->json(['success' => false, 'message' => 'Romaneio já finalizado'], 400);
            }

            // Finalizar romaneio
            $deliveryReceipt->status = 'finalized';
            $deliveryReceipt->finalized_by = auth()->id();
            $deliveryReceipt->finalized_at = now();
            $deliveryReceipt->save();

            return response()->json(['success' => true, 'message' => 'Romaneio finalizado com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro interno do servidor'], 500);
        }
    }

    private function generateNextReceiptNumber($lastNumber = null)
    {
        if (!$lastNumber) {
            return 'ROM' . date('Y') . '0001';
        }

        // Extrair número do último romaneio (ex: ROM20250001 -> 0001)
        $lastNumeric = (int) substr($lastNumber, -4);
        $nextNumeric = $lastNumeric + 1;

        return 'ROM' . date('Y') . str_pad($nextNumeric, 4, '0', STR_PAD_LEFT);
    }

    /**
     * API para buscar dados do CNPJ
     */
    public function searchCnpj(Request $request)
    {
        $cnpj = preg_replace('/[^0-9]/', '', $request->cnpj);
        
        if (strlen($cnpj) !== 14) {
            return response()->json(['error' => 'CNPJ inválido'], 400);
        }

        try {
            // Aqui você pode implementar a integração com API de CNPJ
            // Por enquanto, retornar dados fictícios
            return response()->json([
                'nome' => 'Empresa Exemplo LTDA',
                'uf' => 'SP',
                'municipio' => 'São Paulo'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro na consulta'], 500);
        }
    }

    /**
     * Gerar PDF do romaneio
     */
    public function generatePdf(DeliveryReceipt $deliveryReceipt)
    {
        // Verificar se o romaneio pertence à empresa do usuário
        if ($deliveryReceipt->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $deliveryReceipt->load(['user', 'items']);
        
        $pdf = \PDF::loadView('delivery_receipts.pdf', compact('deliveryReceipt'));
        
        return $pdf->download('Romaneio_' . $deliveryReceipt->receipt_number . '.pdf');
    }

    /**
     * Buscar fornecedores já cadastrados
     */
    public function searchSuppliers(Request $request)
    {
        $search = $request->get('search', '');
        
        // Remover formatação do CNPJ para busca mais eficiente
        $cleanSearch = preg_replace('/[^0-9]/', '', $search);
        
        $suppliers = \App\Models\Supplier::where('company_id', Auth::user()->company_id)
            ->where(function($query) use ($search, $cleanSearch) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('cnpj', 'like', '%' . $search . '%')
                      ->orWhere('cnpj', 'like', '%' . $cleanSearch . '%');
            })
            ->where('status', 'ativo')
            ->orderBy('name')
            ->limit(10)
            ->get();

        // Log para debug
        \Log::info('Busca de fornecedores na tabela suppliers:', [
            'search' => $search,
            'cleanSearch' => $cleanSearch,
            'results_count' => $suppliers->count(),
            'results' => $suppliers->toArray()
        ]);

        return response()->json($suppliers->map(function($supplier) {
            return [
                'name' => $supplier->name,
                'cnpj' => $supplier->cnpj,
                'contact' => $supplier->contact_name,
                'state' => $supplier->state,
                'city' => $supplier->city,
                'phone' => $supplier->contact_phone,
                'email' => $supplier->contact_email,
            ];
        }));
    }

    /**
     * Buscar produtos da empresa via API
     */
    public function searchProducts(Request $request)
    {
        $search = $request->get('search', '');
        
        $products = \App\Models\Product::where('company_id', Auth::user()->company_id)
            ->where(function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('internal_code', 'like', '%' . $search . '%');
            })
            ->with('category')
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json($products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'internal_code' => $product->internal_code,
                'category' => $product->category ? $product->category->name : null,
                'unit' => $product->unit,
                'cost_price' => $product->cost_price,
                'sale_price' => $product->sale_price,
            ];
        }));
    }
}
