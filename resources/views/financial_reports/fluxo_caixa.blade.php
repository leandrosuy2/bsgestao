@extends('dashboard.layout')

@section('title', 'Fluxo de Caixa')

@section('content')
<div class="space-y-6">
    <!-- Cabeçalho -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Fluxo de Caixa</h1>
        <p class="text-gray-600">Análise das movimentações financeiras por período</p>
    </div>

    <!-- Filtros de Data -->
    <div class="bg-white p-6 rounded-lg shadow-sm border">
        <form method="GET" action="{{ route('financial-reports.fluxo-caixa') }}" class="flex flex-wrap gap-4 items-end">
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

    <!-- Resumo do Período -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total a Pagar</p>
                    <p class="text-2xl font-bold text-red-600">
                        R$ {{ number_format($pagarPeriodo->sum('total'), 2, ',', '.') }}
                    </p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total a Receber</p>
                    <p class="text-2xl font-bold text-green-600">
                        R$ {{ number_format($receberPeriodo->sum('total'), 2, ',', '.') }}
                    </p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Saldo Líquido</p>
                    @php
                        $saldoLiquido = $receberPeriodo->sum('total') - $pagarPeriodo->sum('total');
                    @endphp
                    <p class="text-2xl font-bold {{ $saldoLiquido >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        R$ {{ number_format($saldoLiquido, 2, ',', '.') }}
                    </p>
                </div>
                <div class="p-3 {{ $saldoLiquido >= 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-full">
                    <svg class="w-6 h-6 {{ $saldoLiquido >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Fluxo de Caixa -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Fluxo de Caixa por Data</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">A Pagar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">A Receber</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo do Dia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo Acumulado</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $saldoAcumulado = 0;
                        $todasDatas = collect();
                        
                        // Combinar todas as datas
                        foreach($pagarPeriodo as $item) {
                            $todasDatas->push($item->data);
                        }
                        foreach($receberPeriodo as $item) {
                            $todasDatas->push($item->data);
                        }
                        $todasDatas = $todasDatas->unique()->sort();
                    @endphp
                    
                    @forelse($todasDatas as $data)
                        @php
                            $valorPagar = $pagarPeriodo->where('data', $data)->first()->total ?? 0;
                            $valorReceber = $receberPeriodo->where('data', $data)->first()->total ?? 0;
                            $saldoDia = $valorReceber - $valorPagar;
                            $saldoAcumulado += $saldoDia;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($data)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                R$ {{ number_format($valorPagar, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                R$ {{ number_format($valorReceber, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $saldoDia >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                R$ {{ number_format($saldoDia, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $saldoAcumulado >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                R$ {{ number_format($saldoAcumulado, 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Nenhuma movimentação encontrada no período selecionado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
