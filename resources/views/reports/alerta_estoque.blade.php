@extends('dashboard.layout')
@section('content')
<div class="max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Alerta de Estoque Abaixo do Mínimo</h1>
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
                @forelse($produtosCriticos as $item)
                    <tr class="border-b last:border-0 bg-red-50 hover:bg-red-100 transition">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $item['produto']->name }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $item['categoria']->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $item['saldo'] }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $item['min_stock'] }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2 py-1 rounded bg-red-600 text-white text-xs font-semibold">Abaixo do mínimo</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">Nenhum produto com estoque crítico.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
