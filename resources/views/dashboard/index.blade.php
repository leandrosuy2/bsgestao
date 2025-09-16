@extends('dashboard.layout')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Cabeçalho -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600">Visão geral do sistema BSEstoque</p>
        </div>
        <div class="text-sm text-gray-500">
            Última atualização: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Cards de Estatísticas Principais -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total de Produtos -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total de Produtos</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($totalProducts) }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $totalCategories }} categorias</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Valor Total em Estoque -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Valor em Estoque</p>
                    <p class="text-3xl font-bold text-gray-900">R$ {{ number_format($totalStockValue, 2, ',', '.') }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $productsWithLowStock }} com estoque baixo</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Contas a Pagar -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Contas a Pagar</p>
                    <p class="text-3xl font-bold text-red-600">R$ {{ number_format($totalPayable, 2, ',', '.') }}</p>
                    <p class="text-sm text-gray-500 mt-1">Pendentes</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Contas a Receber -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Contas a Receber</p>
                    <p class="text-3xl font-bold text-green-600">R$ {{ number_format($totalReceivable, 2, ',', '.') }}</p>
                    <p class="text-sm text-gray-500 mt-1">Pendentes</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Fluxo de Caixa -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200" style="height:350px;">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Fluxo de Caixa (Últimos 30 dias)</h3>
            <canvas id="cashFlowChart" height="300"></canvas>
        </div>

        <!-- Movimentações por Mês -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200" style="height:350px;">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Movimentações de Estoque</h3>
            <canvas id="movementsChart" height="300"></canvas>
        </div>
    </div>

    <!-- Gráficos de Status Financeiro -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Status Contas a Pagar -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200" style="height:350px;">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Contas a Pagar</h3>
            <canvas id="payablesChart" height="300"></canvas>
        </div>

        <!-- Status Contas a Receber -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200" style="height:350px;">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Contas a Receber</h3>
            <canvas id="receivablesChart" height="300"></canvas>
        </div>
    </div>

    <!-- Informações em Tempo Real -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Produtos Mais Movimentados -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Produtos Mais Movimentados</h3>
            <div class="space-y-3">
                @forelse($topProducts as $product)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">{{ $product->product->name ?? 'Produto não encontrado' }}</p>
                        <p class="text-sm text-gray-500">{{ $product->total_movimentado }} unidades</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $product->product->category->name ?? 'Sem categoria' }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Nenhuma movimentação registrada</p>
                @endforelse
            </div>
        </div>

        <!-- Produtos com Estoque Baixo -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Alerta: Estoque Baixo</h3>
            <div class="space-y-3">
                @forelse($lowStockProducts as $product)
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                    <div>
                        <p class="font-medium text-gray-900">{{ $product->name }}</p>
                        <p class="text-sm text-red-600">Estoque: {{ $product->stock_quantity }} {{ $product->unit }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                            Crítico
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-green-600 text-center py-4">✓ Todos os produtos com estoque adequado</p>
                @endforelse
            </div>
        </div>

        <!-- Vencimentos Próximos -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Vencimentos (Próximos 7 dias)</h3>
            <div class="space-y-3">
                @forelse($upcomingPayables as $payable)
                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-200">
                    <div>
                        <p class="font-medium text-gray-900">{{ Str::limit($payable->descricao, 20) }}</p>
                        <p class="text-sm text-orange-600">Vence: {{ \Carbon\Carbon::parse($payable->data_vencimento)->format('d/m') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-900">R$ {{ number_format($payable->valor, 2, ',', '.') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-green-600 text-center py-4">✓ Nenhum vencimento próximo</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Movimentações Recentes -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Movimentações Recentes</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentMovements as $movement)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $movement->product->name ?? 'Produto não encontrado' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($movement->type == 'entrada')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Entrada
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Saída
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $movement->quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($movement->date)->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($movement->notes, 30) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Nenhuma movimentação registrada.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Gráfico de Fluxo de Caixa
if (document.getElementById('cashFlowChart')) {
    const cashFlowCtx = document.getElementById('cashFlowChart').getContext('2d');
    new Chart(cashFlowCtx, {
        type: 'line',
        data: {
            labels: @json($cashFlow['days']),
            datasets: [{
                label: 'Contas a Pagar',
                data: @json($cashFlow['payables']),
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4
            }, {
                label: 'Contas a Receber',
                data: @json($cashFlow['receivables']),
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        }
    });
}
// Gráfico de Movimentações
if (document.getElementById('movementsChart')) {
    const movementsCtx = document.getElementById('movementsChart').getContext('2d');
    new Chart(movementsCtx, {
        type: 'bar',
        data: {
            labels: @json($movementsByMonth->pluck('month')->map(function($month) { return \Carbon\Carbon::create()->month($month)->format('M'); })),
            datasets: [{
                label: 'Movimentações',
                data: @json($movementsByMonth->pluck('total')),
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}
// Gráfico de Contas a Pagar
if (document.getElementById('payablesChart')) {
    const payablesCtx = document.getElementById('payablesChart').getContext('2d');
    new Chart(payablesCtx, {
        type: 'doughnut',
        data: {
            labels: @json($payablesByStatus->pluck('status')),
            datasets: [{
                data: @json($payablesByStatus->pluck('valor_total')),
                backgroundColor: [
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(245, 158, 11, 0.8)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': R$ ' + context.parsed.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        }
    });
}
// Gráfico de Contas a Receber
if (document.getElementById('receivablesChart')) {
    const receivablesCtx = document.getElementById('receivablesChart').getContext('2d');
    new Chart(receivablesCtx, {
        type: 'doughnut',
        data: {
            labels: @json($receivablesByStatus->pluck('status')),
            datasets: [{
                data: @json($receivablesByStatus->pluck('valor_total')),
                backgroundColor: [
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': R$ ' + context.parsed.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        }
    });
}
</script>
@endsection
