@extends('dashboard.layout')

@section('title', 'Vendedores')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Vendedores</h1>
        <div class="flex gap-2">
            <a href="{{ route('sellers.commissions') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Relatório de Comissões
            </a>
            <a href="{{ route('sellers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Novo Vendedor
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" action="{{ route('sellers.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                <input type="text" name="name" value="{{ request('name') }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Nome do vendedor">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Ativos</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inativos</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Comissão</label>
                <select name="commission" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas</option>
                    <option value="asc" {{ request('commission') == 'asc' ? 'selected' : '' }}>Menor Comissão</option>
                    <option value="desc" {{ request('commission') == 'desc' ? 'selected' : '' }}>Maior Comissão</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    Filtrar
                </button>
                <a href="{{ route('sellers.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Cards Resumo -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total de Vendedores</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-2">{{ $sellers->total() }}</h3>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-600">Vendedores Ativos</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-2">{{ $sellers->where('active', true)->count() }}</h3>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-600">Média de Comissão</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($sellers->avg('commission_rate'), 1) }}%</h3>
                </div>
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

        <!-- Lista de Vendedores -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            @if($sellers->count() > 0)
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome / Documento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contato</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Comissão</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Vendas</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sellers as $seller)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $seller->name }}</div>
                                    @if($seller->document)
                                        <div class="text-xs text-gray-500">{{ $seller->document }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($seller->phone)
                                        <div class="text-sm text-gray-900">{{ $seller->phone }}</div>
                                    @endif
                                    @if($seller->email)
                                        <div class="text-xs text-gray-500">{{ $seller->email }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-medium {{ $seller->commission_rate >= 5 ? 'text-green-600' : 'text-gray-900' }}">
                                        {{ number_format($seller->commission_rate, 1) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($seller->active)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Ativo
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inativo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-sm text-gray-900">{{ $seller->sales_count ?? 0 }}</div>
                                    <div class="text-xs text-gray-500">Vendas</div>
                                </td>
                                <td class="px-6 py-4 text-center space-x-1">
                                    <a href="{{ route('sellers.show', $seller) }}" 
                                       class="inline-flex items-center p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100"
                                       title="Visualizar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('sellers.edit', $seller) }}" 
                                       class="inline-flex items-center p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100"
                                       title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('sellers.destroy', $seller) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Tem certeza que deseja excluir este vendedor?')"
                                                class="inline-flex items-center p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100"
                                                title="Excluir">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-6 text-center text-gray-500">
                    Nenhum vendedor cadastrado.
                </div>
            @endif

            @if($sellers->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $sellers->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
    </div>
