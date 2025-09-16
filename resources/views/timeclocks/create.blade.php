@extends('dashboard.layout')

@section('content')
<div class="w-full max-w-screen-xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <a href="{{ route('timeclocks.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Novo Registro de Ponto
        </h2>
    </div>

    <form method="POST" action="{{ route('timeclocks.store') }}" class="bg-white p-6 rounded-lg shadow-md">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Funcionário</label>
                <select name="employee_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
                    <option value="">Selecione</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Data</label>
                <input type="date" name="date" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('date') }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Entrada</label>
                <input type="time" name="clock_in" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('clock_in') }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Saída</label>
                <input type="time" name="clock_out" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" value="{{ old('clock_out') }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Total de Horas</label>
                <input type="number" name="total_hours" step="0.5" min="0" placeholder="0.0" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" value="{{ old('total_hours') }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
                    <option value="present" @selected(old('status') == 'present')>Presente</option>
                    <option value="absent" @selected(old('status') == 'absent')>Ausente</option>
                    <option value="late" @selected(old('status') == 'late')>Atrasado</option>
                    <option value="half_day" @selected(old('status') == 'half_day')>Meio Período</option>
                </select>
            </div>
        </div>

        <div class="flex justify-end mt-8 gap-3">
            <a href="{{ route('timeclocks.index') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm border">Cancelar</a>
            <button type="submit" class="px-6 py-2 rounded bg-gray-800 text-white hover:bg-gray-900 font-semibold text-sm shadow">Salvar</button>
        </div>
    </form>
</div>
@endsection
