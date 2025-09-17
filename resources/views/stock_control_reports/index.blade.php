@extends('dashboard.layout')

@section('title', 'Relatório de Controle de Estoque')

@section('content')
<div class="space-y-6">
    <!-- Cabeçalho -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Relatório de Controle de Estoque</h1>
            <p class="text-gray-600">Compare estoque físico vs virtual e identifique divergências</p>
        </div>
    </div>

    <!-- Formulário de Relatório -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <form method="POST" action="{{ route('stock-control-reports.generate') }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Categoria -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Categoria (Opcional)
                    </label>
                    <select id="category_id" 
                            name="category_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todas as categorias</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Mostrar produtos com estoque zero -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="show_zero_stock" 
                           name="show_zero_stock" 
                           value="1"
                           {{ old('show_zero_stock') ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="show_zero_stock" class="ml-2 block text-sm text-gray-700">
                        Incluir produtos com estoque zero
                    </label>
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
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-xl border border-green-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Relatório Rápido - guabinorte1@gmail.com</h3>
        <p class="text-gray-600 mb-4">Acesse rapidamente os relatórios de controle de estoque:</p>
        
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('stock-control-reports.guabinorte', ['format' => 'html']) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-md hover:bg-blue-200 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Relatório Completo (HTML)
            </a>
            
            <a href="{{ route('stock-control-reports.guabinorte', ['format' => 'pdf']) }}" 
               class="inline-flex items-center px-4 py-2 bg-red-100 text-red-800 rounded-md hover:bg-red-200 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Relatório Completo (PDF)
            </a>
            
            <a href="{{ route('stock-control-reports.guabinorte', ['show_zero_stock' => '1', 'format' => 'html']) }}" 
               class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-md hover:bg-green-200 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Incluindo Estoque Zero (HTML)
            </a>
            
            <a href="{{ route('stock-control-reports.guabinorte', ['show_zero_stock' => '1', 'format' => 'pdf']) }}" 
               class="inline-flex items-center px-4 py-2 bg-orange-100 text-orange-800 rounded-md hover:bg-orange-200 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Incluindo Estoque Zero (PDF)
            </a>
        </div>
    </div>

    <!-- Informações sobre o Relatório -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Sobre o Relatório de Controle de Estoque</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li><strong>Estoque Físico:</strong> Calculado através das movimentações de entrada e saída</li>
                        <li><strong>Estoque Virtual:</strong> Valor armazenado no campo stock_quantity do produto</li>
                        <li><strong>Divergência:</strong> Diferença entre estoque físico e virtual</li>
                        <li><strong>Status:</strong> Normal, Baixo, Alto ou Zero baseado no estoque físico e estoque mínimo</li>
                        <li><strong>Valor do Estoque:</strong> Estoque físico × preço de custo</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas Importantes -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Importante</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Este relatório ajuda a identificar divergências entre o estoque físico (real) e o estoque virtual (sistema). 
                    Divergências podem indicar problemas como:</p>
                    <ul class="list-disc list-inside mt-1 space-y-1">
                        <li>Movimentações não registradas no sistema</li>
                        <li>Produtos perdidos ou danificados</li>
                        <li>Erros de digitação nas movimentações</li>
                        <li>Necessidade de inventário físico</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
