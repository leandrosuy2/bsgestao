@extends('dashboard.layout')

@section('content')
<div class="w-full max-w-screen-xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <a href="{{ route('benefits.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Novo Benefício
        </h2>
    </div>

    <form method="POST" action="{{ route('benefits.store') }}" class="bg-white p-6 rounded-lg shadow-md">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Nome do Benefício</label>
                <input type="text" name="name" placeholder="Nome do benefício" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('name') }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Tipo</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
                    <option value="">Selecione</option>
                    <option value="health" @selected(old('type') == 'health')>Saúde</option>
                    <option value="food" @selected(old('type') == 'food')>Alimentação</option>
                    <option value="transport" @selected(old('type') == 'transport')>Transporte</option>
                    <option value="education" @selected(old('type') == 'education')>Educação</option>
                    <option value="other" @selected(old('type') == 'other')>Outros</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Valor</label>
                <input type="number" name="value" step="0.01" min="0" placeholder="R$" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('value') }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Frequência</label>
                <select name="frequency" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
                    <option value="">Selecione</option>
                    <option value="monthly" @selected(old('frequency') == 'monthly')>Mensal</option>
                    <option value="quarterly" @selected(old('frequency') == 'quarterly')>Trimestral</option>
                    <option value="yearly" @selected(old('frequency') == 'yearly')>Anual</option>
                    <option value="one_time" @selected(old('frequency') == 'one_time')>Única vez</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Data de Início</label>
                <input type="date" name="start_date" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('start_date') }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Data de Fim</label>
                <input type="date" name="end_date" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" value="{{ old('end_date') }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
                    <option value="active" @selected(old('status') == 'active')>Ativo</option>
                    <option value="inactive" @selected(old('status') == 'inactive')>Inativo</option>
                    <option value="suspended" @selected(old('status') == 'suspended')>Suspenso</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Tributável</label>
                <select name="taxable" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
                    <option value="0" @selected(old('taxable') == '0')>Não</option>
                    <option value="1" @selected(old('taxable') == '1')>Sim</option>
                </select>
            </div>
            <div class="sm:col-span-2 lg:col-span-3 xl:col-span-4">
                <label class="block text-sm text-gray-700 font-medium mb-1">Descrição</label>
                <textarea name="description" rows="3" placeholder="Informações sobre o benefício..." class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end mt-8 gap-3">
            <a href="{{ route('benefits.index') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm border">Cancelar</a>
            <button type="submit" class="px-6 py-2 rounded bg-gray-800 text-white hover:bg-gray-900 font-semibold text-sm shadow">Salvar</button>
        </div>
    </form>
</div>
@endsection
