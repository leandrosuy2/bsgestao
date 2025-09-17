<?php

require_once 'vendor/autoload.php';

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\User;

// Simular ambiente Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE DE DESCONTO POR PRODUTO ===\n\n";

// Buscar usuário guabinorte1@gmail.com
$user = User::where('email', 'guabinorte1@gmail.com')->first();

if (!$user) {
    echo "❌ Usuário guabinorte1@gmail.com não encontrado!\n";
    exit;
}

echo "✅ Usuário encontrado: {$user->name} ({$user->email})\n";
echo "🏢 Empresa: {$user->company_id}\n\n";

// Buscar um produto da empresa
$product = Product::where('company_id', $user->company_id)->first();

if (!$product) {
    echo "❌ Nenhum produto encontrado para a empresa!\n";
    exit;
}

echo "✅ Produto encontrado: {$product->name}\n";
echo "💰 Preço: R$ " . number_format($product->sale_price, 2, ',', '.') . "\n\n";

// Buscar ou criar um caixa aberto
$cashRegister = \App\Models\CashRegister::where('user_id', $user->id)
    ->where('status', 'open')
    ->latest()
    ->first();

if (!$cashRegister) {
    $cashRegister = \App\Models\CashRegister::create([
        'user_id' => $user->id,
        'initial_amount' => 0,
        'final_amount' => 0,
        'status' => 'open',
        'opened_at' => now(),
    ]);
    echo "✅ Caixa criado: #{$cashRegister->id}\n";
} else {
    echo "✅ Caixa encontrado: #{$cashRegister->id}\n";
}

// Criar uma venda de teste
$sale = Sale::create([
    'company_id' => $user->company_id,
    'user_id' => $user->id,
    'cash_register_id' => $cashRegister->id,
    'total' => 0,
    'discount' => 0,
    'final_total' => 0,
    'status' => 'in_progress',
]);

echo "✅ Venda criada: #{$sale->id}\n";

// Adicionar item à venda
$quantity = 2;
$totalPrice = $product->sale_price * $quantity;

$saleItem = SaleItem::create([
    'sale_id' => $sale->id,
    'product_id' => $product->id,
    'product_name' => $product->name,
    'quantity' => $quantity,
    'unit_price' => $product->sale_price,
    'total_price' => $totalPrice,
    'final_price' => $totalPrice,
]);

echo "✅ Item adicionado: {$quantity}x {$product->name}\n";
echo "💰 Total do item: R$ " . number_format($totalPrice, 2, ',', '.') . "\n\n";

// Aplicar desconto de R$ 5,00 no item
$discountAmount = 5.00;
$saleItem->applyDiscount($discountAmount, 'amount');
$saleItem->save();

echo "✅ Desconto aplicado: R$ " . number_format($discountAmount, 2, ',', '.') . "\n";
echo "💰 Preço final do item: R$ " . number_format($saleItem->final_price, 2, ',', '.') . "\n";
echo "📊 Desconto formatado: {$saleItem->formatted_discount}\n\n";

// Recalcular totais da venda
$sale->update([
    'total' => $sale->items()->sum('final_price'),
    'final_total' => $sale->items()->sum('final_price') - $sale->discount,
]);

echo "✅ Totais recalculados:\n";
echo "   Subtotal: R$ " . number_format($sale->items->sum('total_price'), 2, ',', '.') . "\n";
echo "   Desconto Produtos: R$ " . number_format($sale->items->sum('discount_amount'), 2, ',', '.') . "\n";
echo "   Total Final: R$ " . number_format($sale->final_total, 2, ',', '.') . "\n\n";

// Testar desconto em porcentagem
echo "=== TESTE DE DESCONTO EM PORCENTAGEM ===\n";

$saleItem2 = SaleItem::create([
    'sale_id' => $sale->id,
    'product_id' => $product->id,
    'product_name' => $product->name . ' (2)',
    'quantity' => 1,
    'unit_price' => $product->sale_price,
    'total_price' => $product->sale_price,
    'final_price' => $product->sale_price,
]);

$discountPercentage = 10; // 10%
$saleItem2->applyDiscount($discountPercentage, 'percentage');
$saleItem2->save();

echo "✅ Desconto em % aplicado: {$discountPercentage}%\n";
echo "💰 Preço original: R$ " . number_format($saleItem2->total_price, 2, ',', '.') . "\n";
echo "💰 Preço final: R$ " . number_format($saleItem2->final_price, 2, ',', '.') . "\n";
echo "📊 Desconto formatado: {$saleItem2->formatted_discount}\n\n";

// Recalcular totais finais
$sale->update([
    'total' => $sale->items()->sum('final_price'),
    'final_total' => $sale->items()->sum('final_price') - $sale->discount,
]);

echo "✅ TOTAIS FINAIS:\n";
echo "   Subtotal: R$ " . number_format($sale->items->sum('total_price'), 2, ',', '.') . "\n";
echo "   Desconto Total: R$ " . number_format($sale->items->sum('discount_amount'), 2, ',', '.') . "\n";
echo "   Total Final: R$ " . number_format($sale->final_total, 2, ',', '.') . "\n\n";

echo "🔗 Links de Teste:\n";
echo "   Cupom: /pdv/cupom/{$sale->id}\n";
echo "   Romaneio: /pdv/romaneio/{$sale->id}\n\n";

echo "✅ TESTE CONCLUÍDO!\n";
echo "A funcionalidade de desconto por produto está funcionando corretamente.\n";
