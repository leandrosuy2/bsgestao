@extends('dashboard.layout')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detalhes da Venda #{{ $sale->id }}</h1>
            <p class="text-gray-600">Visualize os detalhes antes de cancelar</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('sales.cancellation.index') }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Voltar
            </a>
            <button onclick="cancelarVenda({{ $sale->id }}, '{{ $sale->customer->name ?? 'Cliente não informado' }}', {{ $sale->final_total }})" 
                    class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                <i class="fas fa-times mr-2"></i>Cancelar Venda
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informações da Venda -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações da Venda</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">ID da Venda</label>
                        <p class="text-sm text-gray-900">#{{ $sale->id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Data/Hora</label>
                        <p class="text-sm text-gray-900">{{ $sale->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cliente</label>
                        <p class="text-sm text-gray-900">{{ $sale->customer->name ?? 'Cliente não informado' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Vendedor</label>
                        <p class="text-sm text-gray-900">{{ $sale->seller->name ?? 'Sem vendedor' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ ucfirst($sale->status) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Modo de Pagamento</label>
                        <p class="text-sm text-gray-900">{{ $sale->payment_mode === 'cash' ? 'À vista' : 'A prazo' }}</p>
                    </div>
                </div>

                <!-- Itens da Venda -->
                <h4 class="text-md font-semibold text-gray-900 mb-3">Itens da Venda</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produto</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qtd</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Preço Unit.</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Desconto</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($sale->items as $item)
                                <tr>
                                    <td class="px-3 py-2 text-sm text-gray-900">
                                        {{ $item->product_name }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-900">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-900">
                                        R$ {{ number_format($item->unit_price, 2, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-900">
                                        @if($item->discount_amount > 0)
                                            R$ {{ number_format($item->discount_amount, 2, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-900">
                                        R$ {{ number_format($item->final_price, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Resumo Financeiro -->
        <div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumo Financeiro</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Subtotal:</span>
                        <span class="text-sm font-medium text-gray-900">
                            R$ {{ number_format($sale->items->sum('total_price'), 2, ',', '.') }}
                        </span>
                    </div>
                    
                    @if($sale->items->sum('discount_amount') > 0)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Desconto Produtos:</span>
                        <span class="text-sm font-medium text-red-600">
                            -R$ {{ number_format($sale->items->sum('discount_amount'), 2, ',', '.') }}
                        </span>
                    </div>
                    @endif
                    
                    @if($sale->discount > 0)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Desconto Geral:</span>
                        <span class="text-sm font-medium text-red-600">
                            -R$ {{ number_format($sale->discount, 2, ',', '.') }}
                        </span>
                    </div>
                    @endif
                    
                    <div class="border-t pt-3">
                        <div class="flex justify-between">
                            <span class="text-base font-semibold text-gray-900">Total Final:</span>
                            <span class="text-base font-bold text-gray-900">
                                R$ {{ number_format($sale->final_total, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagamentos -->
            @if($sale->payments->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pagamentos</h3>
                
                <div class="space-y-2">
                    @foreach($sale->payments as $payment)
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-sm text-gray-600">{{ ucfirst($payment->type) }}</span>
                            <span class="text-sm font-medium text-gray-900">
                                R$ {{ number_format($payment->amount, 2, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Cancelamento -->
<div id="modalCancelarVenda" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Cancelar Venda</h3>
                    <button onclick="fecharModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                            <div>
                                <p class="font-semibold text-red-800">Atenção!</p>
                                <p class="text-sm text-red-600">Esta ação irá cancelar a venda e reverter o estoque.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm text-gray-600">
                        <strong>Venda:</strong> <span id="vendaInfo"></span><br>
                        <strong>Total:</strong> <span id="vendaTotal"></span>
                    </p>
                </div>
                
                <form id="formCancelarVenda">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Motivo do cancelamento *
                        </label>
                        <textarea id="motivoCancelamento" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                                  placeholder="Digite o motivo do cancelamento..."></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="fecharModal()" 
                                class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            <i class="fas fa-times mr-2"></i>Confirmar Cancelamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let vendaIdAtual = null;

function cancelarVenda(id, cliente, total) {
    vendaIdAtual = id;
    document.getElementById('vendaInfo').textContent = `#${id} - ${cliente}`;
    document.getElementById('vendaTotal').textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
    document.getElementById('modalCancelarVenda').classList.remove('hidden');
}

function fecharModal() {
    document.getElementById('modalCancelarVenda').classList.add('hidden');
    document.getElementById('motivoCancelamento').value = '';
    vendaIdAtual = null;
}

document.getElementById('formCancelarVenda').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const motivo = document.getElementById('motivoCancelamento').value;
    
    if (!motivo.trim()) {
        alert('Por favor, digite o motivo do cancelamento.');
        return;
    }
    
    // Mostrar loading
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Cancelando...';
    submitBtn.disabled = true;
    
    // Fazer requisição AJAX
    fetch(`/sales/cancellation/${vendaIdAtual}/cancel`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            reason: motivo
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = '/sales/cancellation';
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao cancelar venda');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        fecharModal();
    });
});
</script>
@endsection
