<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Criar uma venda teste com pagamento a prazo
$sale = App\Models\Sale::create([
    'cash_register_id' => 22,
    'user_id' => 12,
    'company_id' => 8,
    'customer_id' => 2,
    'total' => 50.00,
    'discount' => 0.00,
    'final_total' => 50.00,
    'status' => 'completed',
    'payment_mode' => 'installment',
    'sold_at' => now(),
]);

echo "Venda criada ID: " . $sale->id . "\n";

// Criar item da venda
App\Models\SaleItem::create([
    'sale_id' => $sale->id,
    'product_id' => 30,
    'product_name' => 'Produto Teste A Prazo',
    'quantity' => 1,
    'unit_price' => 50.00,
    'total_price' => 50.00,
]);

// Criar pagamento a prazo
App\Models\SalePayment::create([
    'sale_id' => $sale->id,
    'payment_type' => 'prazo',
    'amount' => 50.00,
]);

echo "Pagamento a prazo criado\n";

// Simular a geração do romaneio
$controller = new App\Http\Controllers\PDVController();
$reflector = new ReflectionClass($controller);
$method = $reflector->getMethod('generateDeliveryReceipt');
$method->setAccessible(true);

echo "Gerando romaneio...\n";
$method->invoke($controller, $sale);

// Verificar o romaneio criado
$romaneio = App\Models\DeliveryReceipt::where('sale_id', $sale->id)->first();
if ($romaneio) {
    echo "Romaneio criado com sucesso!\n";
    echo "ID: " . $romaneio->id . "\n";
    echo "Número: " . $romaneio->receipt_number . "\n";
    echo "Data: " . $romaneio->delivery_date->format('d/m/Y H:i') . "\n";
    echo "Status: " . $romaneio->status . "\n";
    echo "Status Pagamento: " . $romaneio->payment_status . "\n";
    
    echo "\nItens:\n";
    foreach ($romaneio->items as $item) {
        echo "- " . $item->product_name . "\n";
        echo "  Qtd: " . $item->quantity . " | Valor Unit: R$ " . number_format($item->unit_price, 2, ',', '.') . " | Total: R$ " . number_format($item->total_price, 2, ',', '.') . "\n";
    }
} else {
    echo "Erro: romaneio não foi criado!\n";
}
