@extends('dashboard.layout')
@section('content')
<div class="container mx-auto py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <h1 class="text-2xl font-bold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Caixa #{{ $register->id }}
        </h1>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('caixa.movements', $register->id) }}" class="inline-flex items-center gap-1 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 font-semibold transition text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16l4-4m0 0l-4-4m4 4H8" /></svg>
                Movimentações
            </a>
            <a href="{{ route('caixa.report', $register->id) }}" class="inline-flex items-center gap-1 px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 font-semibold transition text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h4m0 0V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2h4" /></svg>
                Relatório
            </a>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded shadow p-4 flex flex-col gap-2">
            <span class="text-gray-500 text-xs">Operador</span>
            <span class="font-semibold text-gray-800">{{ $register->user->name ?? '-' }}</span>
        </div>
        <div class="bg-white rounded shadow p-4 flex flex-col gap-2">
            <span class="text-gray-500 text-xs">Aberto em</span>
            <span class="font-semibold text-gray-800">{{ $register->opened_at }}</span>
            <span class="text-gray-500 text-xs mt-2">Fechado em</span>
            <span class="font-semibold text-gray-800">{{ $register->closed_at ?? '-' }}</span>
        </div>
        <div class="bg-white rounded shadow p-4 flex flex-col gap-2">
            <span class="text-gray-500 text-xs">Status</span>
            @if($register->status == 'open')
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-800 rounded text-sm font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Aberto
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-200 text-gray-700 rounded text-sm font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    Fechado
                </span>
            @endif
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded shadow p-4 flex flex-col gap-2">
            <span class="text-gray-500 text-xs">Valor Inicial</span>
            <span class="font-bold text-lg text-green-700">R$ {{ number_format($register->initial_amount, 2, ',', '.') }}</span>
        </div>
        <div class="bg-white rounded shadow p-4 flex flex-col gap-2">
            <span class="text-gray-500 text-xs">Valor Final</span>
            <span class="font-bold text-lg text-blue-700">{{ $register->final_amount !== null ? 'R$ '.number_format($register->final_amount, 2, ',', '.') : '-' }}</span>
        </div>
    </div>
    <div class="bg-white rounded shadow p-4">
        <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7.5V6.375A2.625 2.625 0 015.625 3.75h12.75A2.625 2.625 0 0121 6.375V7.5M3 7.5v10.125A2.625 2.625 0 005.625 20.25h12.75A2.625 2.625 0 0021 17.625V7.5M3 7.5h18M7.5 11.25h9" /></svg>
            Vendas
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Desconto</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Final</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
        </tr>
    </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($register->sales as $sale)
                    <tr>
                        <td class="px-4 py-2">{{ $sale->id }}</td>
                        <td class="px-4 py-2">R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                        <td class="px-4 py-2">R$ {{ number_format($sale->discount, 2, ',', '.') }}</td>
                        <td class="px-4 py-2">R$ {{ number_format($sale->final_total, 2, ',', '.') }}</td>
                        <td class="px-4 py-2">
                            @if($sale->status == 'completed')
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Finalizada
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    Cancelada
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $sale->sold_at }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">Nenhuma venda registrada neste caixa.</td>
        </tr>
                    @endforelse
    </tbody>
</table>
        </div>
    </div>
</div>
@endsection
