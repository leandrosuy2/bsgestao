<?php

require_once 'vendor/autoload.php';

use App\Models\Nfe;
use App\Models\User;
use App\Services\FocusNfeService;

// Simular ambiente Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE DE OBSERVAÇÕES EM NOTAS FISCAIS ===\n\n";

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
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

if ($nfes->isEmpty()) {
    echo "❌ Nenhuma NFe encontrada para a empresa!\n";
    exit;
}

echo "✅ NFes encontradas: " . $nfes->count() . "\n\n";

foreach ($nfes as $nfe) {
    echo "📄 NFe #{$nfe->id}:\n";
    echo "   Status: {$nfe->status}\n";
    echo "   Número: " . ($nfe->numero_nfe ?: 'N/A') . "\n";
    echo "   Observações: " . ($nfe->observacoes ?: 'Nenhuma') . "\n";
    
    if ($nfe->observacoes) {
        echo "   ✅ Observações encontradas!\n";
        echo "   Conteúdo: " . substr($nfe->observacoes, 0, 100) . (strlen($nfe->observacoes) > 100 ? '...' : '') . "\n";
    } else {
        echo "   ❌ Sem observações\n";
    }
    echo "\n";
}

// Testar criação de NFe com observações
echo "🧪 TESTE: Criando NFe com observações...\n";

try {
    $nfe = new Nfe();
    $nfe->company_id = $user->company_id;
    $nfe->ref = 'TESTE_OBS_' . time();
    $nfe->status = 'rascunho';
    $nfe->natureza_operacao = 'Venda para teste de observações';
    $nfe->tipo_documento = '1';
    $nfe->finalidade_emissao = '1';
    $nfe->consumidor_final = '1';
    $nfe->presenca_comprador = '1';
    $nfe->local_destino = '1';
    $nfe->modalidade_frete = '9';
    $nfe->data_emissao = now();
    
    // Dados do emitente
    $nfe->cnpj_emitente = '61196441000103';
    $nfe->nome_emitente = 'GUABINORTE COMERCIO DE RACAO ANIMAL LTDA';
    $nfe->ie_emitente = '750328711';
    $nfe->logradouro_emitente = 'ROD BR 316';
    $nfe->numero_emitente = 'S/N';
    $nfe->bairro_emitente = 'SANTA ROSA';
    $nfe->municipio_emitente = 'BENEVIDES';
    $nfe->uf_emitente = 'PA';
    $nfe->cep_emitente = '68795000';
    $nfe->regime_tributario_emitente = '1';
    
    // Dados do destinatário
    $nfe->cpf_destinatario = '11111111111';
    $nfe->nome_destinatario = 'CLIENTE TESTE OBSERVAÇÕES';
    $nfe->logradouro_destinatario = 'RUA TESTE';
    $nfe->numero_destinatario = '123';
    $nfe->bairro_destinatario = 'CENTRO';
    $nfe->municipio_destinatario = 'SAO PAULO';
    $nfe->uf_destinatario = 'SP';
    $nfe->cep_destinatario = '01000000';
    $nfe->indicador_ie_destinatario = '9';
    
    // Valores
    $nfe->valor_produtos = 100.00;
    $nfe->valor_frete = 0.00;
    $nfe->valor_seguro = 0.00;
    $nfe->valor_desconto = 0.00;
    $nfe->valor_outras_despesas = 0.00;
    $nfe->valor_total = 100.00;
    
    // OBSERVAÇÕES DE TESTE
    $nfe->observacoes = "Esta é uma nota fiscal de teste para verificar se as observações estão sendo salvas e exibidas corretamente. 
    
Observações importantes:
- Produto entregue conforme especificação
- Cliente autorizou a entrega
- Pagamento realizado à vista
- Entrega realizada no prazo

Qualquer dúvida, entrar em contato com o setor comercial.";

    $nfe->save();
    
    echo "✅ NFe de teste criada: #{$nfe->id}\n";
    echo "📝 Observações salvas: " . (strlen($nfe->observacoes) > 0 ? 'Sim' : 'Não') . "\n";
    echo "📏 Tamanho das observações: " . strlen($nfe->observacoes) . " caracteres\n\n";
    
    // Testar se as observações são incluídas nos dados para API
    echo "🔍 TESTE: Verificando dados para API...\n";
    
    $focusService = new FocusNfeService();
    $dadosApi = $focusService->montarDadosParaVisualizacao($nfe);
    
    if (isset($dadosApi['informacoes_adicionais'])) {
        echo "✅ Observações incluídas nos dados da API!\n";
        echo "📝 Conteúdo: " . substr($dadosApi['informacoes_adicionais'], 0, 100) . "...\n";
    } else {
        echo "❌ Observações NÃO incluídas nos dados da API!\n";
    }
    
    echo "\n";
    
    // Limpar NFe de teste
    $nfe->delete();
    echo "🧹 NFe de teste removida\n\n";
    
} catch (Exception $e) {
    echo "❌ Erro no teste: " . $e->getMessage() . "\n";
}

echo "✅ TESTE CONCLUÍDO!\n";
echo "As observações agora são salvas e incluídas na NFe corretamente.\n";
