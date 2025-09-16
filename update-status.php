<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Nfe;

echo "=== ATUALIZANDO STATUS DA NFE ===\n";

try {
    $nfe = Nfe::find(43);
    
    if ($nfe) {
        echo "NFe encontrada:\n";
        echo "   ID: {$nfe->id}\n";
        echo "   Status atual: {$nfe->status}\n";
        echo "   REF: {$nfe->ref}\n";
        
        // Atualizar status
        $nfe->status = 'autorizado';
        $nfe->save();
        
        echo "✅ Status atualizado para: autorizado\n";
        
        // Verificar se salvou
        $nfe->refresh();
        echo "   Status confirmado: {$nfe->status}\n";
        
    } else {
        echo "❌ NFe com ID 43 não encontrada\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n=== FIM ===\n";
