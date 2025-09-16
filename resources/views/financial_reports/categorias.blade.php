@extends('dashboard.layout')

@section('title', 'Relatório por Categorias')

@section('content')
<div class="space-y-6">
    <!-- Cabeçalho -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Relatório por Categorias</h1>
        <p class="text-gray-600">Análise das movimentações financeiras agrupadas por categoria</p>
    </div>

    <!-- Filtros de Data -->
    <div class="bg-white p-6 rounded-lg shadow-sm border">
        <form method="GET" action="{{ route('financial-reports.categorias') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="data_inicio" class="block text-sm font-medium text-gray-700">Data Início</label>
                <input type="date" 
                       name="data_inicio" 
                       id="data_inicio" 
                       value="{{ \Carbon\Carbon::parse($dataInicio)->format('Y-m-d') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="data_fim" class="block text-sm font-medium text-gray-700">Data Fim</label>
                <input type="date" 
                       name="data_fim" 
                       id="data_fim" 
                       value="{{ \Carbon\Carbon::parse($dataFim)->format('Y-m-d') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Resumo -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Categorias a Pagar</p>
                    <p class="text-2xl font-bold text-red-600">{{ $topCategoriasPagar->count() }}</p>
                    <p class="text-sm text-gray-500">R$ {{ number_format($topCategoriasPagar->sum('total'), 2, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Categorias a Receber</p>
                    <p class="text-2xl font-bold text-green-600">{{ $topCategoriasReceber->count() }}</p>
                    <p class="text-sm text-gray-500">R$ {{ number_format($topCategoriasReceber->sum('total'), 2, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Categorias a Pagar -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
                Top 10 Categorias - Contas a Pagar
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-red-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posição</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Médio</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topCategoriasPagar as $index => $categoria)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $index + 1 }}º
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $categoria->categoria ?: 'Sem categoria' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $categoria->quantidade }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                                R$ {{ number_format($categoria->total, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                R$ {{ number_format($categoria->total / $categoria->quantidade, 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Nenhuma categoria encontrada no período selecionado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Categorias a Receber -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
                Top 10 Categorias - Contas a Receber
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-green-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posição</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Médio</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topCategoriasReceber as $index => $categoria)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $index + 1 }}º
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $categoria->categoria ?: 'Sem categoria' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $categoria->quantidade }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                                R$ {{ number_format($categoria->total, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                R$ {{ number_format($categoria->total / $categoria->quantidade, 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Nenhuma categoria encontrada no período selecionado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Comparativo -->
    @if($topCategoriasPagar->count() > 0 && $topCategoriasReceber->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Comparativo por Categoria</h3>
            <p class="text-sm text-gray-600">Categorias que aparecem tanto em contas a pagar quanto a receber</p>
        </div>
        <div class="p-6">
            @php
                $categoriasComum = $topCategoriasPagar->pluck('categoria')->intersect($topCategoriasReceber->pluck('categoria'));
            @endphp
            
            @if($categoriasComum->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($categoriasComum as $categoria)
                        @php
                            $pagar = $topCategoriasPagar->where('categoria', $categoria)->first();
                            $receber = $topCategoriasReceber->where('categoria', $categoria)->first();
                            $saldo = $receber->total - $pagar->total;
                        @endphp
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">{{ $categoria ?: 'Sem categoria' }}</h4>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-red-600">A Pagar:</span>
                                    <span>R$ {{ number_format($pagar->total, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-green-600">A Receber:</span>
                                    <span>R$ {{ number_format($receber->total, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between font-medium border-t pt-1">
                                    <span>Saldo:</span>
                                    <span class="{{ $saldo >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        R$ {{ number_format($saldo, 2, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Nenhuma categoria comum encontrada.</p>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
