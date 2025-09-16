<?php

require 'vendor/autoload.php';

use App\Services\FocusNfeService;
use App\Models\Nfe;

// Conectar ao banco
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE DE DOWNLOAD DANFE ===\n";

try {
    // Buscar a última NFe autorizada
    $nfe = Nfe::where('status', 'autorizado')->latest()->first();
    
    if (!$nfe) {
        echo "❌ Nenhuma NFe autorizada encontrada\n";
        
        // Buscar qualquer NFe
        $nfe = Nfe::latest()->first();
        if ($nfe) {
            echo "📄 Última NFe encontrada:\n";
            echo "   ID: {$nfe->id}\n";
            echo "   Status: {$nfe->status}\n";
            echo "   REF: {$nfe->ref}\n";
        } else {
            echo "❌ Nenhuma NFe encontrada no banco\n";
        }
        exit;
    }
    
    echo "✅ NFe encontrada:\n";
    echo "   ID: {$nfe->id}\n";
    echo "   Status: {$nfe->status}\n";
    echo "   REF: {$nfe->ref}\n";
    echo "   Chave: {$nfe->chave_nfe}\n";
    
    // Testar serviço
    echo "\n=== TESTANDO FOCUS NFE SERVICE ===\n";
    $service = new FocusNfeService();
    
    echo "🔄 Fazendo requisição para baixar DANFE...\n";
    $pdf = $service->baixarDanfe($nfe->ref);
    
    echo "✅ Download realizado com sucesso!\n";
    echo "   Tamanho do arquivo: " . strlen($pdf) . " bytes\n";
    
    // Verificar se é PDF válido
    $isPdf = substr($pdf, 0, 4) === '%PDF';
    echo "   É PDF válido: " . ($isPdf ? 'SIM' : 'NÃO') . "\n";
    
    if ($isPdf) {
        echo "✅ DANFE baixado com sucesso!\n";
    } else {
        echo "⚠️  Conteúdo recebido não é um PDF:\n";
        echo "   Primeiros 200 caracteres: " . substr($pdf, 0, 200) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== FIM DO TESTE ===\n";
