<?php

require_once 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Buscar o último romaneio
$lastReceipt = \App\Models\DeliveryReceipt::latest()->first();

if ($lastReceipt) {
    echo "=== ÚLTIMO ROMANEIO DEBUG ===\n";
    echo "ID: " . $lastReceipt->id . "\n";
    echo "Receipt Number: " . $lastReceipt->receipt_number . "\n";
    echo "Payment Status (DB): '" . $lastReceipt->payment_status . "'\n";
    echo "Sale ID: " . $lastReceipt->sale_id . "\n";
    
    if ($lastReceipt->sale_id) {
        $sale = \App\Models\Sale::find($lastReceipt->sale_id);
        if ($sale) {
            echo "\n=== VENDA RELACIONADA ===\n";
            echo "Sale ID: " . $sale->id . "\n";
            echo "Payment Mode: '" . $sale->payment_mode . "'\n";
            echo "Status: " . $sale->status . "\n";
            
            echo "\n=== PAGAMENTOS DA VENDA ===\n";
            foreach ($sale->payments as $payment) {
                echo "- Tipo: '" . $payment->payment_type . "' | Valor: R$ " . number_format($payment->amount, 2, ',', '.') . "\n";
            }
        }
    }
} else {
    echo "Nenhum romaneio encontrado.\n";
}
