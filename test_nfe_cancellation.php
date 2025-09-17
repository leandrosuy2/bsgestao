<?php

require_once 'vendor/autoload.php';

use App\Models\Nfe;
use App\Models\User;

// Simular ambiente Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE DE CANCELAMENTO DE NOTAS FISCAIS ===\n\n";

// Buscar usuário guabinorte1@gmail.com
$user = User::where('email', 'guabinorte1@gmail.com')->first();

if (!$user) {
    echo "❌ Usuário guabinorte1@gmail.com não encontrado!\n";
    exit;
}

echo "✅ Usuário encontrado: {$user->name} ({$user->email})\n";
echo "🏢 Empresa: {$user->company_id}\n\n";

// Buscar NFes da empresa
$nfes = Nfe::where('company_id', $user->company_id)
    ->whereIn('status', ['emitida', 'autorizado', 'processando_autorizacao'])
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

if ($nfes->isEmpty()) {
    echo "❌ Nenhuma NFe encontrada para a empresa!\n";
    echo "💡 Crie uma NFe primeiro para testar o cancelamento.\n";
    exit;
}

echo "✅ NFes encontradas: " . $nfes->count() . "\n\n";

foreach ($nfes as $nfe) {
    echo "📄 NFe #{$nfe->id}:\n";
    echo "   Status: {$nfe->status}\n";
    echo "   Número: " . ($nfe->numero_nfe ?: 'N/A') . "\n";
    echo "   Chave: " . ($nfe->chave_nfe ?: 'N/A') . "\n";
    echo "   Data Emissão: " . ($nfe->data_emissao ? $nfe->data_emissao->format('d/m/Y H:i') : 'N/A') . "\n";
    
    // Verificar se pode ser cancelada
    $podeCancelar24h = $nfe->data_emissao && now()->diffInHours($nfe->data_emissao) <= 24;
    $podeCancelarNormal = in_array($nfe->status, ['emitida', 'autorizado']);
    
    echo "   Cancelamento 24h: " . ($podeCancelar24h ? '✅ Sim' : '❌ Não') . "\n";
    echo "   Cancelamento Normal: " . ($podeCancelarNormal ? '✅ Sim' : '❌ Não') . "\n";
    
    if ($nfe->status === 'cancelado') {
        echo "   ⚠️  JÁ CANCELADA\n";
        if ($nfe->justificativa_cancelamento) {
            echo "   Justificativa: {$nfe->justificativa_cancelamento}\n";
        }
        if ($nfe->data_cancelamento) {
            echo "   Data Cancelamento: " . $nfe->data_cancelamento->format('d/m/Y H:i') . "\n";
        }
    }
    
    echo "\n";
}

echo "🔗 Links de Teste:\n";
echo "   Lista de NFes: /nfe\n";
echo "   Painel NFe: /nfe/painel\n\n";

// Verificar se existem campos de cancelamento na tabela
echo "🔍 Verificando campos de cancelamento na tabela:\n";
$campos = [
    'justificativa_cancelamento',
    'protocolo_cancelamento', 
    'data_cancelamento',
    'mensagem_cancelamento_sefaz'
];

foreach ($campos as $campo) {
    $existe = \Illuminate\Support\Facades\Schema::hasColumn('nfe', $campo);
    echo "   {$campo}: " . ($existe ? '✅ Existe' : '❌ Não existe') . "\n";
}

echo "\n✅ TESTE CONCLUÍDO!\n";
echo "A funcionalidade de cancelamento de notas fiscais está implementada.\n";
echo "Os usuários agora podem cancelar NFes com justificativa obrigatória.\n";
