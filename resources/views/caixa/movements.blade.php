@extends('dashboard.layout')
@section('content')
<div class="container mx-auto py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <h1 class="text-2xl font-bold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16l4-4m0 0l-4-4m4 4H8" /></svg>
            Movimentações do Caixa #{{ $register->id }}
        </h1>
        <a href="{{ route('caixa.show', $register->id) }}" class="inline-flex items-center gap-1 px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 font-semibold transition text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Voltar ao Caixa
        </a>
    </div>
    <div class="bg-white rounded shadow p-4 mb-8">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
        <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
        </tr>
    </thead>
                <tbody class="divide-y divide-gray-200">
        @foreach($movements as $mov)
        <tr>
                        <td class="px-4 py-2">{{ $mov->id }}</td>
                        <td class="px-4 py-2">
                            @if($mov->type == 'in')
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Suprimento
                                </span>
                            @elseif($mov->type == 'out')
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    Sangria
                                </span>
                            @elseif($mov->type == 'sale')
                                @if(str_contains($mov->description, '(NF)'))
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-semibold">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        Venda c/ NFe
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-semibold">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7.5V6.375A2.625 2.625 0 015.625 3.75h12.75A2.625 2.625 0 0121 6.375V7.5M3 7.5v10.125A2.625 2.625 0 005.625 20.25h12.75A2.625 2.625 0 0021 17.625V7.5M3 7.5h18M7.5 11.25h9" /></svg>
                                        Venda s/ NFe
                                    </span>
                                @endif
                @endif
            </td>
                        <td class="px-4 py-2">R$ {{ number_format($mov->amount, 2, ',', '.') }}</td>
                        <td class="px-4 py-2">{{ $mov->description }}</td>
                        <td class="px-4 py-2">{{ $mov->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
        </div>
    </div>
@if($register->status == 'open')
    <div class="bg-white rounded shadow p-6 max-w-lg mx-auto">
        <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Nova Movimentação
        </h3>
        <form action="{{ route('caixa.movements.store', $register->id) }}" method="POST" class="space-y-4">
    @csrf
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                    <div class="relative">
                        <select name="type" id="type" class="form-select w-full pl-10 pr-3 py-2 border rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="">Selecione</option>
                <option value="in">Suprimento</option>
                <option value="out">Sangria</option>
            </select>
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        </span>
                    </div>
                </div>
                <div class="flex-1">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Valor</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" /></svg>
                        </span>
                        <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="pl-10 pr-3 py-2 border rounded w-full focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Digite o valor" required>
                    </div>
        </div>
        </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                <input type="text" name="description" id="description" class="form-input w-full px-3 py-2 border rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Descrição da movimentação">
        </div>
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 rounded bg-green-600 text-white font-semibold hover:bg-green-700 transition">Registrar</button>
        </div>
        </form>
    </div>
@endif
</div>
@endsection
