@extends('dashboard.layout')
@section('title', 'Folha de Pagamento')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Folha de Pagamento</h1>
        <a href="{{ route('payrolls.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Nova Folha</a>
    </div>
    <div class="bg-white rounded-lg shadow-sm border overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Colaborador</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Competência</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salário Base</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descontos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Adicionais</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Líquido</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pagamento</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($payrolls as $folha)
                <tr>
                    <td class="px-6 py-4">{{ $folha->employee->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $folha->competencia }}</td>
                    <td class="px-6 py-4">R$ {{ number_format($folha->salario_base, 2, ',', '.') }}</td>
                    <td class="px-6 py-4">R$ {{ number_format($folha->descontos, 2, ',', '.') }}</td>
                    <td class="px-6 py-4">R$ {{ number_format($folha->adicionais, 2, ',', '.') }}</td>
                    <td class="px-6 py-4">R$ {{ number_format($folha->total_liquido, 2, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        @if($folha->status == 'pago')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Pago</span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendente</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $folha->data_pagamento ? \Carbon\Carbon::parse($folha->data_pagamento)->format('d/m/Y') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
