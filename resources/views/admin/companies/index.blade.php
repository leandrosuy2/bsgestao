@extends('dashboard.layout')

@section('title', 'Empresas')

@section('content')
@if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border-l-4 border-green-400 text-green-800 rounded">
        {{ session('success') }}
    </div>
@endif
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Empresas do Sistema</h1>
        <p class="text-gray-600">Gerencie todas as empresas cadastradas no SaaS.</p>
    </div>
    <a href="{{ route('admin.companies.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Nova Empresa
    </a>
</div>
<div class="bg-white rounded-xl shadow p-6 border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Nome</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Trial</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Pagamento</th>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($companies as $company)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 font-medium text-gray-900">{{ $company->name }}</td>
                <td class="px-4 py-2 text-gray-700">{{ $company->email }}</td>
                <td class="px-4 py-2 text-gray-700">
                    {{ $company->trial_start ? \Carbon\Carbon::parse($company->trial_start)->format('d/m/Y') : '-' }}<br>
                    até {{ $company->trial_end ? \Carbon\Carbon::parse($company->trial_end)->format('d/m/Y') : '-' }}
                </td>
                <td class="px-4 py-2">
                    @if(!$company->is_active)
                        <span class="inline-block px-2 py-1 text-xs rounded bg-red-100 text-red-800">Bloqueada</span>
                    @elseif($company->trial_end && now()->greaterThan($company->trial_end) && (!$company->paid_until || now()->greaterThan($company->paid_until)))
                        <span class="inline-block px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Trial Expirado</span>
                    @else
                        <span class="inline-block px-2 py-1 text-xs rounded bg-green-100 text-green-800">Ativa</span>
                    @endif
                </td>
                <td class="px-4 py-2 text-gray-700">
                    @if($company->paid_until && now()->lessThanOrEqualTo($company->paid_until))
                        <span class="inline-block px-2 py-1 text-xs rounded bg-green-100 text-green-800">Pago até {{ \Carbon\Carbon::parse($company->paid_until)->format('d/m/Y') }}</span>
                    @else
                        <span class="inline-block px-2 py-1 text-xs rounded bg-red-100 text-red-800">Não pago</span>
                    @endif
                </td>
                <td class="px-4 py-2">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.companies.edit', $company) }}" class="text-blue-600 hover:text-blue-900" title="Editar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <form action="{{ route('admin.companies.toggleActive', $company) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="{{ $company->is_active ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900' }}" title="{{ $company->is_active ? 'Desativar' : 'Ativar' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </form>
                        <form action="{{ route('admin.companies.liberarPagamento', $company) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-900" title="Liberar Pagamento">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </button>
                        </form>
                        <form action="{{ route('admin.companies.renovarTrial', $company) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-purple-600 hover:text-purple-900" title="Renovar Trial">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
