@extends('dashboard.layout')

@section('title', 'Dashboard Administrativo')

@section('content')
<div class="space-y-8">
    <!-- Cabeçalho -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Administrativo</h1>
            <p class="text-gray-600">Visão geral de todas as empresas no sistema</p>
        </div>
        <div class="text-sm text-gray-500">
            Última atualização: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Cards de Estatísticas Principais -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total de Empresas -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total de Empresas</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalCompanies }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $activeCompanies }} ativas</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total de Usuários -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total de Usuários</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $activeUsers }} ativos</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Empresas em Trial -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Empresas em Trial</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $trialCompanies }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $expiredTrials }} expiradas</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Empresas Pagas -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Empresas Pagas</p>
                    <p class="text-3xl font-bold text-green-600">{{ $paidCompanies }}</p>
                    <p class="text-sm text-gray-500 mt-1">Ativas</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Empresas -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Empresas Cadastradas</h3>
            <a href="{{ route('admin.companies.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Gerenciar Empresas
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trial</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuários</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($companies as $company)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $company->name }}</div>
                            <div class="text-sm text-gray-500">{{ $company->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($company->is_active)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Ativa
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Bloqueada
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($company->trial_end && $company->trial_end instanceof \Carbon\Carbon)
                                @if($company->trial_end->isFuture())
                                    <span class="text-green-600">Ativo até {{ $company->trial_end->format('d/m/Y') }}</span>
                                @else
                                    <span class="text-red-600">Expirado em {{ $company->trial_end->format('d/m/Y') }}</span>
                                @endif
                            @else
                                <span class="text-gray-500">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $company->users_count ?? 0 }} usuários
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.companies.edit', $company) }}" class="text-blue-600 hover:text-blue-900">
                                Editar
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Nenhuma empresa cadastrada
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Estatísticas Gerais -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Status das Empresas -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Status das Empresas</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <span class="font-medium text-gray-900">Empresas Ativas</span>
                    <span class="text-2xl font-bold text-green-600">{{ $activeCompanies }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                    <span class="font-medium text-gray-900">Em Trial</span>
                    <span class="text-2xl font-bold text-orange-600">{{ $trialCompanies }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                    <span class="font-medium text-gray-900">Bloqueadas</span>
                    <span class="text-2xl font-bold text-red-600">{{ $blockedCompanies }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <span class="font-medium text-gray-900">Pagas</span>
                    <span class="text-2xl font-bold text-blue-600">{{ $paidCompanies }}</span>
                </div>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ações Rápidas</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.companies.create') }}" class="block w-full p-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center">
                    Cadastrar Nova Empresa
                </a>
                <a href="{{ route('admin.companies.index') }}" class="block w-full p-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-center">
                    Gerenciar Empresas
                </a>
                <a href="{{ route('admin.companies.index') }}?filter=expired" class="block w-full p-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-center">
                    Ver Trials Expirados
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
