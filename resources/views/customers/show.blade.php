@extends('dashboard.layout')

@section('title', 'Detalhes do Cliente')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Detalhes do Cliente</h1>
        <div class="space-x-2">
            <a href="{{ route('customers.edit', $customer) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Editar
            </a>
            <a href="{{ route('customers.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Voltar
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Cabeçalho do Cliente -->
        <div class="p-6 bg-gray-50 border-b">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">{{ $customer->name }}</h2>
                    <p class="text-gray-600">{{ $customer->formatted_cpf_cnpj }} - {{ $customer->display_type }}</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $customer->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $customer->active ? 'Ativo' : 'Inativo' }}
                    </span>
                    <p class="text-xs text-gray-500 mt-1">Cliente desde {{ $customer->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Informações de Contato -->
        <div class="p-6 border-b">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Informações de Contato</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Telefone</label>
                    <p class="mt-1 text-gray-900">{{ $customer->phone ?: 'Não informado' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Email</label>
                    <p class="mt-1 text-gray-900">{{ $customer->email ?: 'Não informado' }}</p>
                </div>
            </div>
        </div>

        <!-- Endereço -->
        <div class="p-6 border-b">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Endereço</h3>
            @if($customer->address || $customer->number || $customer->neighborhood || $customer->city || $customer->state || $customer->postal_code)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500">Logradouro</label>
                        <p class="mt-1 text-gray-900">{{ $customer->address ?: 'Não informado' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Número</label>
                        <p class="mt-1 text-gray-900">{{ $customer->number ?: 'Não informado' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Bairro</label>
                        <p class="mt-1 text-gray-900">{{ $customer->neighborhood ?: 'Não informado' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Cidade</label>
                        <p class="mt-1 text-gray-900">{{ $customer->city ?: 'Não informado' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Estado</label>
                        <p class="mt-1 text-gray-900">{{ $customer->state ?: 'Não informado' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">CEP</label>
                        <p class="mt-1 text-gray-900">{{ $customer->postal_code ?: 'Não informado' }}</p>
                    </div>
                </div>
            @else
                <p class="text-gray-500 italic">Endereço não informado</p>
            @endif
        </div>

        <!-- Observações -->
        @if($customer->notes)
            <div class="p-6 border-b">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Observações</h3>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $customer->notes }}</p>
            </div>
        @endif

        <!-- Histórico de Vendas -->
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Resumo de Vendas</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600">{{ $customer->sales_count }}</p>
                    <p class="text-sm text-blue-800">Total de Vendas</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600">R$ {{ number_format($customer->sales_total, 2, ',', '.') }}</p>
                    <p class="text-sm text-green-800">Valor Total</p>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <p class="text-2xl font-bold text-purple-600">
                        {{ $customer->sales_count > 0 ? 'R$ ' . number_format($customer->sales_total / $customer->sales_count, 2, ',', '.') : 'R$ 0,00' }}
                    </p>
                    <p class="text-sm text-purple-800">Ticket Médio</p>
                </div>
            </div>
        </div>

        <!-- Últimas Vendas -->
        @if($customer->recent_sales && $customer->recent_sales->count() > 0)
            <div class="p-6 border-t">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Últimas Vendas</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customer->recent_sales as $sale)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $sale->sale_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        R$ {{ number_format($sale->total_amount, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $sale->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                       ($sale->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $sale->status === 'completed' ? 'Concluída' : 
                                               ($sale->status === 'pending' ? 'Pendente' : 'Cancelada') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('sales.show', $sale) }}" 
                                           class="text-blue-600 hover:text-blue-900">Ver detalhes</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
