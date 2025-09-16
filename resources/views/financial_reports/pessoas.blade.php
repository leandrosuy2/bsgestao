@extends('dashboard.layout')

@section('title', 'Relatório por Pessoas')

@section('content')
<div class="space-y-6">
    <!-- Cabeçalho -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Relatório por Pessoas</h1>
        <p class="text-gray-600">Análise das movimentações financeiras agrupadas por pessoa</p>
    </div>

    <!-- Filtros de Data -->
    <div class="bg-white p-6 rounded-lg shadow-sm border">
        <form method="GET" action="{{ route('financial-reports.pessoas') }}" class="flex flex-wrap gap-4 items-end">
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
                    <p class="text-sm font-medium text-gray-600">Total Pessoas a Pagar</p>
                    <p class="text-2xl font-bold text-red-600">{{ $topPessoasPagar->count() }}</p>
                    <p class="text-sm text-gray-500">R$ {{ number_format($topPessoasPagar->sum('total'), 2, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Pessoas a Receber</p>
                    <p class="text-2xl font-bold text-green-600">{{ $topPessoasReceber->count() }}</p>
                    <p class="text-sm text-gray-500">R$ {{ number_format($topPessoasReceber->sum('total'), 2, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Pessoas a Pagar -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
                Top 15 Pessoas - Contas a Pagar
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-red-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posição</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pessoa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Médio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% do Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $totalGeral = $topPessoasPagar->sum('total'); @endphp
                    @forelse($topPessoasPagar as $index => $pessoa)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $index + 1 }}º
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-xs font-medium text-gray-700">
                                                {{ strtoupper(substr($pessoa->pessoa ?: 'S', 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $pessoa->pessoa ?: 'Sem identificação' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $pessoa->quantidade }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                                R$ {{ number_format($pessoa->total, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                R$ {{ number_format($pessoa->total / $pessoa->quantidade, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $totalGeral > 0 ? number_format(($pessoa->total / $totalGeral) * 100, 1) : 0 }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Nenhuma pessoa encontrada no período selecionado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pessoas a Receber -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
                Top 15 Pessoas - Contas a Receber
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-green-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posição</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pessoa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Médio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% do Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $totalGeralReceber = $topPessoasReceber->sum('total'); @endphp
                    @forelse($topPessoasReceber as $index => $pessoa)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $index + 1 }}º
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-xs font-medium text-gray-700">
                                                {{ strtoupper(substr($pessoa->pessoa ?: 'S', 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $pessoa->pessoa ?: 'Sem identificação' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $pessoa->quantidade }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                                R$ {{ number_format($pessoa->total, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                R$ {{ number_format($pessoa->total / $pessoa->quantidade, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $totalGeralReceber > 0 ? number_format(($pessoa->total / $totalGeralReceber) * 100, 1) : 0 }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Nenhuma pessoa encontrada no período selecionado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Comparativo -->
    @if($topPessoasPagar->count() > 0 && $topPessoasReceber->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Comparativo por Pessoa</h3>
            <p class="text-sm text-gray-600">Pessoas que aparecem tanto em contas a pagar quanto a receber</p>
        </div>
        <div class="p-6">
            @php
                $pessoasComum = $topPessoasPagar->pluck('pessoa')->intersect($topPessoasReceber->pluck('pessoa'));
            @endphp
            
            @if($pessoasComum->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pessoa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">A Pagar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">A Receber</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Situação</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pessoasComum as $pessoa)
                                @php
                                    $pagar = $topPessoasPagar->where('pessoa', $pessoa)->first();
                                    $receber = $topPessoasReceber->where('pessoa', $pessoa)->first();
                                    $saldo = $receber->total - $pagar->total;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-gray-700">
                                                        {{ strtoupper(substr($pessoa ?: 'S', 0, 2)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $pessoa ?: 'Sem identificação' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                        R$ {{ number_format($pagar->total, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                        R$ {{ number_format($receber->total, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $saldo >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        R$ {{ number_format($saldo, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($saldo > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                A receber mais
                                            </span>
                                        @elseif($saldo < 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                A pagar mais
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Equilibrado
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Nenhuma pessoa comum encontrada.</p>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
