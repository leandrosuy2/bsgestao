@extends('dashboard.layout')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Orçamento #{{ $quote->id }}</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('quotes.pdf', $quote) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold shadow flex items-center gap-2">
                <i class="fas fa-file-pdf"></i> Gerar PDF
            </a>
            @if($quote->status === 'draft' || $quote->status === 'sent')
            <button onclick="converterParaVenda({{ $quote->id }})" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-semibold shadow flex items-center gap-2">
                <i class="fas fa-shopping-cart"></i> Converter em Venda
            </button>
            @endif
            <a href="{{ route('quotes.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold shadow flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informações do Orçamento -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Detalhes do Orçamento</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cliente</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $quote->customer_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <p class="mt-1">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-gray-100 text-gray-800',
                                    'sent' => 'bg-blue-100 text-blue-800',
                                    'accepted' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'expired' => 'bg-yellow-100 text-yellow-800',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$quote->status] }}">
                                {{ $quote->status_label }}
                            </span>
                        </p>
                    </div>
                    @if($quote->customer_email)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $quote->customer_email }}</p>
                    </div>
                    @endif
                    @if($quote->customer_phone)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Telefone</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $quote->customer_phone }}</p>
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Criado em</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $quote->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($quote->valid_until)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Validade</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $quote->valid_until->format('d/m/Y') }}</p>
                    </div>
                    @endif
                </div>

                @if($quote->notes)
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Observações</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $quote->notes }}</p>
                </div>
                @endif

                <!-- Itens do Orçamento -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Itens do Orçamento</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produto</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qtd</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unitário</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($quote->items as $item)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $item->product_name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-center">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right">R$ {{ number_format($item->total_price, 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumo</h3>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Subtotal:</span>
                        <span class="text-sm font-medium text-gray-900">R$ {{ number_format($quote->total, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Desconto:</span>
                        <span class="text-sm font-medium text-gray-900">R$ {{ number_format($quote->discount, 2, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between">
                            <span class="text-base font-semibold text-gray-900">Total Final:</span>
                            <span class="text-base font-bold text-purple-600">R$ {{ number_format($quote->final_total, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-600">
                        <p><strong>Criado por:</strong> {{ $quote->user->name }}</p>
                        <p><strong>Empresa:</strong> {{ $quote->company->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function converterParaVenda(quoteId) {
    if (!confirm('Deseja converter este orçamento em venda?')) {
        return;
    }

    fetch(`/quotes/${quoteId}/convert`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Orçamento convertido em venda com sucesso!');
            window.location.href = '/pdv/receipt/' + data.sale_id;
        } else {
            alert(data.error || 'Erro ao converter orçamento!');
        }
    })
    .catch(e => {
        alert('Erro ao converter orçamento!');
    });
}
</script>
@endsection
