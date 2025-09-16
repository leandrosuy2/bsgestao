@extends('dashboard.layout')

@section('title', 'Detalhes do Romaneio')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Romaneio #{{ $deliveryReceipt->id }}</h1>
        <div class="space-x-2">
            @if($deliveryReceipt->status !== 'finalized')
                <a href="{{ route('delivery_receipts.edit', $deliveryReceipt) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Editar
                </a>
            @endif
            <a href="{{ route('delivery_receipts.pdf', $deliveryReceipt) }}" 
               class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                <i class="fas fa-file-pdf mr-2"></i>Baixar PDF
            </a>
            <button onclick="window.print()" 
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Imprimir
            </button>
            <a href="{{ route('delivery_receipts.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Voltar
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Cabeçalho do Romaneio -->
        <div class="p-6 bg-gray-50 border-b">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-800 mb-3">Informações do Fornecedor</h3>
                    <div class="space-y-2">
                        <p><span class="font-medium">Nome:</span> {{ $deliveryReceipt->supplier_name }}</p>
                        <p><span class="font-medium">CNPJ:</span> {{ $deliveryReceipt->supplier_cnpj }}</p>
                        @if($deliveryReceipt->supplier_contact)
                            <p><span class="font-medium">Contato:</span> {{ $deliveryReceipt->supplier_contact }}</p>
                        @endif
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-800 mb-3">Informações da Entrega</h3>
                    <div class="space-y-2">
                        <p><span class="font-medium">Data:</span> {{ $deliveryReceipt->delivery_date->format('d/m/Y') }}</p>
                        <p><span class="font-medium">Status:</span> 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $deliveryReceipt->status === 'finalized' ? 'bg-green-100 text-green-800' : 
                                           ($deliveryReceipt->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $deliveryReceipt->status === 'finalized' ? 'Finalizado' : 
                                   ($deliveryReceipt->status === 'in_progress' ? 'Em Andamento' : 'Pendente') }}
                            </span>
                        </p>
                        <p><span class="font-medium">Progresso:</span> 
                            <span class="font-semibold text-blue-600">{{ $deliveryReceipt->progress_percentage }}%</span>
                        </p>
                        @if($deliveryReceipt->finalized_by)
                            <p><span class="font-medium">Finalizado por:</span> {{ $deliveryReceipt->finalizedBy->name ?? 'Usuário removido' }}</p>
                            <p><span class="font-medium">Data de finalização:</span> {{ $deliveryReceipt->finalized_at->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra de Progresso -->
        <div class="p-6 border-b">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-medium text-gray-700">Progresso da Conferência</span>
                <span class="text-sm font-medium text-gray-700">{{ $deliveryReceipt->checked_items }}/{{ $deliveryReceipt->total_items }} itens</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" 
                     style="width: {{ $deliveryReceipt->progress_percentage }}%"></div>
            </div>
        </div>

        <!-- Lista de Produtos -->
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Produtos do Romaneio</h3>
            
            @if($deliveryReceipt->items->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qtd Esperada</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qtd Recebida</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observações</th>
                                @if($deliveryReceipt->status !== 'finalized')
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($deliveryReceipt->items as $item)
                                <tr id="item-{{ $item->id }}" class="{{ $item->checked ? 'bg-green-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center">
                                            @if($item->checked)
                                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16z" clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                        @if($item->product_code)
                                            <div class="text-sm text-gray-500">Código: {{ $item->product_code }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                        {{ number_format($item->expected_quantity, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm font-medium 
                                                    {{ $item->received_quantity == $item->expected_quantity ? 'text-green-600' : 
                                                       ($item->received_quantity > $item->expected_quantity ? 'text-blue-600' : 'text-red-600') }}">
                                            {{ number_format($item->received_quantity, 2, ',', '.') }}
                                        </span>
                                        @if($item->received_quantity != $item->expected_quantity)
                                            <div class="text-xs text-gray-500">
                                                Dif: {{ number_format($item->received_quantity - $item->expected_quantity, 2, ',', '.') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $item->notes ?: '-' }}
                                    </td>
                                    @if($deliveryReceipt->status !== 'finalized')
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <button onclick="toggleItem({{ $item->id }})" 
                                                    class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                                {{ $item->checked ? 'Desmarcar' : 'Conferir' }}
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-5.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-5.172a1 1 0 01-.707-.293L7.586 13H2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum produto</h3>
                    <p class="mt-1 text-sm text-gray-500">Este romaneio ainda não possui produtos cadastrados.</p>
                </div>
            @endif
        </div>

        <!-- Observações Gerais -->
        @if($deliveryReceipt->notes)
            <div class="p-6 border-t">
                <h3 class="text-lg font-medium text-gray-800 mb-3">Observações Gerais</h3>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $deliveryReceipt->notes }}</p>
            </div>
        @endif

        <!-- Ações -->
        @if($deliveryReceipt->status !== 'finalized')
            <div class="p-6 border-t bg-gray-50">
                <div class="flex justify-end space-x-2">
                    <button onclick="finalizeReceipt()" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Finalizar Romaneio
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

@if($deliveryReceipt->status !== 'finalized')
<script>
function toggleItem(itemId) {
    // Verificar se o token CSRF existe
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('Erro: Token CSRF não encontrado. Recarregue a página e tente novamente.');
        return;
    }

    fetch(`/delivery-receipts/{{ $deliveryReceipt->id }}/items/${itemId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro ao atualizar item: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao comunicar com o servidor');
    });
}

function finalizeReceipt() {
    if (confirm('Tem certeza que deseja finalizar este romaneio? Esta ação não poderá ser desfeita.')) {
        // Verificar se o token CSRF existe
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('Erro: Token CSRF não encontrado. Recarregue a página e tente novamente.');
            return;
        }

        fetch(`/delivery-receipts/{{ $deliveryReceipt->id }}/finalize`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao finalizar romaneio: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao comunicar com o servidor');
        });
    }
}
</script>
@endif
@endsection
