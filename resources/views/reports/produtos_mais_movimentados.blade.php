@extends('dashboard.layout')
@section('content')
<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Produtos Mais Movimentados</h1>
    <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
        <div>
            <label class="block text-xs text-gray-500 font-medium mb-1">Data inicial</label>
            <input type="date" name="date_start" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" value="{{ $dateStart }}">
        </div>
        <div>
            <label class="block text-xs text-gray-500 font-medium mb-1">Data final</label>
            <input type="date" name="date_end" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" value="{{ $dateEnd }}">
        </div>
        <div class="md:col-span-5 flex justify-end mt-2">
            <button type="submit" class="px-4 py-2 rounded bg-gray-800 text-white hover:bg-gray-900 font-semibold text-sm shadow">Filtrar</button>
        </div>
    </form>
    <div class="overflow-x-auto bg-white rounded-xl shadow-sm">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-600 uppercase">#</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Produto</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Categoria</th>
                    <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Total Movimentado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $i => $mov)
                    <tr class="border-b last:border-0 hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-700 font-bold">{{ $i+1 }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $mov->product->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $mov->product->category->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $mov->total }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-400">Nenhum produto movimentado no período.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
