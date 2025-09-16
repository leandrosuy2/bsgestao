@extends('dashboard.layout')

@section('title', 'Contas a Pagar')

@section('content')
<div class="space-y-6">
    <!-- Cabeçalho -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Contas a Pagar</h1>
            <p class="text-gray-600">Gerencie as obrigações financeiras da empresa</p>
        </div>
        <a href="{{ route('payables.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nova Conta
        </a>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pendente</p>
                    <p class="text-2xl font-bold text-orange-600">R$ {{ number_format($totalPendente, 2, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pago</p>
                    <p class="text-2xl font-bold text-green-600">R$ {{ number_format($totalPago, 2, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Atrasado</p>
                    <p class="text-2xl font-bold text-red-600">R$ {{ number_format($totalAtrasado, 2, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white p-6 rounded-lg shadow-sm border">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm">
                    <option value="">Todos</option>
                    <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="pago" {{ request('status') == 'pago' ? 'selected' : '' }}>Pago</option>
                    <option value="atrasado" {{ request('status') == 'atrasado' ? 'selected' : '' }}>Atrasado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Pessoa</label>
                <input type="text" name="pessoa" value="{{ request('pessoa') }}" placeholder="Nome da pessoa" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Categoria</label>
                <input type="text" name="categoria" value="{{ request('categoria') }}" placeholder="Categoria" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Data Início</label>
                <input type="date" name="date_start" value="{{ request('date_start') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Data Fim</label>
                <input type="date" name="date_end" value="{{ request('date_end') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm">
            </div>
            <div class="sm:col-span-2 lg:col-span-3 xl:col-span-5 flex gap-3">
                <button type="submit" class="px-6 py-2 rounded bg-gray-800 text-white hover:bg-gray-900 font-semibold text-sm shadow">Filtrar</button>
                <a href="{{ route('payables.index') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm border">Limpar</a>
            </div>
        </form>
    </div>

    <!-- Tabela -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pessoa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payables as $payable)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $payable->descricao }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $payable->pessoa }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $payable->categoria }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">R$ {{ number_format($payable->valor, 2, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($payable->data_vencimento)->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($payable->status == 'pendente')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pendente
                                </span>
                            @elseif($payable->status == 'pago')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Pago
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Atrasado
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                @if($payable->status == 'pendente')
                                    <form method="POST" action="{{ route('payables.marcar-pago', $payable) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-green-600 hover:text-green-900" title="Marcar como pago">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('payables.edit', $payable) }}" class="text-blue-600 hover:text-blue-900" title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('payables.destroy', $payable) }}" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir esta conta?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Excluir">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Nenhuma conta a pagar encontrada.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        @if($payables->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $payables->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
