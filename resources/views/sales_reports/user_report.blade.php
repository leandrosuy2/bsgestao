@extends('dashboard.layout')

@section('title', 'Relatório de Vendas - ' . $user->name)

@section('content')
<style>
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
        max-width: 100%;
        overflow: hidden;
    }
    
    .chart-container canvas {
        max-width: 100% !important;
        max-height: 300px !important;
    }
    
    /* Prevenir que os gráficos cresçam indefinidamente */
    #salesByDayChart,
    #salesByPaymentChart {
        max-width: 100% !important;
        max-height: 300px !important;
        width: 100% !important;
        height: 300px !important;
    }
</style>
<div class="space-y-6">
    <!-- Cabeçalho do Relatório -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Relatório de Vendas</h1>
                <p class="text-gray-600">{{ $periods['label'] }}</p>
                <div class="mt-2 text-sm text-gray-500">
                    <p><strong>Usuário:</strong> {{ $user->name }} ({{ $user->email }})</p>
                    <p><strong>Empresa:</strong> {{ $user->company->name ?? 'N/A' }}</p>
                    <p><strong>Gerado em:</strong> {{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <div class="text-right">
                <a href="{{ route('sales-reports.user', ['user_email' => $user->email, 'start_date' => $periods['start']->format('Y-m-d'), 'end_date' => $periods['end']->format('Y-m-d'), 'format' => 'pdf']) }}" 
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
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total de Vendas</p>
                    <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($totalSales, 2, ',', '.') }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Número de Vendas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalSalesCount) }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Ticket Médio</p>
                    <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($averageTicket, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-full">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Clientes Atendidos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $salesByCustomer->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Vendas por Dia -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendas por Dia</h3>
            @if($salesData['byDay']->count() > 0)
                <div class="chart-container">
                    <canvas id="salesByDayChart"></canvas>
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Nenhuma venda registrada no período</p>
            @endif
        </div>

        <!-- Vendas por Forma de Pagamento -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendas por Forma de Pagamento</h3>
            @if($salesData['byPayment']->count() > 0)
                <div class="chart-container">
                    <canvas id="salesByPaymentChart"></canvas>
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Nenhuma venda registrada no período</p>
            @endif
        </div>
    </div>

    <!-- Vendas por Cliente -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendas por Cliente</h3>
        
        @if($salesByCustomer->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total de Vendas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número de Vendas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket Médio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% do Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($salesByCustomer as $customer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $customer['customer'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">R$ {{ number_format($customer['total'], 2, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $customer['count'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">R$ {{ number_format($customer['total'] / $customer['count'], 2, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $totalSales > 0 ? number_format(($customer['total'] / $totalSales) * 100, 1) : 0 }}%
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-8">Nenhuma venda registrada no período</p>
        @endif
    </div>

    <!-- Detalhamento por Forma de Pagamento -->
    @if($salesData['byPayment']->count() > 0)
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Detalhamento por Forma de Pagamento</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($salesData['byPayment'] as $payment)
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-900 capitalize">{{ $payment['payment_mode'] }}</h4>
                    <p class="text-2xl font-bold text-blue-600">R$ {{ number_format($payment['total'], 2, ',', '.') }}</p>
                    <p class="text-sm text-gray-500">{{ $payment['count'] }} vendas</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@if($salesData['byDay']->count() > 0 || $salesData['byPayment']->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Vendas por Dia
    @if($salesData['byDay']->count() > 0)
    const salesByDayElement = document.getElementById('salesByDayChart');
    if (salesByDayElement) {
        const salesByDayCtx = salesByDayElement.getContext('2d');
        new Chart(salesByDayCtx, {
            type: 'line',
            data: {
                labels: @json($salesData['byDay']->map(function($item) { return \Carbon\Carbon::parse($item['date'])->format('d/m'); })),
                datasets: [{
                    label: 'Vendas (R$)',
                    data: @json($salesData['byDay']->map(function($item) { return $item['total']; })),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
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
    @endif

    // Gráfico de Vendas por Forma de Pagamento
    @if($salesData['byPayment']->count() > 0)
    const salesByPaymentElement = document.getElementById('salesByPaymentChart');
    if (salesByPaymentElement) {
        const salesByPaymentCtx = salesByPaymentElement.getContext('2d');
        new Chart(salesByPaymentCtx, {
            type: 'doughnut',
            data: {
                labels: @json($salesData['byPayment']->map(function($payment) { return ucfirst($payment['payment_mode']); })),
                datasets: [{
                    data: @json($salesData['byPayment']->map(function($payment) { return $payment['total']; })),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)'
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
    @endif
});
</script>
@endif
@endsection

