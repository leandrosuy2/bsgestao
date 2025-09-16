@extends('dashboard.layout')
@section('title', 'Benefícios')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Benefícios</h1>
        <a href="{{ route('benefits.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Novo Benefício</a>
    </div>
    <div class="bg-white rounded-lg shadow-sm border overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Colaborador</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Início</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fim</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($benefits as $beneficio)
                <tr>
                    <td class="px-6 py-4">{{ $beneficio->employee->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $beneficio->tipo }}</td>
                    <td class="px-6 py-4">R$ {{ number_format($beneficio->valor, 2, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        @if($beneficio->status == 'ativo')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Ativo</span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-800">Inativo</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($beneficio->data_inicio)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">{{ $beneficio->data_fim ? \Carbon\Carbon::parse($beneficio->data_fim)->format('d/m/Y') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
