@extends('dashboard.layout')

@section('title', 'Romaneios')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Romaneios</h1>
        <a href="{{ route('delivery_receipts.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            + Novo Romaneio
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fornecedor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transportadora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progresso</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($receipts as $receipt)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $receipt->receipt_number }}</div>
                                <div class="text-sm text-gray-500">por {{ $receipt->user ? $receipt->user->name : 'Sistema' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $receipt->delivery_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $receipt->supplier_name ?: 'N/A' }}</div>
                                @if($receipt->supplier_city)
                                    <div class="text-sm text-gray-500">{{ $receipt->supplier_city }}, {{ $receipt->supplier_state }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $receipt->carrier ?: 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $receipt->completion_percentage }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $receipt->checked_items }}/{{ $receipt->total_items }}</span>
                                </div>
                                <div class="text-xs text-gray-500">{{ $receipt->completion_percentage }}% concluído</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($receipt->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($receipt->status === 'completed') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $receipt->status_name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('delivery_receipts.show', $receipt) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                                    <a href="{{ route('delivery_receipts.edit', $receipt) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                    <form action="{{ route('delivery_receipts.destroy', $receipt) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('Tem certeza que deseja excluir este romaneio?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                Nenhum romaneio encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($receipts->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $receipts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
