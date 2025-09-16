@extends('dashboard.layout')
@section('title', 'Controle de Ponto')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Controle de Ponto</h1>
        <a href="{{ route('timeclocks.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Novo Registro</a>
    </div>
    <div class="bg-white rounded-lg shadow-sm border overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Colaborador</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entrada</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Saída</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Intervalo Início</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Intervalo Fim</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Observação</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($timeclocks as $ponto)
                <tr>
                    <td class="px-6 py-4">{{ $ponto->employee->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($ponto->data)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">{{ $ponto->hora_entrada }}</td>
                    <td class="px-6 py-4">{{ $ponto->hora_saida }}</td>
                    <td class="px-6 py-4">{{ $ponto->hora_intervalo_inicio }}</td>
                    <td class="px-6 py-4">{{ $ponto->hora_intervalo_fim }}</td>
                    <td class="px-6 py-4">{{ $ponto->observacao }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
