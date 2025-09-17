@extends('dashboard.layout')

@section('title', 'Relatório de Controle de Estoque')

@section('content')
<div class="space-y-6">
    <!-- Cabeçalho do Relatório -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Relatório de Controle de Estoque</h1>
                <p class="text-gray-600">Comparação entre Estoque Físico e Virtual</p>
                <div class="mt-2 text-sm text-gray-500">
                    <p><strong>Empresa:</strong> {{ $user->company->name ?? 'N/A' }}</p>
                    @if($category)
                        <p><strong>Categoria:</strong> {{ $category->name }}</p>
                    @endif
                    <p><strong>Gerado em:</strong> {{ $generatedAt->format('d/m/Y H:i') }}</p>
                    <p><strong>Incluindo estoque zero:</strong> {{ $showZeroStock ? 'Sim' : 'Não' }}</p>
                </div>
            </div>
            <div class="text-right">
                <a href="{{ route('stock-control-reports.generate', ['category_id' => request('category_id'), 'show_zero_stock' => $showZeroStock, 'format' => 'pdf']) }}" 
                   class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Baixar PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Resumo Executivo -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total de Produtos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_products']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Estoque Físico Total</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_physical_stock']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Divergências</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['products_with_difference']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-full">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Valor Total</p>
                    <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($stats['total_value'], 2, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas Adicionais -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Status do Estoque</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Produtos com estoque baixo</span>
                    <span class="text-sm font-semibold text-red-600">{{ $stats['low_stock_products'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Produtos sem estoque</span>
                    <span class="text-sm font-semibold text-gray-600">{{ $stats['zero_stock_products'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Precisão do estoque</span>
                    <span class="text-sm font-semibold text-green-600">{{ number_format($stats['accuracy_percentage'], 1) }}%</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Comparação de Estoque</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Estoque Físico</span>
                    <span class="text-sm font-semibold text-green-600">{{ number_format($stats['total_physical_stock']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Estoque Virtual</span>
                    <span class="text-sm font-semibold text-blue-600">{{ number_format($stats['total_virtual_stock']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Diferença Total</span>
                    <span class="text-sm font-semibold {{ $stats['total_difference'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $stats['total_difference'] >= 0 ? '+' : '' }}{{ number_format($stats['total_difference']) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ações Recomendadas</h3>
            <div class="space-y-2">
                @if($stats['products_with_difference'] > 0)
                    <div class="flex items-center text-sm text-yellow-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Realizar inventário físico
                    </div>
                @endif
                @if($stats['low_stock_products'] > 0)
                    <div class="flex items-center text-sm text-red-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Repor estoque baixo
                    </div>
                @endif
                @if($stats['zero_stock_products'] > 0)
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Verificar produtos sem estoque
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabela de Produtos -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Detalhamento por Produto</h3>
        
        @if(count($products) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estoque Físico</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estoque Virtual</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Diferença</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($products as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $item['product']->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $item['product']->internal_code }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $item['category']->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm font-semibold text-green-600">{{ number_format($item['physical_stock']) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm font-semibold text-blue-600">{{ number_format($item['virtual_stock']) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm font-semibold {{ $item['difference'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $item['difference'] >= 0 ? '+' : '' }}{{ number_format($item['difference']) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $statusColors = [
                                            'normal' => 'bg-green-100 text-green-800',
                                            'low' => 'bg-yellow-100 text-yellow-800',
                                            'high' => 'bg-blue-100 text-blue-800',
                                            'zero' => 'bg-gray-100 text-gray-800'
                                        ];
                                        $statusLabels = [
                                            'normal' => 'Normal',
                                            'low' => 'Baixo',
                                            'high' => 'Alto',
                                            'zero' => 'Zero'
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$item['status']] }}">
                                        {{ $statusLabels[$item['status']] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-semibold text-gray-900">R$ {{ number_format($item['stock_value'], 2, ',', '.') }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-8">Nenhum produto encontrado com os filtros aplicados</p>
        @endif
    </div>

    <!-- Produtos com Maiores Divergências -->
    @if(count($products) > 0)
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 10 Maiores Divergências</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach(array_slice($products, 0, 10) as $item)
                @if($item['difference'] != 0)
                <div class="p-4 border border-gray-200 rounded-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $item['product']->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $item['category']->name ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold {{ $item['difference'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $item['difference'] >= 0 ? '+' : '' }}{{ number_format($item['difference']) }}
                            </div>
                            <div class="text-xs text-gray-500">
                                Físico: {{ $item['physical_stock'] }} | Virtual: {{ $item['virtual_stock'] }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
