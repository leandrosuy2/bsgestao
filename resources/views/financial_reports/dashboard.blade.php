@extends('dashboard.layout')

@section('title', 'Dashboard Financeiro')

@section('content')
<div class="space-y-6">
    <!-- Cabeçalho -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard Financeiro</h1>
        <p class="text-gray-600">Visão geral das finanças da empresa</p>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Contas a Pagar - Pendente -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">A Pagar - Pendente</p>
                    <p class="text-2xl font-bold text-orange-600">R$ {{ number_format($totalPagarPendente, 2, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Contas a Pagar - Pago -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">A Pagar - Pago</p>
                    <p class="text-2xl font-bold text-green-600">R$ {{ number_format($totalPagarPago, 2, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Contas a Receber - Pendente -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">A Receber - Pendente</p>
                    <p class="text-2xl font-bold text-blue-600">R$ {{ number_format($totalReceberPendente, 2, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Contas a Receber - Recebido -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">A Receber - Recebido</p>
                    <p class="text-2xl font-bold text-green-600">R$ {{ number_format($totalReceberRecebido, 2, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Resumo Mensal -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumo do Mês Atual</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Contas a Pagar</span>
                    <span class="text-sm font-semibold text-red-600">R$ {{ number_format($pagarMesAtual, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Contas a Receber</span>
                    <span class="text-sm font-semibold text-green-600">R$ {{ number_format($receberMesAtual, 2, ',', '.') }}</span>
                </div>
                <hr>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-900">Saldo Previsto</span>
                    <span class="text-sm font-bold {{ ($receberMesAtual - $pagarMesAtual) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        R$ {{ number_format($receberMesAtual - $pagarMesAtual, 2, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Alertas</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Contas Atrasadas a Pagar</span>
                    <span class="text-sm font-semibold text-red-600">R$ {{ number_format($totalPagarAtrasado, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Contas Atrasadas a Receber</span>
                    <span class="text-sm font-semibold text-red-600">R$ {{ number_format($totalReceberAtrasado, 2, ',', '.') }}</span>
                </div>
                <hr>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Vencimentos Próximos (7 dias)</span>
                    <span class="text-sm font-semibold text-orange-600">
                        {{ $vencimentosProximosPagar->count() + $vencimentosProximosReceber->count() }} contas
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Vencimentos Próximos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Contas a Pagar - Vencimentos Próximos -->
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Vencimentos a Pagar - Próximos 7 dias</h3>
            </div>
            <div class="overflow-x-auto">
                @if($vencimentosProximosPagar->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimento</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($vencimentosProximosPagar as $payable)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payable->descricao }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">R$ {{ number_format($payable->valor, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($payable->data_vencimento)->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-6 py-8 text-center text-gray-500">
                        Nenhum vencimento próximo para contas a pagar.
                    </div>
                @endif
            </div>
        </div>

        <!-- Contas a Receber - Vencimentos Próximos -->
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Vencimentos a Receber - Próximos 7 dias</h3>
            </div>
            <div class="overflow-x-auto">
                @if($vencimentosProximosReceber->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimento</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($vencimentosProximosReceber as $receivable)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $receivable->descricao }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">R$ {{ number_format($receivable->valor, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($receivable->data_vencimento)->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-6 py-8 text-center text-gray-500">
                        Nenhum vencimento próximo para contas a receber.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Links de Ação -->
    <div class="flex justify-center gap-4">
        <a href="{{ route('payables.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Ver Contas a Pagar
        </a>
        <a href="{{ route('receivables.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Ver Contas a Receber
        </a>
    </div>
</div>
@endsection
