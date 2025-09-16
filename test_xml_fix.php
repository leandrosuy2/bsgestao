<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testando correção do XML NFe...\n\n";

// Criar uma NFe de teste
$nfe = new App\Models\Nfe();
$nfe->nome_destinatario = 'TESTE CPF';
$nfe->cpf_destinatario = '12345678901';
$nfe->cnpj_destinatario = null; // Garantir que está vazio
$nfe->valor_total = 100;

echo "Teste 1 - NFe com CPF:\n";
echo "CPF: " . ($nfe->cpf_destinatario ?: 'vazio') . "\n";
echo "CNPJ: " . ($nfe->cnpj_destinatario ?: 'vazio') . "\n\n";

// Simular o service
$service = app(\App\Services\FocusNfeService::class);

// Usar reflexão para chamar o método privado
$reflection = new ReflectionClass($service);
$method = $reflection->getMethod('montarDadosNfe');
$method->setAccessible(true);

try {
    $dados = $method->invoke($service, $nfe);
    
    echo "Resultado dos dados montados:\n";
    echo "cpf_destinatario: " . ($dados['cpf_destinatario'] ?? 'NÃO DEFINIDO') . "\n";
    echo "cnpj_destinatario: " . ($dados['cnpj_destinatario'] ?? 'NÃO DEFINIDO') . "\n";
    
    // Verificar se apenas um está definido
    $temCpf = isset($dados['cpf_destinatario']);
    $temCnpj = isset($dados['cnpj_destinatario']);
    
    if ($temCpf && !$temCnpj) {
        echo "✅ CORRETO: Apenas CPF definido\n";
    } elseif (!$temCpf && $temCnpj) {
        echo "✅ CORRETO: Apenas CNPJ definido\n";
    } elseif ($temCpf && $temCnpj) {
        echo "❌ ERRO: Ambos CPF e CNPJ definidos\n";
    } else {
        echo "❌ ERRO: Nem CPF nem CNPJ definidos\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
