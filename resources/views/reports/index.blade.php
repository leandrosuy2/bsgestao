@extends('dashboard.layout')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
        <svg class="w-7 h-7 text-gray-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17v-2a4 4 0 014-4h10a4 4 0 014 4v2M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75"/></svg>
        Relatórios do Estoque
    </h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('reports.estoque_atual') }}" class="block p-5 bg-white rounded-lg shadow hover:shadow-lg border border-gray-100 hover:border-gray-300 transition">
            <div class="font-semibold text-gray-800 mb-1">Estoque atual por produto e categoria</div>
            <div class="text-gray-500 text-sm">Veja o saldo de cada produto, filtrando por categoria, e identifique rapidamente estoques abaixo do mínimo.</div>
        </a>
        <a href="{{ route('reports.historico_movimentacoes') }}" class="block p-5 bg-white rounded-lg shadow hover:shadow-lg border border-gray-100 hover:border-gray-300 transition">
            <div class="font-semibold text-gray-800 mb-1">Histórico de movimentações</div>
            <div class="text-gray-500 text-sm">Consulte todas as entradas e saídas por período, produto, categoria e tipo de movimentação.</div>
        </a>
        <a href="{{ route('reports.alerta_estoque') }}" class="block p-5 bg-white rounded-lg shadow hover:shadow-lg border border-gray-100 hover:border-gray-300 transition">
            <div class="font-semibold text-gray-800 mb-1">Alerta de estoque abaixo do mínimo</div>
            <div class="text-gray-500 text-sm">Veja rapidamente quais produtos estão com estoque crítico e precisam de atenção.</div>
        </a>
        <a href="{{ route('reports.produtos_mais_movimentados') }}" class="block p-5 bg-white rounded-lg shadow hover:shadow-lg border border-gray-100 hover:border-gray-300 transition">
            <div class="font-semibold text-gray-800 mb-1">Produtos mais movimentados</div>
            <div class="text-gray-500 text-sm">Ranking dos produtos com maior volume de movimentações em um período.</div>
        </a>
    </div>
</div>
@endsection
