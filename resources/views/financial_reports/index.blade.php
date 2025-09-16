@extends('dashboard.layout')

@section('title', 'Relatórios Financeiros')

@section('content')
<div class="space-y-6">
    <!-- Cabeçalho -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Relatórios Financeiros</h1>
        <p class="text-gray-600">Análises e relatórios do módulo financeiro</p>
    </div>

    <!-- Cards de Acesso Rápido -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="{{ route('financial-reports.dashboard') }}" class="bg-white p-6 rounded-lg shadow-sm border hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Dashboard Financeiro</h3>
                    <p class="text-sm text-gray-600">Visão geral das finanças</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('financial-reports.fluxo-caixa') }}" class="bg-white p-6 rounded-lg shadow-sm border hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Fluxo de Caixa</h3>
                    <p class="text-sm text-gray-600">Análise temporal</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('financial-reports.categorias') }}" class="bg-white p-6 rounded-lg shadow-sm border hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Por Categorias</h3>
                    <p class="text-sm text-gray-600">Análise por tipo</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('financial-reports.pessoas') }}" class="bg-white p-6 rounded-lg shadow-sm border hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Por Pessoas</h3>
                    <p class="text-sm text-gray-600">Análise por cliente/fornecedor</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    <!-- Links Rápidos -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ações Rápidas</h3>
            <div class="space-y-3">
                <a href="{{ route('payables.create') }}" class="flex items-center gap-3 p-3 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nova Conta a Pagar
                </a>
                <a href="{{ route('receivables.create') }}" class="flex items-center gap-3 p-3 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nova Conta a Receber
                </a>
                <a href="{{ route('payables.index') }}" class="flex items-center gap-3 p-3 rounded-lg bg-orange-50 text-orange-700 hover:bg-orange-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Ver Contas a Pagar
                </a>
                <a href="{{ route('receivables.index') }}" class="flex items-center gap-3 p-3 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Ver Contas a Receber
                </a>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Contas Pendentes</span>
                    <span class="text-sm text-gray-900">Verificar status</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Vencimentos Próximos</span>
                    <span class="text-sm text-gray-900">Próximos 7 dias</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Relatórios Mensais</span>
                    <span class="text-sm text-gray-900">Análise completa</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Exportação</span>
                    <span class="text-sm text-gray-900">PDF/Excel</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
