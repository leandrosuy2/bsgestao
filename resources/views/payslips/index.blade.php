@extends('dashboard.layout')
@section('title', 'Holerites')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Holerites</h1>
        <a href="{{ route('payslips.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Novo Holerite</a>
    </div>
    <div class="bg-white rounded-lg shadow-sm border overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Colaborador</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Competência</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Arquivo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data Geração</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Observação</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($payslips as $holerite)
                <tr>
                    <td class="px-6 py-4">{{ $holerite->employee->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $holerite->competencia }}</td>
                    <td class="px-6 py-4">
                        <a href="/{{ $holerite->arquivo }}" target="_blank" class="text-blue-600 hover:underline">Ver PDF</a>
                    </td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($holerite->data_geracao)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">{{ $holerite->observacao }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
