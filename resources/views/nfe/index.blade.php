@extends('dashboard.layout')

@section('title', 'Notas Fiscais')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Notas Fiscais</h1>
        <div class="flex gap-2">
            <a href="{{ route('nfe.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Painel de Emissão
            </a>
            <a href="{{ route('nfe.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nova Nota Fiscal
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" action="{{ route('nfe.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                <input type="text" name="numero" value="{{ request('numero') }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Número da NFe">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                <input type="text" name="cliente" value="{{ request('cliente') }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Nome do cliente">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="rascunho" {{ request('status') == 'rascunho' ? 'selected' : '' }}>Rascunho</option>
                    <option value="processando_autorizacao" {{ request('status') == 'processando_autorizacao' ? 'selected' : '' }}>Processando</option>
                    <option value="autorizado" {{ request('status') == 'autorizado' ? 'selected' : '' }}>Autorizada</option>
                    <option value="emitida" {{ request('status') == 'emitida' ? 'selected' : '' }}>Emitida</option>
                    <option value="erro_autorizacao" {{ request('status') == 'erro_autorizacao' ? 'selected' : '' }}>Erro</option>
                    <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg mr-2">
                    Filtrar
                </button>
                <a href="{{ route('nfe.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Lista de NFes -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($nfes->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($nfes as $nfe)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $nfe->numero_nfe ?? 'Pendente' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $nfe->nome_destinatario }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    R$ {{ number_format($nfe->valor_total, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($nfe->status)
                                        @case('rascunho')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Rascunho
                                            </span>
                                            @break
                                        @case('processando_autorizacao')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Processando
                                            </span>
                                            @break
                                        @case('autorizado')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Autorizada
                                            </span>
                                            @break
                                        @case('emitida')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Emitida
                                            </span>
                                            @break
                                        @case('erro_autorizacao')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Erro
                                            </span>
                                            @break
                                        @case('cancelado')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Cancelada
                                            </span>
                                            @break
                                        @default
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ ucfirst($nfe->status) }}
                                            </span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $nfe->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('nfe.show', $nfe) }}" 
                                           class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-2 py-1 rounded">
                                            Ver
                                        </a>
                                        
                                        @if($nfe->status === 'rascunho')
                                            <button onclick="emitirNfe({{ $nfe->id }})" 
                                                    class="text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200 px-2 py-1 rounded">
                                                Emitir
                                            </button>
                                        @endif
                                        
                                        @if(in_array($nfe->status, ['emitida', 'autorizado']))
                                            <a href="{{ route('nfe.danfe', $nfe) }}" target="_blank"
                                               class="text-purple-600 hover:text-purple-900 bg-purple-100 hover:bg-purple-200 px-2 py-1 rounded">
                                                DANFE
                                            </a>
                                            <a href="{{ route('nfe.xml', $nfe) }}" target="_blank"
                                               class="text-orange-600 hover:text-orange-900 bg-orange-100 hover:bg-orange-200 px-2 py-1 rounded">
                                                XML
                                            </a>
                                            <button onclick="cancelarNfe({{ $nfe->id }})" 
                                                    class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-2 py-1 rounded">
                                                Cancelar
                                            </button>
                                        @endif

                                        @if(in_array($nfe->status, ['rascunho', 'erro', 'erro_autorizacao', 'cancelado']))
                                            <button onclick="excluirNfe({{ $nfe->id }})" 
                                                    class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-2 py-1 rounded"
                                                    title="Excluir NFe">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginação -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $nfes->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma nota fiscal encontrada</h3>
                <p class="mt-1 text-sm text-gray-500">Comece criando uma nova nota fiscal.</p>
                <div class="mt-6">
                    <a href="{{ route('nfe.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Nova Nota Fiscal
                    </a>
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

function cancelarNfe(id) {
    const justificativa = prompt('Digite a justificativa para cancelamento (mínimo 15 caracteres):');
    if (justificativa && justificativa.length >= 15) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/nfe/${id}/cancelar`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const justificativaInput = document.createElement('input');
        justificativaInput.type = 'hidden';
        justificativaInput.name = 'justificativa';
        justificativaInput.value = justificativa;
        form.appendChild(justificativaInput);
        
        document.body.appendChild(form);
        form.submit();
    } else if (justificativa !== null) {
        alert('A justificativa deve ter no mínimo 15 caracteres.');
    }
}

function excluirNfe(id) {
    if (confirm('⚠️ ATENÇÃO: Esta ação é IRREVERSÍVEL!\n\nTem certeza que deseja EXCLUIR definitivamente esta NFe?\n\nEla será removida permanentemente do sistema.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/nfe/${id}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
