@extends('dashboard.layout')

@section('title', 'Detalhes do Vendedor')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Vendedor: {{ $seller->name }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('sellers.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Voltar
            </a>
            
            <a href="{{ route('sellers.edit', $seller) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>

            <a href="{{ route('sellers.commissions') }}?seller_id={{ $seller->id }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Relatório de Comissões
            </a>
        </div>
    </div>

    <!-- Status do Vendedor -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold mb-2">Status do Vendedor</h2>
                <div class="flex items-center space-x-4">
                    @if($seller->active)
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                            Ativo
                        </span>
                        <span class="text-sm text-gray-500">
                            Taxa de Comissão: <span class="font-semibold text-gray-900">{{ number_format($seller->commission_rate, 1) }}%</span>
                        </span>
                    @else
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                            Inativo
                        </span>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Cadastrado em</p>
                <p class="text-lg font-semibold text-gray-900">
                    {{ $seller->created_at->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Informações e Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Informações Básicas -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Informações Básicas</h2>
            <div class="space-y-4">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nome Completo</label>
                        <p class="text-base text-gray-900">{{ $seller->name }}</p>
                    </div>
                    
                    @if($seller->document)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Documento</label>
                        <p class="text-base text-gray-900">{{ $seller->document }}</p>
                    </div>
                    @endif

                    @if($seller->phone)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Telefone</label>
                        <p class="text-base text-gray-900">{{ $seller->phone }}</p>
                    </div>
                    @endif

                    @if($seller->email)
                    <div>
                        <label class="text-sm font-medium text-gray-500">E-mail</label>
                        <p class="text-base text-gray-900">{{ $seller->email }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total em Vendas</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-2">
                            R$ {{ number_format($seller->total_sales ?? 0, 2, ',', '.') }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $seller->sales_count ?? 0 }} vendas realizadas</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total em Comissões</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-2">
                            R$ {{ number_format($seller->total_commission ?? 0, 2, ',', '.') }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">{{ number_format($seller->commission_rate, 1) }}% de comissão</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimas Vendas -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Últimas Vendas</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Valor</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Comissão</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($seller->sales()->latest()->take(5)->get() as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $sale->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $sale->customer->name ?? 'Cliente não identificado' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                            R$ {{ number_format($sale->total, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right font-medium">
                            R$ {{ number_format($sale->total * ($seller->commission_rate / 100), 2, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                            Nenhuma venda registrada ainda.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
