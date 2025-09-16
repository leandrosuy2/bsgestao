@extends('dashboard.layout')

@section('content')
<div class="w-full max-w-screen-xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <a href="{{ route('payrolls.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Nova Folha de Pagamento
        </h2>
    </div>

    <form method="POST" action="{{ route('payrolls.store') }}" class="bg-white p-6 rounded-lg shadow-md">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Mês</label>
                <select name="month" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
                    <option value="">Selecione</option>
                    <option value="01" @selected(old('month') == '01')>Janeiro</option>
                    <option value="02" @selected(old('month') == '02')>Fevereiro</option>
                    <option value="03" @selected(old('month') == '03')>Março</option>
                    <option value="04" @selected(old('month') == '04')>Abril</option>
                    <option value="05" @selected(old('month') == '05')>Maio</option>
                    <option value="06" @selected(old('month') == '06')>Junho</option>
                    <option value="07" @selected(old('month') == '07')>Julho</option>
                    <option value="08" @selected(old('month') == '08')>Agosto</option>
                    <option value="09" @selected(old('month') == '09')>Setembro</option>
                    <option value="10" @selected(old('month') == '10')>Outubro</option>
                    <option value="11" @selected(old('month') == '11')>Novembro</option>
                    <option value="12" @selected(old('month') == '12')>Dezembro</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Ano</label>
                <input type="number" name="year" min="2020" max="2030" placeholder="2024" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('year', date('Y')) }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Total de Funcionários</label>
                <input type="number" name="total_employees" min="1" placeholder="Quantidade" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('total_employees') }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Total de Salários</label>
                <input type="number" name="total_salary" step="0.01" min="0" placeholder="R$" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('total_salary') }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Total de Benefícios</label>
                <input type="number" name="total_benefits" step="0.01" min="0" placeholder="R$" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" value="{{ old('total_benefits', 0) }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Total de Descontos</label>
                <input type="number" name="total_deductions" step="0.01" min="0" placeholder="R$" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" value="{{ old('total_deductions', 0) }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Total Líquido</label>
                <input type="number" name="net_total" step="0.01" min="0" placeholder="R$" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('net_total') }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
                    <option value="draft" @selected(old('status') == 'draft')>Rascunho</option>
                    <option value="processing" @selected(old('status') == 'processing')>Processando</option>
                    <option value="completed" @selected(old('status') == 'completed')>Concluída</option>
                    <option value="paid" @selected(old('status') == 'paid')>Paga</option>
                </select>
            </div>
            <div class="sm:col-span-2 lg:col-span-3 xl:col-span-4">
                <label class="block text-sm text-gray-700 font-medium mb-1">Observações</label>
                <textarea name="notes" rows="3" placeholder="Informações adicionais..." class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end mt-8 gap-3">
            <a href="{{ route('payrolls.index') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm border">Cancelar</a>
            <button type="submit" class="px-6 py-2 rounded bg-gray-800 text-white hover:bg-gray-900 font-semibold text-sm shadow">Salvar</button>
        </div>
    </form>
</div>
@endsection
