@extends('dashboard.layout')

@section('title', 'Detalhes da Nota Fiscal')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold">
                @if($nfe->numero_nfe)
                    NFe #{{ $nfe->numero_nfe }}
                @elseif($nfe->status === 'rascunho')
                    Rascunho de NFe #{{ $nfe->id }}
                @elseif($nfe->status === 'processando')
                    NFe #{{ $nfe->id }} - Processando...
                @else
                    NFe #{{ $nfe->id }}
                @endif
            </h1>
            @if($nfe->chave_nfe)
                <p class="text-sm text-gray-600 mt-1">Chave: {{ $nfe->chave_nfe }}</p>
            @endif
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('nfe.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                Voltar
            </a>
            
            @if($nfe->status === 'rascunho')
                <a href="{{ route('nfe.edit', $nfe) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Editar
                </a>
                <button onclick="emitirNfe({{ $nfe->id }})" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    Emitir NFe
                </button>
            @endif
            
            @if($nfe->status === 'erro')
                <a href="{{ route('nfe.edit', $nfe) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-edit"></i> Editar Dados
                </a>
                <button onclick="reenviarNfe({{ $nfe->id }})" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-redo"></i> Reenviar NFe
                </button>
            @endif
            
            @if(in_array($nfe->status, ['emitida', 'autorizado', 'processando_autorizacao']) && $nfe->status !== 'cancelado')
                @if(in_array($nfe->status, ['emitida', 'autorizado']))
                <a href="{{ route('nfe.danfe', $nfe) }}" target="_blank" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                    Baixar DANFE
                </a>
                <a href="{{ route('nfe.xml', $nfe) }}?download=1" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-download mr-1"></i> Download XML
                </a>
                <a href="{{ route('nfe.xml', $nfe) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-eye mr-1"></i> Visualizar XML
                </a>
                @endif
                
                @if($nfe->data_emissao && now()->diffInHours($nfe->data_emissao) <= 24)
                <button onclick="abrirModalCancelar24h({{ $nfe->id }})" class="bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-times mr-1"></i> Cancelar NFE (24h)
                </button>
                @endif
                
                <button onclick="abrirModalDevolucao({{ $nfe->id }})" class="bg-yellow-500 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-undo mr-1"></i> Devolução NFE
                </button>
            @endif

            @if(in_array($nfe->status, ['cancelado']))
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mt-4">
                    <p class="text-gray-600">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Esta NFe foi cancelada. Não é possível realizar mais ações.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Status da NFe -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold mb-2">Status da Nota Fiscal</h2>
                <div class="flex items-center space-x-4">
                    @switch($nfe->status)
                        @case('rascunho')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                Rascunho
                            </span>
                            @break
                        @case('processando_autorizacao')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Processando Autorização
                            </span>
                            @break
                        @case('autorizado')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                Autorizada
                            </span>
                            @break
                        @case('emitida')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                Emitida
                            </span>
                            @break
                        @case('erro_autorizacao')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                Erro na Autorização
                            </span>
                            @break
                        @case('cancelado')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                Cancelada
                            </span>
                            @break
                        @case('devolvida')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-orange-100 text-orange-800">
                                Devolvida
                            </span>
                            @break
                        @default
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ ucfirst($nfe->status) }}
                            </span>
                    @endswitch
                    
                    @if($nfe->chave_nfe)
                        <span class="text-sm text-gray-600">
                            Chave: {{ $nfe->chave_nfe }}
                        </span>
                    @endif
                    
                    @if($nfe->numero_nfe)
                        <span class="text-sm text-gray-600">
                            Número: {{ $nfe->numero_nfe }}
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="text-right">
                <p class="text-sm text-gray-600">Criada em</p>
                <p class="font-semibold">{{ $nfe->created_at->format('d/m/Y H:i') }}</p>
                @if($nfe->data_emissao)
                    <p class="text-sm text-gray-600 mt-2">Emitida em</p>
                    <p class="font-semibold">{{ $nfe->data_emissao->format('d/m/Y') }}</p>
                @endif
            </div>
        </div>
        
        @if($nfe->mensagem_sefaz)
            @if(in_array($nfe->status, ['autorizado', 'emitida']))
                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <h3 class="font-medium text-green-800 mb-2">Mensagem SEFAZ</h3>
                    <p class="text-green-700">{{ $nfe->mensagem_sefaz }}</p>
                </div>
            @else
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <h3 class="font-medium text-red-800 mb-2">Mensagem SEFAZ</h3>
                    <p class="text-red-700">{{ $nfe->mensagem_sefaz }}</p>
                </div>
            @endif
        @endif

        @if($nfe->status === 'devolvida' && $nfe->data_devolucao)
            <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                <h3 class="font-medium text-orange-800 mb-2">Informações da Devolução</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="font-medium text-orange-700">Data da Devolução:</p>
                        <p class="text-orange-600">{{ $nfe->data_devolucao->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($nfe->protocolo_devolucao)
                    <div>
                        <p class="font-medium text-orange-700">Protocolo:</p>
                        <p class="text-orange-600">{{ $nfe->protocolo_devolucao }}</p>
                    </div>
                    @endif
                    @if($nfe->justificativa_devolucao)
                    <div class="md:col-span-2">
                        <p class="font-medium text-orange-700">Justificativa:</p>
                        <p class="text-orange-600">{{ $nfe->justificativa_devolucao }}</p>
                    </div>
                    @endif
                    @if($nfe->mensagem_devolucao_sefaz)
                    <div class="md:col-span-2">
                        <p class="font-medium text-orange-700">Mensagem SEFAZ:</p>
                        <p class="text-orange-600">{{ $nfe->mensagem_devolucao_sefaz }}</p>
                    </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Dados do Emitente -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Emitente</h2>
            <div class="space-y-2">
                <p><span class="font-medium">Empresa:</span> {{ $nfe->company->name }}</p>
                <p><span class="font-medium">CNPJ:</span> {{ $nfe->company->cnpj ?? 'Não informado' }}</p>
                <!-- Adicionar mais dados da empresa conforme necessário -->
            </div>
        </div>

        <!-- Dados do Destinatário -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Destinatário</h2>
            <div class="space-y-2">
                <p><span class="font-medium">Nome:</span> {{ $nfe->nome_destinatario }}</p>
                <p><span class="font-medium">CNPJ/CPF:</span> {{ $nfe->cnpj_destinatario ?: $nfe->cpf_destinatario }}</p>
                @if($nfe->email_destinatario)
                    <p><span class="font-medium">E-mail:</span> {{ $nfe->email_destinatario }}</p>
                @endif
                @if($nfe->telefone_destinatario)
                    <p><span class="font-medium">Telefone:</span> {{ $nfe->telefone_destinatario }}</p>
                @endif
                <div class="mt-3">
                    <p class="font-medium">Endereço:</p>
                    <p class="text-sm text-gray-600">
                        {{ $nfe->logradouro_destinatario }}@if($nfe->numero_destinatario), {{ $nfe->numero_destinatario }}@endif
                        <br>
                        @if($nfe->bairro_destinatario){{ $nfe->bairro_destinatario }} - @endif{{ $nfe->municipio_destinatario }}/{{ $nfe->uf_destinatario }}<br>
                        CEP: {{ $nfe->cep_destinatario }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Dados da Nota -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-xl font-semibold mb-4">Dados da Nota</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p><span class="font-medium">Natureza da Operação:</span></p>
                <p class="text-gray-600">{{ $nfe->natureza_operacao }}</p>
            </div>
            <div>
                <p><span class="font-medium">Série:</span></p>
                <p class="text-gray-600">{{ $nfe->serie_nfe }}</p>
            </div>
            @if($nfe->data_emissao)
                <div>
                    <p><span class="font-medium">Data de Emissão:</span></p>
                    <p class="text-gray-600">{{ $nfe->data_emissao->format('d/m/Y') }}</p>
                </div>
            @endif
        </div>
        
        @if($nfe->observacoes)
            <div class="mt-4">
                <p class="font-medium">Observações:</p>
                <p class="text-gray-600 mt-1">{{ $nfe->observacoes }}</p>
            </div>
        @endif
    </div>

    <!-- Itens da Nota -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-xl font-semibold mb-4">Itens da Nota</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NCM</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">CFOP</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qtd</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Unid</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Vlr Unit</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Vlr Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($nfe->items as $index => $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-4 py-4 text-sm text-gray-900">
                                <div>
                                    <p class="font-medium">{{ $item->descricao }}</p>
                                    @if($item->codigo_produto)
                                        <p class="text-xs text-gray-500">Código: {{ $item->codigo_produto }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900">{{ $item->codigo_ncm }}</td>
                            <td class="px-4 py-4 text-sm text-gray-900">{{ $item->cfop }}</td>
                            <td class="px-4 py-4 text-sm text-gray-900 text-center">{{ number_format($item->quantidade_comercial, 2, ',', '.') }}</td>
                            <td class="px-4 py-4 text-sm text-gray-900 text-center">{{ $item->unidade_comercial }}</td>
                            <td class="px-4 py-4 text-sm text-gray-900 text-right">R$ {{ number_format($item->valor_unitario_comercial, 2, ',', '.') }}</td>
                            <td class="px-4 py-4 text-sm text-gray-900 text-right font-medium">R$ {{ number_format($item->valor_total_item, 2, ',', '.') }}</td>
                        </tr>
                        
                        <!-- Impostos do item -->
                        @if($item->icms_aliquota > 0 || $item->ipi_aliquota > 0 || $item->pis_aliquota > 0 || $item->cofins_aliquota > 0)
                            <tr class="bg-gray-50">
                                <td colspan="8" class="px-4 py-2">
                                    <div class="text-xs text-gray-600">
                                        <span class="font-medium">Impostos:</span>
                                        @if($item->icms_aliquota > 0)
                                            ICMS: {{ $item->icms_aliquota }}% (R$ {{ number_format($item->icms_valor, 2, ',', '.') }})
                                        @endif
                                        @if($item->ipi_aliquota > 0)
                                            | IPI: {{ $item->ipi_aliquota }}% (R$ {{ number_format($item->ipi_valor, 2, ',', '.') }})
                                        @endif
                                        @if($item->pis_aliquota > 0)
                                            | PIS: {{ $item->pis_aliquota }}% (R$ {{ number_format($item->pis_valor, 2, ',', '.') }})
                                        @endif
                                        @if($item->cofins_aliquota > 0)
                                            | COFINS: {{ $item->cofins_aliquota }}% (R$ {{ number_format($item->cofins_valor, 2, ',', '.') }})
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Totais -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-xl font-semibold mb-4">Totais</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Subtotal</p>
                <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($nfe->valor_produtos, 2, ',', '.') }}</p>
            </div>
            
            @if($nfe->valor_frete > 0)
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-600">Frete</p>
                    <p class="text-2xl font-bold text-blue-900">R$ {{ number_format($nfe->valor_frete, 2, ',', '.') }}</p>
                </div>
            @endif
            
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <p class="text-sm text-green-600">Total</p>
                <p class="text-3xl font-bold text-green-900">R$ {{ number_format($nfe->valor_total, 2, ',', '.') }}</p>
            </div>
        </div>
        
        @if($nfe->items->sum('icms_valor') > 0 || $nfe->items->sum('ipi_valor') > 0 || $nfe->items->sum('pis_valor') > 0 || $nfe->items->sum('cofins_valor') > 0)
            <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                <h3 class="font-medium text-yellow-800 mb-2">Resumo de Impostos</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    @if($nfe->items->sum('icms_valor') > 0)
                        <div>
                            <span class="font-medium">ICMS:</span>
                            <span class="ml-2">R$ {{ number_format($nfe->items->sum('icms_valor'), 2, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($nfe->items->sum('ipi_valor') > 0)
                        <div>
                            <span class="font-medium">IPI:</span>
                            <span class="ml-2">R$ {{ number_format($nfe->items->sum('ipi_valor'), 2, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($nfe->items->sum('pis_valor') > 0)
                        <div>
                            <span class="font-medium">PIS:</span>
                            <span class="ml-2">R$ {{ number_format($nfe->items->sum('pis_valor'), 2, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($nfe->items->sum('cofins_valor') > 0)
                        <div>
                            <span class="font-medium">COFINS:</span>
                            <span class="ml-2">R$ {{ number_format($nfe->items->sum('cofins_valor'), 2, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function emitirNfe(id) {
    if (confirm('Tem certeza que deseja emitir esta nota fiscal?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/nfe/${id}/emitir`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function reenviarNfe(id) {
    if (confirm('Tem certeza que deseja reenviar esta nota fiscal? Os dados atuais serão utilizados.')) {
        // Mostrar loading
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Reenviando...';
        button.disabled = true;
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/nfe/${id}/emitir`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<!-- Modal Cancelar 24h -->
<div id="modalCancelar24h" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 transition-opacity duration-300">
    <div class="relative min-h-screen flex items-center justify-center px-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full transform transition-transform duration-300 scale-95" id="modalCancelar24hContent">
            <div class="p-6">
                <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-100 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mt-4 text-center">Cancelar NFe (24h)</h3>
                <div class="mt-4 text-center">
                    <p class="text-gray-600">
                        Tem certeza de que deseja cancelar esta NFe? Esta ação não pode ser desfeita.
                    </p>
                    <p class="text-sm text-red-500 mt-3 bg-red-50 p-2 rounded">
                        ⚠️ Cancelamento disponível apenas dentro de 24h após a emissão.
                    </p>
                </div>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <button onclick="fecharModalCancelar24h()" class="px-6 py-2 bg-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-400 transition-colors">
                        Cancelar
                    </button>
                    <button onclick="confirmarCancelamento24h()" class="px-6 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 transition-colors">
                        Sim, Cancelar NFe
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Devolução -->
<div id="modalDevolucao" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 transition-opacity duration-300">
    <div class="relative min-h-screen flex items-center justify-center px-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full transform transition-transform duration-300 scale-95" id="modalDevolucaoContent">
            <div class="p-6">
                <div class="flex items-center justify-center w-16 h-16 mx-auto bg-yellow-100 rounded-full">
                    <i class="fas fa-undo text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mt-4 text-center">Devolução NFe</h3>
                <div class="mt-6 space-y-4">
                    <div>
                        <label for="motivoDevolucao" class="block text-sm font-medium text-gray-700 mb-2">
                            Motivo da Devolução *
                        </label>
                        <select id="motivoDevolucao" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                            <option value="">Selecione o motivo...</option>
                            <option value="Produto com defeito">Produto com defeito</option>
                            <option value="Produto em desacordo">Produto em desacordo</option>
                            <option value="Entrega em atraso">Entrega em atraso</option>
                            <option value="Arrependimento do cliente">Arrependimento do cliente</option>
                            <option value="Produto danificado no transporte">Produto danificado no transporte</option>
                            <option value="Cancelamento da compra">Cancelamento da compra</option>
                            <option value="Erro na emissão">Erro na emissão</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="justificativaDevolucao" class="block text-sm font-medium text-gray-700 mb-2">
                            Justificativa (opcional)
                        </label>
                        <textarea id="justificativaDevolucao" rows="3" placeholder="Descreva detalhes adicionais sobre a devolução..." 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 resize-none"></textarea>
                    </div>
                    
                    <div class="bg-yellow-50 p-3 rounded-md">
                        <p class="text-sm text-yellow-800">
                            💡 A devolução será processada e a NFe terá seu status alterado para "devolvida".
                        </p>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <button onclick="fecharModalDevolucao()" class="px-6 py-2 bg-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-400 transition-colors">
                        Cancelar
                    </button>
                    <button onclick="confirmarDevolucao()" class="px-6 py-2 bg-yellow-600 text-white font-medium rounded-md hover:bg-yellow-700 transition-colors" style="background-color: #d97706 !important;">
                        <i class="fas fa-check mr-2"></i>
                        Processar Devolução
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let nfeIdParaAcao = null;

// Funções Modal Cancelar 24h
function abrirModalCancelar24h(nfeId) {
    nfeIdParaAcao = nfeId;
    const modal = document.getElementById('modalCancelar24h');
    const content = document.getElementById('modalCancelar24hContent');
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
}

function fecharModalCancelar24h() {
    const modal = document.getElementById('modalCancelar24h');
    const content = document.getElementById('modalCancelar24hContent');
    
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
    
    nfeIdParaAcao = null;
}

function confirmarCancelamento24h() {
    if (nfeIdParaAcao) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/nfe/${nfeIdParaAcao}/cancelar-24h`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Funções Modal Devolução
function abrirModalDevolucao(nfeId) {
    nfeIdParaAcao = nfeId;
    const modal = document.getElementById('modalDevolucao');
    const content = document.getElementById('modalDevolucaoContent');
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
}

function fecharModalDevolucao() {
    const modal = document.getElementById('modalDevolucao');
    const content = document.getElementById('modalDevolucaoContent');
    
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
    
    document.getElementById('motivoDevolucao').value = '';
    document.getElementById('justificativaDevolucao').value = '';
    nfeIdParaAcao = null;
}

function confirmarDevolucao() {
    const motivo = document.getElementById('motivoDevolucao').value;
    const justificativa = document.getElementById('justificativaDevolucao').value;
    
    if (!motivo) {
        alert('Por favor, selecione o motivo da devolução.');
        return;
    }
    
    if (nfeIdParaAcao) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/nfe/${nfeIdParaAcao}/devolucao`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const motivoInput = document.createElement('input');
        motivoInput.type = 'hidden';
        motivoInput.name = 'motivo';
        motivoInput.value = motivo;
        form.appendChild(motivoInput);
        
        const justificativaInput = document.createElement('input');
        justificativaInput.type = 'hidden';
        justificativaInput.name = 'justificativa';
        justificativaInput.value = justificativa || 'Devolução solicitada pelo cliente';
        form.appendChild(justificativaInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Fechar modais clicando fora
window.onclick = function(event) {
    const modalCancelar = document.getElementById('modalCancelar24h');
    const modalDevolucao = document.getElementById('modalDevolucao');
    
    if (event.target === modalCancelar) {
        fecharModalCancelar24h();
    }
    if (event.target === modalDevolucao) {
        fecharModalDevolucao();
    }
}

// Função antiga removida
function cancelarNfe(id) {
    // Função removida - agora usa apenas os modais
}

function reenviarNfe(id) {
    if (confirm('Deseja reenviar esta NFe?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/nfe/${id}/emitir`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
