<?php
require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Configurar conexão com o banco
$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'bsestoque',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

$capsule->bootEloquent();

// Verificar o último romaneio gerado
$lastDeliveryReceipt = $capsule->table('delivery_receipts')
    ->orderBy('id', 'desc')
    ->first();

if ($lastDeliveryReceipt) {
    echo "=== ÚLTIMO ROMANEIO ===\n";
    echo "ID: " . $lastDeliveryReceipt->id . "\n";
    echo "Número: " . $lastDeliveryReceipt->receipt_number . "\n";
    echo "Status Pagamento: " . $lastDeliveryReceipt->payment_status . "\n";
    echo "Data: " . $lastDeliveryReceipt->delivery_date . "\n";
    
    // Buscar a venda relacionada
    if ($lastDeliveryReceipt->sale_id) {
        echo "\n=== VENDA RELACIONADA ===\n";
        $sale = $capsule->table('sales')
            ->where('id', $lastDeliveryReceipt->sale_id)
            ->first();
        
        if ($sale) {
            echo "Sale ID: " . $sale->id . "\n";
            echo "Payment Mode: " . $sale->payment_mode . "\n";
            echo "Status: " . $sale->status . "\n";
            
            // Buscar pagamentos da venda
            echo "\n=== PAGAMENTOS DA VENDA ===\n";
            $payments = $capsule->table('sale_payments')
                ->where('sale_id', $sale->id)
                ->get();
            
            foreach ($payments as $payment) {
                echo "Tipo: " . $payment->payment_type . " - Valor: R$ " . number_format($payment->amount, 2, ',', '.') . "\n";
            }
        }
    }
} else {
    echo "Nenhum romaneio encontrado.\n";
}
?>
