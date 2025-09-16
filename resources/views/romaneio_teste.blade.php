@extends('dashboard.layout')

@section('title', 'Teste do Sistema de Romaneio')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Teste do Sistema de Romaneio</h1>
    
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Funcionalidades Implementadas</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border rounded-lg p-4">
                <h3 class="font-semibold text-green-600 mb-2">✅ Busca de Produtos</h3>
                <p class="text-sm text-gray-600 mb-3">Sistema busca produtos cadastrados na empresa</p>
                <a href="{{ route('delivery_receipts.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Testar Criação
                </a>
            </div>
            
            <div class="border rounded-lg p-4">
                <h3 class="font-semibold text-green-600 mb-2">✅ Interface Moderna</h3>
                <p class="text-sm text-gray-600 mb-3">Design responsivo com Tailwind CSS</p>
                <a href="{{ route('delivery_receipts.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Ver Romaneios
                </a>
            </div>
            
            <div class="border rounded-lg p-4">
                <h3 class="font-semibold text-green-600 mb-2">✅ Autocomplete de Produtos</h3>
                <p class="text-sm text-gray-600 mb-3">Busca por nome ou código do produto</p>
                <div class="text-sm text-gray-500">
                    Disponível na tela de criação
                </div>
            </div>
            
            <div class="border rounded-lg p-4">
                <h3 class="font-semibold text-green-600 mb-2">✅ Consulta CNPJ</h3>
                <p class="text-sm text-gray-600 mb-3">Preenchimento automático dos dados do fornecedor</p>
                <div class="text-sm text-gray-500">
                    Integrado no formulário
                </div>
            </div>
        </div>
        
        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
            <h3 class="font-semibold text-blue-800 mb-2">Como Testar:</h3>
            <ol class="list-decimal list-inside text-sm text-blue-700 space-y-1">
                <li>Certifique-se de ter produtos cadastrados na empresa</li>
                <li>Vá para <strong>Criar Novo Romaneio</strong></li>
                <li>Digite no campo de busca de produtos</li>
                <li>Clique no produto desejado para adicionar</li>
                <li>Preencha as quantidades</li>
                <li>Salve o romaneio</li>
            </ol>
        </div>
        
        <div class="mt-4 p-4 bg-yellow-50 rounded-lg">
            <h3 class="font-semibold text-yellow-800 mb-2">Recursos Disponíveis:</h3>
            <ul class="list-disc list-inside text-sm text-yellow-700 space-y-1">
                <li>Busca dinâmica de produtos</li>
                <li>Máscara de CNPJ automática</li>
                <li>Campos de quantidade esperada e recebida</li>
                <li>Checkbox para marcar conferência</li>
                <li>Observações por item</li>
                <li>Seção de assinaturas</li>
                <li>Função de impressão</li>
            </ul>
        </div>
    </div>
</div>
@endsection
