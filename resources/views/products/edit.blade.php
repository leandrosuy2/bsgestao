@extends('dashboard.layout')

@section('content')
<div class="w-full max-w-screen-xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <a href="{{ route('products.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Editar Produto
        </h2>
    </div>

    <form method="POST" action="{{ route('products.update', $product) }}" class="bg-white p-6 rounded-lg shadow-md">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Nome</label>
                <input type="text" name="name" placeholder="Nome do produto" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('name', $product->name) }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Código Interno</label>
                <input type="text" name="internal_code" placeholder="Código único" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('internal_code', $product->internal_code) }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Categoria</label>
                <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
                    <option value="">Selecione</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Unidade</label>
                <input type="text" name="unit" placeholder="Ex: kg, un" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('unit', $product->unit) }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Preço de Custo</label>
                <input type="number" step="0.01" name="cost_price" placeholder="R$" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('cost_price', $product->cost_price) }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Preço de Venda</label>
                <input type="number" step="0.01" name="sale_price" placeholder="R$" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('sale_price', $product->sale_price) }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Estoque Mínimo</label>
                <input type="number" name="min_stock" placeholder="Qtd mínima" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('min_stock', $product->min_stock) }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Estoque Atual</label>
                <input type="number" name="stock_quantity" placeholder="Qtd atual" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('stock_quantity', $product->stock_quantity) }}">
            </div>
            <div class="sm:col-span-2 lg:col-span-3 xl:col-span-4">
                <label class="block text-sm text-gray-700 font-medium mb-1">Descrição</label>
                <textarea name="description" rows="3" placeholder="Informações adicionais..." class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm">{{ old('description', $product->description) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end mt-8 gap-3">
            <a href="{{ route('products.index') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm border">Cancelar</a>
            <button type="submit" class="px-6 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 font-semibold text-sm shadow transition-colors">Salvar</button>
        </div>
    </form>
</div>
@endsection
