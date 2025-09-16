@extends('dashboard.layout')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <span class="bg-blue-600 p-2 rounded-full shadow">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
            </svg>
        </span>
        <h1 class="text-2xl font-bold text-gray-800">Pesquisar Boletos</h1>
    </div>
    <a href="{{ route('venda-boleto') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl transition font-semibold shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Gerar Novo Boleto
    </a>
</div>

<!-- Filtros de pesquisa -->
<div class="bg-white rounded-xl shadow-sm mb-6">
    <div class="p-6">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Filtros de Pesquisa</h3>
        <form method="GET" action="{{ route('pesquisar-boletos') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                <input type="text" name="cliente" value="{{ request('cliente') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm" 
                       placeholder="Nome do cliente">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Documento</label>
                <input type="text" name="documento" value="{{ request('documento') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm" 
                       placeholder="CPF/CNPJ">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm">
                    <option value="">Todos</option>
                    <option value="gerado" {{ request('status') === 'gerado' ? 'selected' : '' }}>Gerado</option>
                    <option value="pago" {{ request('status') === 'pago' ? 'selected' : '' }}>Pago</option>
                    <option value="vencido" {{ request('status') === 'vencido' ? 'selected' : '' }}>Vencido</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-6 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 font-semibold text-sm shadow transition">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                        </svg>
                        Pesquisar
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

@if($boletos->count() > 0)
<div class="overflow-x-auto bg-white rounded-xl shadow-sm">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-100 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Cliente</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Documento</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Valor</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Vencimento</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Nosso Número</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Status</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Criado em</th>
                <th class="px-4 py-3 text-right"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($boletos as $boleto)
            <tr class="border-b last:border-0 hover:bg-gray-50 transition">
                <td class="px-4 py-3">
                    <div class="font-medium text-gray-900">{{ $boleto->cliente_nome }}</div>
                    <div class="text-xs text-gray-600">{{ $boleto->cliente_cidade }}/{{ $boleto->cliente_uf }}</div>
                </td>
                <td class="px-4 py-3 font-mono text-sm text-gray-600">
                    {{ $boleto->cliente_documento }}
                </td>
                <td class="px-4 py-3 font-semibold text-green-600">
                    R$ {{ number_format($boleto->valor, 2, ',', '.') }}
                </td>
                <td class="px-4 py-3 text-gray-600">
                    {{ $boleto->data_vencimento->format('d/m/Y') }}
                </td>
                <td class="px-4 py-3 font-mono text-sm text-gray-600">
                    {{ $boleto->nosso_numero }}
                </td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold 
                        @if($boleto->status === 'gerado') bg-green-100 text-green-800
                        @elseif($boleto->status === 'pago') bg-blue-100 text-blue-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ ucfirst($boleto->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">
                    {{ $boleto->created_at->format('d/m/Y H:i') }}
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex justify-end gap-2">
                        @if($boleto->pdf_path)
                            <a href="{{ $boleto->pdf_path }}" target="_blank" 
                               class="inline-flex items-center gap-1 text-white bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 font-semibold px-3 py-1.5 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-red-500 transition"
                               title="Abrir PDF">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                PDF
                            </a>
                        @endif
                        
                        <button onclick="mostrarDetalhes({{ $boleto->id }})" 
                                class="inline-flex items-center gap-1 text-white bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 font-semibold px-3 py-1.5 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                                title="Ver detalhes">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Detalhes
                        </button>
                        
                        <button onclick="copiarLinha('{{ $boleto->linha_digitavel }}')" 
                                class="inline-flex items-center gap-1 text-white bg-gradient-to-r from-gray-600 to-gray-500 hover:from-gray-700 hover:to-gray-600 font-semibold px-3 py-1.5 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-gray-500 transition"
                                title="Copiar linha digitável">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Copiar
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
    <div class="bg-white rounded-xl shadow-sm">
        <div class="text-center py-12">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-gray-400 mx-auto mb-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0-1.125-.504-1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Nenhum boleto encontrado</h3>
            <p class="text-gray-500 mb-6">Você ainda não gerou nenhum boleto ou nenhum boleto corresponde aos filtros aplicados.</p>
            <a href="{{ route('venda-boleto') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl transition font-semibold shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Gerar Primeiro Boleto
            </a>
        </div>
    </div>
@endif

<!-- Modal para detalhes do boleto -->
<div id="modalDetalhes" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-96 overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Detalhes do Boleto</h3>
                <button onclick="fecharModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="conteudoDetalhes"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const boletos = @json($boletos);

function mostrarDetalhes(id) {
    const boleto = boletos.find(b => b.id === id);
    if (!boleto) return;
    
    document.getElementById('conteudoDetalhes').innerHTML = `
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="bg-gray-50 p-3 rounded">
                    <span class="text-gray-600 text-xs font-medium">TXID</span>
                    <div class="font-mono text-sm">${boleto.txid}</div>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <span class="text-gray-600 text-xs font-medium">Nosso Número</span>
                    <div class="font-mono text-sm">${boleto.nosso_numero}</div>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <span class="text-gray-600 text-xs font-medium">Cooperativa</span>
                    <div class="font-mono text-sm">${boleto.cooperativa}</div>
                </div>
                <div class="bg-gray-50 p-3 rounded">
                    <span class="text-gray-600 text-xs font-medium">Posto</span>
                    <div class="font-mono text-sm">${boleto.posto}</div>
                </div>
            </div>
            <div class="bg-gray-50 p-3 rounded">
                <span class="text-gray-600 text-xs font-medium">Linha Digitável</span>
                <code class="block text-xs break-all mt-1 font-mono">${boleto.linha_digitavel}</code>
            </div>
            <div class="bg-gray-50 p-3 rounded">
                <span class="text-gray-600 text-xs font-medium">Código de Barras</span>
                <code class="block text-xs break-all mt-1 font-mono">${boleto.codigo_barras}</code>
            </div>
            ${boleto.qr_code ? `
            <div class="bg-gray-50 p-3 rounded">
                <span class="text-gray-600 text-xs font-medium">QR Code PIX</span>
                <code class="block text-xs break-all max-h-20 overflow-y-auto mt-1 font-mono">${boleto.qr_code}</code>
            </div>
            ` : ''}
        </div>
    `;
    
    document.getElementById('modalDetalhes').classList.remove('hidden');
    document.getElementById('modalDetalhes').classList.add('flex');
}

function fecharModal() {
    document.getElementById('modalDetalhes').classList.add('hidden');
    document.getElementById('modalDetalhes').classList.remove('flex');
}

function copiarLinha(linha) {
    navigator.clipboard.writeText(linha).then(() => {
        // Criar notificação de sucesso
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        notification.innerHTML = '✓ Linha digitável copiada!';
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }).catch(() => {
        alert('Erro ao copiar. Tente novamente.');
    });
}

// Fechar modal ao clicar fora
document.getElementById('modalDetalhes').addEventListener('click', function(e) {
    if (e.target === this) {
        fecharModal();
    }
});
</script>
@endsection
