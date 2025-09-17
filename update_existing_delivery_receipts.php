<?php

require_once 'vendor/autoload.php';

use App\Models\DeliveryReceipt;
use App\Models\Sale;
use App\Models\Customer;

// Simular ambiente Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ATUALIZANDO ROMANEIOS EXISTENTES COM INFORMAÇÕES DO CLIENTE ===\n\n";

// Buscar todos os romaneios que têm sale_id mas não têm informações do cliente
$deliveryReceipts = DeliveryReceipt::whereNotNull('sale_id')
    ->where(function($query) {
        $query->whereNull('customer_name')
              ->orWhere('customer_name', 'Cliente não informado');
    })
    ->with('sale.customer')
    ->get();

echo "Romaneios encontrados para atualizar: " . $deliveryReceipts->count() . "\n\n";

$updated = 0;
$errors = 0;

foreach ($deliveryReceipts as $deliveryReceipt) {
    try {
        $sale = $deliveryReceipt->sale;
        if (!$sale) {
            echo "❌ Romaneio {$deliveryReceipt->receipt_number}: Venda não encontrada\n";
            $errors++;
            continue;
        }

        $customer = $sale->customer;
        
        // Preparar dados do cliente
        $customerName = 'Cliente não informado';
        $customerCpfCnpj = '';
        $customerPhone = '';
        $customerEmail = '';
        $deliveryAddress = '';
        $deliveryCity = '';
        $deliveryState = '';
        $deliveryZipcode = '';
        
        if ($customer) {
            $customerName = $customer->name;
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

        // Atualizar o romaneio
        $deliveryReceipt->update([
            'customer_id' => $sale->customer_id,
            'customer_name' => $customerName,
            'customer_cpf_cnpj' => $customerCpfCnpj,
            'customer_phone' => $customerPhone,
            'customer_email' => $customerEmail,
            'delivery_address' => $deliveryAddress,
            'delivery_city' => $deliveryCity,
            'delivery_state' => $deliveryState,
            'delivery_zipcode' => $deliveryZipcode,
        ]);

        echo "✅ Romaneio {$deliveryReceipt->receipt_number}: Atualizado com cliente '{$customerName}'\n";
        $updated++;

    } catch (Exception $e) {
        echo "❌ Erro ao atualizar romaneio {$deliveryReceipt->receipt_number}: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n=== RESUMO ===\n";
echo "Romaneios atualizados: {$updated}\n";
echo "Erros: {$errors}\n";
echo "Total processados: " . ($updated + $errors) . "\n";

if ($updated > 0) {
    echo "\n✅ Atualização concluída! Os romaneios agora exibem as informações do cliente corretamente.\n";
} else {
    echo "\n⚠️  Nenhum romaneio foi atualizado.\n";
}
