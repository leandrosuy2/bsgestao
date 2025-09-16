@extends('dashboard.layout')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
        <svg class="w-7 h-7 text-gray-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17v-2a4 4 0 014-4h10a4 4 0 014 4v2M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75"/></svg>
        Estoque Atual por Produto e Categoria
    </h1>
    <form method="GET" class="mb-6 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 font-medium mb-1">Categoria</label>
            <select name="category_id" class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm">
                <option value="">Todas</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected((string)($categoryId ?? '') === (string)$cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 rounded bg-gray-800 text-white hover:bg-gray-900 font-semibold text-sm shadow">Filtrar</button>
    </form>
    <div class="overflow-x-auto bg-white rounded-xl shadow-sm">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Produto</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Categoria</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Estoque Atual</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Estoque Mínimo</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Situação</th>
                </tr>
            </thead>
            <tbody>
                @forelse($estoques as $item)
                    <tr class="border-b last:border-0 hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $item['produto']->name }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $item['categoria']->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $item['saldo'] }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $item['min_stock'] }}</td>
                        <td class="px-4 py-3">
                            @if($item['saldo'] < $item['min_stock'])
                                <span class="inline-block px-2 py-1 rounded bg-red-100 text-red-800 text-xs font-semibold">Abaixo do mínimo</span>
                            @else
                                <span class="inline-block px-2 py-1 rounded bg-green-100 text-green-800 text-xs font-semibold">OK</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">Nenhum produto encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
