@extends('dashboard.layout')
@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Histórico de Movimentações</h1>
    <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
        <div>
            <label class="block text-xs text-gray-500 font-medium mb-1">Produto</label>
            <select name="product_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm">
                <option value="">Todos</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 font-medium mb-1">Categoria</label>
            <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm">
                <option value="">Todas</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 font-medium mb-1">Tipo</label>
            <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm">
                <option value="">Todos</option>
                <option value="entrada" @selected(request('type') == 'entrada')>Entrada</option>
                <option value="saida" @selected(request('type') == 'saida')>Saída</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 font-medium mb-1">Data inicial</label>
            <input type="date" name="date_start" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" value="{{ request('date_start') }}">
        </div>
        <div>
            <label class="block text-xs text-gray-500 font-medium mb-1">Data final</label>
            <input type="date" name="date_end" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" value="{{ request('date_end') }}">
        </div>
        <div class="md:col-span-5 flex justify-end mt-2">
            <button type="submit" class="px-4 py-2 rounded bg-gray-800 text-white hover:bg-gray-900 font-semibold text-sm shadow">Filtrar</button>
        </div>
    </form>
    <div class="overflow-x-auto bg-white rounded-xl shadow-sm">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Data</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Produto</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Categoria</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Tipo</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Motivo</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Quantidade</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Responsável</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $mov)
                    <tr class="border-b last:border-0 hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-900">{{ \Carbon\Carbon::parse($mov->date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $mov->product->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $mov->product->category->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2 py-1 rounded {{ $mov->type == 'entrada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-semibold uppercase">
                                {{ ucfirst($mov->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ ucfirst($mov->movement_reason) }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $mov->quantity }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $mov->user->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">Nenhuma movimentação encontrada.</td>
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
</div>
@endsection
