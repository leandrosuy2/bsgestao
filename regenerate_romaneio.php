<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Sale;
use App\Models\DeliveryReceipt;
use App\Models\DeliveryReceiptItem;
use App\Models\Product;
use App\Models\Customer;

$sale = Sale::with('items')->find(57);

if (!$sale) {
    echo "Venda não encontrada\n";
    exit;
}

// Gerar número único do romaneio
$receiptNumber = 'ROM-' . date('Ymd') . '-' . str_pad($sale->id, 4, '0', STR_PAD_LEFT);

// Buscar informações do cliente se existir
$customerName = 'Cliente não informado';
$customerContact = '';
if ($sale->customer_id) {
    $customer = Customer::find($sale->customer_id);
    if ($customer) {
        $customerName = $customer->name;
        $customerContact = $customer->phone ?? $customer->email ?? '';
    }
}

// Definir status de pagamento baseado no modo de pagamento da venda
$paymentStatus = 'paid'; // padrão
if ($sale->payment_mode === 'installment') {
    $paymentStatus = 'installment';
}

echo "Criando romaneio para venda {$sale->id}...\n";

// Criar o romaneio
$deliveryReceipt = DeliveryReceipt::create([
    'company_id' => $sale->company_id,
    'user_id' => $sale->user_id,
    'sale_id' => $sale->id,
    'receipt_number' => $receiptNumber,
    'supplier_name' => $customerName,
    'supplier_cnpj' => '',
    'supplier_contact' => $customerContact,
    'delivery_date' => now(),
    'status' => 'pending',
    'payment_status' => $paymentStatus,
    'notes' => 'Romaneio gerado automaticamente para venda PDV #' . $sale->id,
]);

echo "Romaneio criado com ID {$deliveryReceipt->id}\n";

// Adicionar TODOS os itens ao romaneio
foreach ($sale->items as $saleItem) {
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

    echo "Adicionando item: {$productName}\n";
    echo "  - Quantidade: {$saleItem->quantity}\n";
    echo "  - Preço unitário: R$ {$saleItem->unit_price}\n";
    echo "  - Preço total: R$ {$saleItem->total_price}\n";

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

echo "\nRomaneio regenerado com sucesso!\n";
echo "- Total de itens: {$totalItems}\n";
echo "- Itens conferidos: {$checkedItems}\n";
echo "- Progresso: {$progressPercentage}%\n";
echo "- Status de pagamento: {$paymentStatus}\n";
