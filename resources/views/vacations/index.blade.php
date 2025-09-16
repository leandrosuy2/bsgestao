@extends('dashboard.layout')
@section('title', 'Férias')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Férias</h1>
        <a href="{{ route('vacations.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Nova Solicitação</a>
    </div>
    <div class="bg-white rounded-lg shadow-sm border overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Colaborador</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Início</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fim</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dias</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Solicitação</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aprovação</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($vacations as $ferias)
                <tr>
                    <td class="px-6 py-4">{{ $ferias->employee->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($ferias->data_inicio)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($ferias->data_fim)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">{{ $ferias->dias }}</td>
                    <td class="px-6 py-4">
                        @if($ferias->status == 'aprovada')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aprovada</span>
                        @elseif($ferias->status == 'negada')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Negada</span>
                        @elseif($ferias->status == 'gozada')
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Gozada</span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Solicitada</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($ferias->data_solicitacao)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">{{ $ferias->data_aprovacao ? \Carbon\Carbon::parse($ferias->data_aprovacao)->format('d/m/Y') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
