@extends('dashboard.layout')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Orçamentos</h1>
        <a href="{{ route('quotes.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-semibold shadow flex items-center gap-2">
            <i class="fas fa-plus"></i> Novo Orçamento
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criado em</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Validade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($quotes as $quote)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $quote->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $quote->customer_name }}</div>
                            @if($quote->customer_email)
                                <div class="text-sm text-gray-500">{{ $quote->customer_email }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            R$ {{ number_format($quote->final_total, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $quote->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($quote->valid_until)
                                {{ $quote->valid_until->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('quotes.show', $quote) }}" class="text-blue-600 hover:text-blue-900" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('quotes.pdf', $quote) }}" class="text-green-600 hover:text-green-900" title="Gerar PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                @if($quote->status === 'draft' || $quote->status === 'sent')
                                <button onclick="converterParaVenda({{ $quote->id }})" class="text-purple-600 hover:text-purple-900" title="Converter em Venda">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Nenhum orçamento encontrado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($quotes->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $quotes->links() }}
        </div>
        @endif
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
            location.reload();
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
