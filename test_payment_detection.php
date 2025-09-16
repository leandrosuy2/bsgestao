<?php

// Simular uma requisição de venda a prazo para teste
$testData = [
    'itens' => [
        [
            'id' => '1',
            'nome' => 'Produto Teste A Prazo',
            'qtd' => 2,
            'unitario' => 25.00
        ]
    ],
    'pagamentos' => [
        [
            'tipo' => 'prazo',
            'valor' => 50.00
        ]
    ],
    'customer_id' => null,
    'desconto' => 0,
    'modo_pagamento' => 'installment',
    'data_vencimento' => '2025-07-30',
    'observacoes_prazo' => 'Pagamento teste a prazo'
];

echo "=== DADOS DE TESTE PARA VENDA A PRAZO ===\n";
echo "Pagamentos:\n";
foreach ($testData['pagamentos'] as $pag) {
    echo "- Tipo: " . $pag['tipo'] . " | Valor: R$ " . number_format($pag['valor'], 2, ',', '.') . "\n";
}
echo "Modo Pagamento: " . $testData['modo_pagamento'] . "\n";

// Verificar se a detecção de pagamento a prazo funcionará
$hasPrazo = false;
foreach ($testData['pagamentos'] as $payment) {
    if ($payment['tipo'] === 'prazo') {
        $hasPrazo = true;
        break;
    }
}

$expectedPaymentMode = $hasPrazo ? 'installment' : 'cash';
echo "Detecção de pagamento a prazo: " . ($hasPrazo ? 'SIM' : 'NÃO') . "\n";
echo "Modo de pagamento esperado: " . $expectedPaymentMode . "\n";

// Simular a lógica de status de pagamento
$paymentStatus = 'paid';
if ($expectedPaymentMode === 'installment' || $hasPrazo) {
    $paymentStatus = 'installment';
}

echo "Status de pagamento do romaneio: " . $paymentStatus . "\n";
echo "\nResultado esperado no romaneio: " . ($paymentStatus === 'installment' ? '💳 A Prazo' : '✓ Pago') . "\n";
