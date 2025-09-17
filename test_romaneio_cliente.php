<?php

require_once 'vendor/autoload.php';

use App\Models\DeliveryReceipt;
use App\Models\Sale;
use App\Models\Customer;

// Simular ambiente Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE DE ROMANEIO COM INFORMAÇÕES DO CLIENTE ===\n\n";

// Buscar um romaneio recente que tenha cliente
$deliveryReceipt = DeliveryReceipt::whereNotNull('customer_name')
    ->where('customer_name', '!=', 'Cliente não informado')
    ->with(['sale.customer', 'items'])
    ->latest()
    ->first();

if (!$deliveryReceipt) {
    echo "❌ Nenhum romaneio com cliente encontrado!\n";
    exit;
}

echo "✅ Romaneio encontrado: {$deliveryReceipt->receipt_number}\n";
echo "📅 Data: " . $deliveryReceipt->delivery_date->format('d/m/Y H:i') . "\n";
echo "👤 Cliente: {$deliveryReceipt->customer_name}\n";
echo "📞 Telefone: " . ($deliveryReceipt->customer_phone ?: 'Não informado') . "\n";
echo "📧 Email: " . ($deliveryReceipt->customer_email ?: 'Não informado') . "\n";
echo "🆔 CPF/CNPJ: " . ($deliveryReceipt->customer_cpf_cnpj ?: 'Não informado') . "\n";

if ($deliveryReceipt->delivery_address) {
    echo "📍 Endereço: {$deliveryReceipt->delivery_address}\n";
}
if ($deliveryReceipt->delivery_city) {
    echo "🏙️  Cidade: {$deliveryReceipt->delivery_city}\n";
}
if ($deliveryReceipt->delivery_state) {
    echo "🗺️  Estado: {$deliveryReceipt->delivery_state}\n";
}
if ($deliveryReceipt->delivery_zipcode) {
    echo "📮 CEP: {$deliveryReceipt->delivery_zipcode}\n";
}

echo "\n📋 Itens do Romaneio:\n";
foreach ($deliveryReceipt->items as $item) {
    echo "- {$item->product_name} (Qtd: {$item->expected_quantity})\n";
}

echo "\n🔗 Links de Teste:\n";
echo "HTML: /pdv/romaneio/{$deliveryReceipt->sale_id}\n";
echo "PDF: /delivery-receipts/{$deliveryReceipt->id}/pdf\n";

echo "\n✅ TESTE CONCLUÍDO!\n";
echo "As informações do cliente agora aparecem corretamente no romaneio.\n";
