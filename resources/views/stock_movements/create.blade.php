@extends('dashboard.layout')

@section('content')
<div class="w-full max-w-screen-xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <a href="{{ route('stock_movements.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Nova Movimentação
        </h2>
    </div>

    <form method="POST" action="{{ route('stock_movements.store') }}" class="bg-white p-6 rounded-lg shadow-md">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Produto</label>
                <select name="product_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
                    <option value="">Selecione</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Tipo</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
                    <option value="">Selecione</option>
                    <option value="entrada" @selected(old('type') == 'entrada')>Entrada</option>
                    <option value="saida" @selected(old('type') == 'saida')>Saída</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Motivo</label>
                <select name="movement_reason" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
                    <option value="">Selecione</option>
                    <option value="compra" @selected(old('movement_reason') == 'compra')>Compra</option>
                    <option value="devolucao" @selected(old('movement_reason') == 'devolucao')>Devolução</option>
                    <option value="ajuste" @selected(old('movement_reason') == 'ajuste')>Ajuste Manual</option>
                    <option value="venda" @selected(old('movement_reason') == 'venda')>Venda</option>
                    <option value="perda" @selected(old('movement_reason') == 'perda')>Perda</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Quantidade</label>
                <input type="number" name="quantity" min="1" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('quantity') }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Data</label>
                <input type="date" name="date" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('date', date('Y-m-d')) }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Responsável</label>
                <select name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
                    <option value="">Selecione</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2 lg:col-span-3 xl:col-span-4">
                <label class="block text-sm text-gray-700 font-medium mb-1">Observação</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="flex justify-end mt-8 gap-3">
            <a href="{{ route('stock_movements.index') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm border">Cancelar</a>
            <button type="submit" class="px-6 py-2 rounded bg-gray-800 text-white hover:bg-gray-900 font-semibold text-sm shadow">Salvar</button>
        </div>
    </form>
</div>
@endsection