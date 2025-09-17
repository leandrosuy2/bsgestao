<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashMovement;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Seller;
use App\Models\DeliveryReceipt;
use App\Models\DeliveryReceiptItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PDVController extends Controller
{
    public function index(Request $request)
    {
        // Busca produtos da empresa do usuário para autocomplete e consulta de preço
        $products = Product::forCurrentCompany()->get();

        // Busca clientes da empresa do usuário
        $customers = Customer::forCurrentCompany()->where('active', true)->orderBy('name')->get();

        // Busca vendedores ativos da empresa
        $sellers = Seller::forCurrentCompany()->where('active', true)->orderBy('name')->get();

        // Busca vendas abertas do usuário (carrinho em andamento)
        $sale = Sale::where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->with(['items.product', 'payments', 'seller'])
            ->latest()->first();

        // Busca caixa aberto do usuário
        $register = CashRegister::where('user_id', Auth::id())->where('status', 'open')->latest()->first();

        return view('pdv.full', compact('products', 'customers', 'sellers', 'sale', 'register'));
    }

    public function startSale(Request $request)
    {
        $request->validate([
            'seller_id' => 'nullable|exists:sellers,id'
        ], [
            'seller_id.exists' => 'Vendedor selecionado não é válido'
        ]);

        $register = CashRegister::where('user_id', Auth::id())->where('status', 'open')->latest()->first();
        if (!$register) {
            return back()->with('error', 'Abra um caixa antes de iniciar uma venda.');
        }
        
        $sale = Sale::create([
            'cash_register_id' => $register->id,
            'user_id' => Auth::id(),
            'seller_id' => $request->seller_id,
            'total' => 0,
            'discount' => 0,
            'final_total' => 0,
            'status' => 'in_progress',
            'sold_at' => null,
        ]);
        return redirect()->route('pdv.full');
    }

    public function addItem(Request $request)
    {
        Log::info('PDV addItem chamado', [
            'user_id' => Auth::id(),
            'product_id' => $request->input('product_id'),
            'quantity' => $request->input('quantity'),
            'all' => $request->all(),
            'method' => $request->method(),
            'url' => $request->url(),
        ]);
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $sale = Sale::where('user_id', Auth::id())->where('status', 'in_progress')->latest()->firstOrFail();

        // Buscar produto da empresa do usuário
        $product = Product::forCurrentCompany()->findOrFail($request->product_id);

        $totalPrice = $product->sale_price * $request->quantity;
        $saleItem = SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => $request->quantity,
            'unit_price' => $product->sale_price,
            'total_price' => $totalPrice,
            'final_price' => $totalPrice, // Inicialmente igual ao total_price
        ]);
        
        $this->recalculateSaleTotals($sale);
        return redirect('/pdv/full');
    }

    public function removeItem($itemId)
    {
        // Buscar item de uma venda do usuário atual
        $item = SaleItem::whereHas('sale', function($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($itemId);

        $sale = $item->sale;
        $item->delete();
        $this->recalculateSaleTotals($sale);
        return redirect()->route('pdv.index');
    }

    /**
     * Aplicar desconto em um produto específico
     */
    public function applyItemDiscount(Request $request, $itemId)
    {
        $request->validate([
            'discount_value' => 'required|numeric|min:0',
            'discount_type' => 'required|in:amount,percentage',
        ]);

        $item = SaleItem::whereHas('sale', function($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($itemId);

        $sale = $item->sale;

        // Aplicar desconto no item
        $item->applyDiscount($request->discount_value, $request->discount_type);
        $item->save();

        // Recalcular totais da venda
        $this->recalculateSaleTotals($sale);

        return response()->json([
            'success' => true,
            'message' => 'Desconto aplicado com sucesso!',
            'item' => [
                'id' => $item->id,
                'discount_amount' => $item->discount_amount,
                'discount_percentage' => $item->discount_percentage,
                'final_price' => $item->final_price,
                'formatted_discount' => $item->formatted_discount,
            ]
        ]);
    }

    /**
     * Remover desconto de um produto específico
     */
    public function removeItemDiscount($itemId)
    {
        $item = SaleItem::whereHas('sale', function($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($itemId);

        $sale = $item->sale;

        // Remover desconto do item
        $item->removeDiscount();
        $item->save();

        // Recalcular totais da venda
        $this->recalculateSaleTotals($sale);

        return response()->json([
            'success' => true,
            'message' => 'Desconto removido com sucesso!',
            'item' => [
                'id' => $item->id,
                'final_price' => $item->final_price,
            ]
        ]);
    }

    /**
     * Recalcular totais da venda baseado nos itens
     */
    private function recalculateSaleTotals(Sale $sale)
    {
        // Recalcular total baseado nos preços finais dos itens
        $total = $sale->items()->sum('final_price');
        
        $sale->update([
            'total' => $total,
            'final_total' => $total - $sale->discount,
        ]);
    }

    public function applyDiscount(Request $request)
    {
        $request->validate([
            'discount' => 'required|numeric|min:0',
            'discount_type' => 'required|in:percentage,value', // Adicionando tipo de desconto
        ]);
        
        $sale = Sale::where('user_id', Auth::id())->where('status', 'in_progress')->latest()->firstOrFail();
        
        // Calcular desconto baseado no tipo
        if ($request->discount_type === 'percentage') {
            $discount = ($sale->total * $request->discount) / 100;
        } else {
            $discount = $request->discount;
        }
        
        // Validar se o desconto não é maior que o total
        if ($discount > $sale->total) {
            return back()->withErrors(['discount' => 'Desconto não pode ser maior que o total da venda.']);
        }
        
        $sale->discount = $discount;
        $sale->discount_type = $request->discount_type;
        $sale->final_total = $sale->total - $sale->discount;
        $sale->save();
        
        return redirect()->route('pdv.index');
    }

    public function addPayment(Request $request)
    {
        $request->validate([
            'payment_type' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);
        $sale = Sale::where('user_id', Auth::id())->where('status', 'in_progress')->latest()->firstOrFail();
        SalePayment::create([
            'sale_id' => $sale->id,
            'payment_type' => $request->payment_type,
            'amount' => $request->amount,
        ]);
        return redirect()->route('pdv.index');
    }

    public function finalizeWithInvoice(Request $request)
    {
        $sale = Sale::where('user_id', Auth::id())->where('status', 'in_progress')->with('items.product')->latest()->firstOrFail();
        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                // Buscar produto da empresa do usuário
                $product = Product::forCurrentCompany()->findOrFail($item->product_id);
                $product->stock_quantity -= $item->quantity; // Decrementar estoque atual
                $product->save();
            }
            $sale->status = 'completed';
            $sale->sold_at = now();
            $sale->save();
            $register = $sale->cashRegister;
            $register->movements()->create([
                'user_id' => $sale->user_id,
                'type' => 'sale',
                'amount' => $sale->final_total,
                'description' => 'Venda PDV (NF) #' . $sale->id,
            ]);
        });
        // Aqui você pode acionar a geração de nota fiscal eletrônica
        return redirect()->route('pdv.receipt', $sale->id);
    }

    public function finalizeWithoutInvoice(Request $request)
    {
        $sale = Sale::where('user_id', Auth::id())->where('status', 'in_progress')->with('items.product')->latest()->firstOrFail();
        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                // Buscar produto da empresa do usuário
                $product = Product::forCurrentCompany()->findOrFail($item->product_id);
                $product->stock_quantity -= $item->quantity; // Decrementar estoque atual
                $product->save();
            }
            $sale->status = 'completed';
            $sale->sold_at = now();
            $sale->save();
            $register = $sale->cashRegister;
            $register->movements()->create([
                'user_id' => $sale->user_id,
                'type' => 'sale',
                'amount' => $sale->final_total,
                'description' => 'Venda PDV (Sem NF) #' . $sale->id,
            ]);
        });
        return redirect()->route('pdv.receipt', $sale->id);
    }

    public function cancelSale(Request $request)
    {
        $sale = Sale::where('user_id', Auth::id())->where('status', 'in_progress')->latest()->first();
        if ($sale) {
            $sale->delete();
        }
        return redirect()->route('pdv.full');
    }

    public function receipt($saleId)
    {
        // Buscar venda do usuário atual
        $sale = Sale::where('user_id', Auth::id())
            ->with(['items.product', 'payments', 'user'])
            ->findOrFail($saleId);
        return view('pdv.receipt', compact('sale'));
    }

    public function history()
    {
        // Buscar vendas do usuário atual
        $sales = Sale::where('user_id', Auth::id())
            ->with('user')
            ->orderByDesc('sold_at')
            ->paginate(20);
        return view('pdv.history', compact('sales'));
    }

    public function priceLookup(Request $request)
    {
        $request->validate(['termo' => 'required|string|min:2']);
        $termo = $request->input('termo');

        // Buscar produtos da empresa do usuário
        $produtos = Product::forCurrentCompany()
            ->where(function($query) use ($termo) {
                $query->where('name', 'like', "%{$termo}%")
                      ->orWhere('internal_code', 'like', "%{$termo}%");
            })
            ->limit(15)
            ->get(['id', 'name', 'internal_code', 'sale_price']);

        return response()->json($produtos);
    }

    public function finalize(Request $request)
    {
        try {
            \Log::info('PDV Finalize - Dados recebidos:', $request->all());

            $request->validate([
                'itens' => 'required|array|min:1',
                'itens.*.id' => 'nullable|string', // Pode ser vazio para produtos avulsos
                'itens.*.nome' => 'required|string',
                'itens.*.qtd' => 'required|numeric|min:1',
                'itens.*.unitario' => 'required|numeric|min:0',
                'pagamentos' => 'required|array|min:1',
                'pagamentos.*.tipo' => 'required|string|in:dinheiro,pix,cartao,cartao_credito,cartao_debito,prazo',
                'pagamentos.*.valor' => 'required|numeric|min:0',
                'customer_id' => 'nullable|exists:customers,id',
                'desconto' => 'nullable|numeric|min:0',
                'modo_pagamento' => 'nullable|string|in:cash,installment',
                'data_vencimento' => 'nullable|date|after:today',
                'observacoes_prazo' => 'nullable|string|max:500',
            ]);

            // Buscar ou criar cash register ativo
            $register = CashRegister::where('user_id', Auth::id())->where('status', 'open')->latest()->first();
            if (!$register) {
                return response()->json([
                    'success' => false, 
                    'error' => 'Abra um caixa antes de finalizar uma venda.'
                ], 400);
            }

            // Verificar se há pagamento a prazo ou se o modo foi definido como installment
            $hasPrazo = collect($request->pagamentos)->contains('tipo', 'prazo');
            $modoPagamento = $request->modo_pagamento ?? ($hasPrazo ? 'installment' : 'cash');

            // Buscar venda em progresso ou criar uma nova
            $sale = Sale::where('user_id', Auth::id())
                ->where('status', 'in_progress')
                ->where('cash_register_id', $register->id)
                ->latest()
                ->first();
            
            if (!$sale) {
                // Criar uma nova venda se não existir
                $sale = Sale::create([
                    'cash_register_id' => $register->id,
                    'user_id' => Auth::id(),
                    'company_id' => Auth::user()->company_id,
                    'status' => 'in_progress',
                    'total' => 0,
                    'discount' => 0,
                    'final_total' => 0,
                ]);
            }

            DB::transaction(function () use ($request, $sale, $register, $modoPagamento) {
                // Limpar itens existentes
                $sale->items()->delete();

                // Adicionar novos itens
                foreach ($request->itens as $item) {
                    $productId = $item['id'] ?? null;
                    $quantity = $item['qtd'] ?? $item['quantity'] ?? 1;
                    $unitPrice = $item['unitario'] ?? 0;
                    $productName = $item['nome'] ?? '';
                    
                    // Se tem ID do produto, buscar o produto real
                    if ($productId && $productId !== '') {
                        $product = Product::forCurrentCompany()->find($productId);
                        if ($product) {
                            SaleItem::create([
                                'sale_id' => $sale->id,
                                'product_id' => $product->id,
                                'product_name' => $product->name, // Nome do produto do banco
                                'quantity' => $quantity,
                                'unit_price' => $product->sale_price,
                                'total_price' => $product->sale_price * $quantity,
                            ]);
                            
                            // Atualizar estoque do produto
                            $product->stock_quantity -= $quantity;
                            $product->save();
                        }
                    } else {
                        // Produto avulso - usar product_id = null mas garantir que tem nome
                        $finalProductName = !empty($productName) ? $productName : 'Produto avulso';
                        SaleItem::create([
                            'sale_id' => $sale->id,
                            'product_id' => null, // Usar null para produtos avulsos
                            'product_name' => $finalProductName,
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'total_price' => $unitPrice * $quantity,
                        ]);
                    }
                }

                // Calcular totais baseado nos preços finais dos itens
                $total = $sale->items()->sum('final_price');
                
                // Calcular desconto - ajustado para lidar com descontos em valor real
                $desconto = $request->desconto ?? 0;
                $descontoTipo = $request->discount_type ?? 'value';
                
                // Se for desconto em porcentagem, converter para valor
                if ($descontoTipo === 'percentage') {
                    $desconto = ($total * $desconto) / 100;
                }
                
                $finalTotal = $total - $desconto;

                // Definir data de vencimento para pagamento a prazo
                $dataVencimento = null;
                if ($modoPagamento === 'installment') {
                    $dataVencimento = $request->data_vencimento ?? now()->addDay()->format('Y-m-d');
                }

                // Verificar se tem vendedor e calcular comissão
                $seller_id = $sale->seller_id ?? $request->seller_id;
                $commission = 0;
                
                if ($seller_id) {
                    $seller = \App\Models\Seller::find($seller_id);
                    if ($seller) {
                        $commission = ($finalTotal * $seller->commission_rate) / 100;
                    }
                }

                $sale->update([
                    'customer_id' => $request->customer_id,
                    'seller_id' => $seller_id,
                    'commission_amount' => $commission,
                    'total' => $total,
                    'discount' => $desconto,
                    'discount_type' => $descontoTipo,
                    'final_total' => $finalTotal,
                    'status' => 'completed', // Finalizar a venda
                    'sold_at' => now(),
                    'payment_mode' => $modoPagamento,
                    'installment_due_date' => $dataVencimento,
                    // Salva observação mesmo em vendas normais
                    'installment_notes' => $request->observacoes_prazo ?? null,
                ]);

                // Limpar pagamentos existentes
                $sale->payments()->delete();

                // Adicionar novos pagamentos e movimentações de caixa
                foreach ($request->pagamentos as $pagamento) {
                    if ($pagamento['valor'] > 0) {
                        SalePayment::create([
                            'sale_id' => $sale->id,
                            'payment_type' => $pagamento['tipo'],
                            'amount' => $pagamento['valor'],
                        ]);

                        // Criar movimentação de caixa apenas para pagamentos imediatos
                        if ($pagamento['tipo'] !== 'prazo') {
                            CashMovement::create([
                                'cash_register_id' => $register->id,
                                'user_id' => Auth::id(),
                                'type' => 'in',
                                'amount' => $pagamento['valor'],
                                'description' => "Venda #{$sale->id} - " . ucfirst($pagamento['tipo']),
                            ]);
                        }
                    }
                }

                // Atualizar saldo final do caixa apenas com pagamentos imediatos
                $totalIn = CashMovement::where('cash_register_id', $register->id)
                    ->where('type', 'in')
                    ->sum('amount');
                    
                $totalOut = CashMovement::where('cash_register_id', $register->id)
                    ->where('type', 'out')
                    ->sum('amount');
                
                $novoSaldo = $register->initial_amount + $totalIn - $totalOut;
                
                $register->update([
                    'final_amount' => $novoSaldo
                ]);

                // GERAR ROMANEIO AUTOMÁTICO
                $this->generateDeliveryReceipt($sale);
            });

            // Integração Sicredi: disparar Job se o usuário tiver integração ativa
            $userIntegration = \App\Models\UserPaymentIntegration::where('user_id', Auth::id())->where('enabled', true)->first();
            if ($userIntegration) {
                \App\Jobs\ProcessSicrediPaymentJob::dispatch($sale, $userIntegration);
            }
            return response()->json([
                'success' => true, 
                'sale_id' => $sale->id,
                'message' => 'Venda finalizada com sucesso!',
                'cupom_url' => route('pdv.cupom', $sale->id),
                'romaneio_url' => route('pdv.romaneio', $sale->id),
                'payment_mode' => $sale->payment_mode,
                'installment_due_date' => $sale->installment_due_date,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'error' => 'Dados inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro ao finalizar venda: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false, 
                'error' => 'Erro interno do servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gerar cupom da venda
     */
    public function cupom($id)
    {
        $sale = Sale::with(['items', 'payments', 'user', 'cashRegister'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $html = view('pdv.cupom', compact('sale'))->render();
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="cupom-venda-' . $sale->id . '.html"');
    }

    /**
     * Gerar romaneio da venda
     */
    public function romaneio($id)
    {
        $sale = Sale::with(['items', 'customer', 'user'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Buscar o romaneio gerado automaticamente para esta venda
        $deliveryReceipt = DeliveryReceipt::with(['items'])
            ->where('company_id', $sale->company_id)
            ->where('notes', 'LIKE', '%venda PDV #' . $sale->id . '%')
            ->latest()
            ->first();

        if (!$deliveryReceipt) {
            return response()->json([
                'success' => false,
                'error' => 'Romaneio não encontrado para esta venda.'
            ], 404);
        }

        $html = view('pdv.romaneio', compact('sale', 'deliveryReceipt'))->render();
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="romaneio-venda-' . $sale->id . '.html"');
    }

    /**
     * Recalcula o saldo final do caixa baseado em todas as movimentações
     */
    public function recalculateCashRegister($registerId)
    {
        try {
            $register = CashRegister::findOrFail($registerId);
            
            $totalIn = CashMovement::where('cash_register_id', $registerId)
                ->where('type', 'in')
                ->sum('amount');
                
            $totalOut = CashMovement::where('cash_register_id', $registerId)
                ->where('type', 'out')
                ->sum('amount');
            
            $novoSaldo = $register->initial_amount + $totalIn - $totalOut;
            
            $register->update(['final_amount' => $novoSaldo]);
            
            return response()->json([
                'success' => true,
                'initial_amount' => $register->initial_amount,
                'total_in' => $totalIn,
                'total_out' => $totalOut,
                'final_amount' => $novoSaldo,
                'message' => 'Saldo recalculado com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gera romaneio automático para uma venda
     */
    private function generateDeliveryReceipt(Sale $sale)
    {
        try {
            // Buscar TODOS os itens da venda (com produtos cadastrados e produtos avulsos)
            $saleItems = $sale->items()->get();
            
            if ($saleItems->isEmpty()) {
                return; // Não gerar romaneio se não há itens
            }

            // Gerar número único do romaneio
            $receiptNumber = 'ROM-' . date('Ymd') . '-' . str_pad($sale->id, 4, '0', STR_PAD_LEFT);

            // Buscar informações completas do cliente se existir
            $customerName = 'Cliente não informado';
            $customerContact = '';
            $customerCpfCnpj = '';
            $customerPhone = '';
            $customerEmail = '';
            $deliveryAddress = '';
            $deliveryCity = '';
            $deliveryState = '';
            $deliveryZipcode = '';
            
            if ($sale->customer_id) {
                $customer = Customer::find($sale->customer_id);
                if ($customer) {
                    $customerName = $customer->name;
                    $customerContact = $customer->phone ?? $customer->email ?? '';
                    $customerCpfCnpj = $customer->cpf_cnpj ?? '';
                    $customerPhone = $customer->phone ?? '';
                    $customerEmail = $customer->email ?? '';
                    
                    // Montar endereço completo
                    $addressParts = [];
                    if ($customer->address) $addressParts[] = $customer->address;
                    if ($customer->number) $addressParts[] = $customer->number;
                    if ($customer->neighborhood) $addressParts[] = $customer->neighborhood;
                    $deliveryAddress = implode(', ', $addressParts);
                    
                    $deliveryCity = $customer->city ?? '';
                    $deliveryState = $customer->state ?? '';
                    $deliveryZipcode = $customer->zipcode ?? '';
                }
            }

            // Definir status de pagamento baseado no modo de pagamento da venda
            $paymentStatus = 'paid'; // padrão
            
            // Recarregar a venda com pagamentos para garantir que estão atualizados
            $sale->load('payments');
            
            // Verificar se tem pagamento a prazo nos pagamentos da venda
            $hasInstallmentPayment = false;
            if ($sale->payments && $sale->payments->count() > 0) {
                // Carregar informações do vendedor se necessário
            if (!$sale->relationLoaded('seller')) {
                $sale->load('seller');
            }
            
            foreach ($sale->payments as $payment) {
                    Log::info("Payment debug", [
                        'payment_type' => $payment->payment_type,
                        'amount' => $payment->amount
                    ]);
                    if ($payment->payment_type === 'prazo') {
                        $hasInstallmentPayment = true;
                        break;
                    }
                }
            }
            
            // Log para debug
            Log::info("Payment status debug", [
                'sale_id' => $sale->id,
                'payment_mode' => $sale->payment_mode,
                'hasInstallmentPayment' => $hasInstallmentPayment,
                'payments_count' => $sale->payments ? $sale->payments->count() : 0
            ]);
            
            // Definir status baseado no payment_mode da venda OU se tem pagamento prazo
            if ($sale->payment_mode === 'installment' || $hasInstallmentPayment) {
                $paymentStatus = 'installment';
            }
            
            // Log final do status
            Log::info("Final payment status", [
                'sale_id' => $sale->id,
                'payment_status' => $paymentStatus
            ]);

            // Criar o romaneio
            $deliveryReceipt = DeliveryReceipt::create([
                'company_id' => $sale->company_id,
                'user_id' => $sale->user_id,
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'customer_name' => $customerName,
                'customer_cpf_cnpj' => $customerCpfCnpj,
                'customer_phone' => $customerPhone,
                'customer_email' => $customerEmail,
                'delivery_address' => $deliveryAddress,
                'delivery_city' => $deliveryCity,
                'delivery_state' => $deliveryState,
                'delivery_zipcode' => $deliveryZipcode,
                'receipt_number' => $receiptNumber,
                'supplier_name' => $customerName,
                'supplier_cnpj' => $customerCpfCnpj,
                'supplier_contact' => $customerContact,
                'delivery_date' => now(), // Inclui data e hora atual
                'status' => 'pending',
                'payment_status' => $paymentStatus,
                'notes' => 'Romaneio gerado automaticamente para venda PDV #' . $sale->id,
            ]);

            // Adicionar TODOS os itens ao romaneio
            foreach ($saleItems as $saleItem) {
                // Garantir que sempre temos um nome de produto válido
                $productName = $saleItem->product_name;
                
                // Se o produto tem ID, buscar o nome atual do produto
                if ($saleItem->product_id) {
                    $product = Product::find($saleItem->product_id);
                    if ($product) {
                        $productName = $product->name;
                    }
                }
                
                // Se ainda não tem nome, usar fallback
                if (empty($productName)) {
                    $productName = 'Produto ID: ' . ($saleItem->product_id ?? 'Avulso');
                }

                DeliveryReceiptItem::create([
                    'delivery_receipt_id' => $deliveryReceipt->id,
                    'product_name' => $productName,
                    'expected_quantity' => $saleItem->quantity,
                    'received_quantity' => $saleItem->quantity, // Marcar como entregue automaticamente
                    'quantity' => $saleItem->quantity, // Para compatibilidade
                    'unit_price' => $saleItem->unit_price,
                    'total_price' => $saleItem->total_price,
                    'checked' => true, // Marcar como conferido automaticamente
                    'notes' => 'Item da venda PDV #' . $sale->id,
                ]);
            }

            // Recalcular totais do romaneio
            $totalItems = $deliveryReceipt->items()->count();
            $checkedItems = $deliveryReceipt->items()->where('checked', true)->count();
            $progressPercentage = $totalItems > 0 ? ($checkedItems / $totalItems) * 100 : 0;

            $deliveryReceipt->update([
                'total_items' => $totalItems,
                'checked_items' => $checkedItems,
                'progress_percentage' => $progressPercentage,
            ]);

            Log::info("Romaneio automático gerado", [
                'sale_id' => $sale->id,
                'receipt_id' => $deliveryReceipt->id,
                'receipt_number' => $receiptNumber,
                'customer_name' => $customerName,
                'total_items' => $totalItems
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao gerar romaneio automático: ' . $e->getMessage(), [
                'sale_id' => $sale->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
