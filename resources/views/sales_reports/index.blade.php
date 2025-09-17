@extends('dashboard.layout')

@section('title', 'Relatório de Vendas por Usuário')

@section('content')
<div class="space-y-6">
    <!-- Cabeçalho -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Relatório de Vendas por Usuário</h1>
            <p class="text-gray-600">Gere relatórios detalhados de vendas por usuário específico</p>
        </div>
    </div>

    <!-- Formulário de Relatório -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <form method="POST" action="{{ route('sales-reports.user') }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Email do Usuário -->
                <div>
                    <label for="user_email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email do Usuário
                    </label>
                    <input type="email" 
                           id="user_email" 
                           name="user_email" 
                           value="{{ old('user_email', 'guabinorte1@gmail.com') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Digite o email do usuário"
                           required>
                    @error('user_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Período -->
                <div>
                    <label for="period" class="block text-sm font-medium text-gray-700 mb-2">
                        Período
                    </label>
                    <select id="period" 
                            name="period" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            required>
                        <option value="week" {{ old('period') == 'week' ? 'selected' : '' }}>Semana Atual</option>
                        <option value="month" {{ old('period') == 'month' ? 'selected' : '' }}>Mês Atual</option>
                        <option value="year" {{ old('period') == 'year' ? 'selected' : '' }}>Ano Atual</option>
                    </select>
                    @error('period')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botões -->
            <div class="flex flex-col sm:flex-row gap-4">
                <button type="submit" 
                        name="format" 
                        value="html"
                        class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Visualizar Relatório
                </button>
                
                <button type="submit" 
                        name="format" 
                        value="pdf"
                        class="flex-1 bg-red-600 text-white px-6 py-3 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Baixar PDF
                </button>
            </div>
        </form>
    </div>

    <!-- Relatório Rápido para guabinorte1@gmail.com -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Relatório Rápido - guabinorte1@gmail.com</h3>
        <p class="text-gray-600 mb-4">Acesse rapidamente os relatórios para o usuário específico:</p>
        
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('sales-reports.guabinorte', ['period' => 'week', 'format' => 'html']) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-md hover:bg-blue-200 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Semana (HTML)
            </a>
            
            <a href="{{ route('sales-reports.guabinorte', ['period' => 'week', 'format' => 'pdf']) }}" 
               class="inline-flex items-center px-4 py-2 bg-red-100 text-red-800 rounded-md hover:bg-red-200 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Semana (PDF)
            </a>
            
            <a href="{{ route('sales-reports.guabinorte', ['period' => 'month', 'format' => 'html']) }}" 
               class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-md hover:bg-green-200 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Mês (HTML)
            </a>
            
            <a href="{{ route('sales-reports.guabinorte', ['period' => 'month', 'format' => 'pdf']) }}" 
               class="inline-flex items-center px-4 py-2 bg-orange-100 text-orange-800 rounded-md hover:bg-orange-200 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Mês (PDF)
            </a>
            
            <a href="{{ route('sales-reports.guabinorte', ['period' => 'year', 'format' => 'html']) }}" 
               class="inline-flex items-center px-4 py-2 bg-purple-100 text-purple-800 rounded-md hover:bg-purple-200 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Ano (HTML)
            </a>
            
            <a href="{{ route('sales-reports.guabinorte', ['period' => 'year', 'format' => 'pdf']) }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ano (PDF)
            </a>
        </div>
    </div>

    <!-- Instruções -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Instruções</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Digite o email do usuário para gerar o relatório específico</li>
                        <li>Selecione o período desejado (semana, mês ou ano)</li>
                        <li>Escolha entre visualizar no navegador ou baixar em PDF</li>
                        <li>Use os botões de acesso rápido para o usuário guabinorte1@gmail.com</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
