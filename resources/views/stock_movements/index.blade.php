@extends('dashboard.layout')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <span class="bg-gray-800 p-2 rounded-full shadow">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
        </span>
        <h1 class="text-2xl font-bold text-gray-800">Movimentações de Estoque</h1>
    </div>
    <a href="{{ route('stock_movements.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-gray-800 to-gray-700 text-white px-5 py-2 rounded-xl hover:from-gray-900 hover:to-gray-800 transition font-semibold shadow-lg ring-1 ring-gray-900/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Nova Movimentação
    </a>
</div>

@if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-100 border border-green-200 text-green-800 rounded shadow-sm">
        {{ session('success') }}
    </div>
@endif

<div class="overflow-x-auto bg-white rounded-xl shadow-sm">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-100 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Data</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Produto</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Tipo</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Motivo</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Quantidade</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Responsável</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Observação</th>
                <th class="px-4 py-3 text-right"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $mov)
                <tr class="border-b last:border-0 hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-gray-900">{{ \Carbon\Carbon::parse($mov->date)->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $mov->product->name ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-block px-2 py-1 rounded {{ $mov->type == 'entrada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-semibold uppercase">
                            {{ ucfirst($mov->type) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ ucfirst($mov->movement_reason) }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $mov->quantity }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $mov->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $mov->notes ?? '-' }}</td>
                    <td class="px-4 py-3 text-right">
                        <form action="{{ route('stock_movements.destroy', $mov) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover esta movimentação?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 text-white bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 font-semibold px-3 py-1.5 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Remover
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">Nenhuma movimentação registrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($movements->hasPages())
    <div class="mt-6">
        {{ $movements->links() }}
    </div>
@endif
@endsection
