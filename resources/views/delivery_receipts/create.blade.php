@extends('dashboard.layout')

@section('title', 'Novo Romaneio')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">+ Novo Romaneio</h1>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('delivery_receipts.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label for="receipt_number" class="block text-sm font-medium text-gray-700 mb-1">Número do Romaneio</label>
                <input type="text" name="receipt_number" id="receipt_number" value="{{ old('receipt_number', $nextNumber) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Ex: ROM123456" required>
            </div>
            
            <div>
                <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-1">Data de Entrega</label>
                <input type="date" name="delivery_date" id="delivery_date" value="{{ old('delivery_date', date('Y-m-d')) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div>
                <label for="supplier_contact" class="block text-sm font-medium text-gray-700 mb-1">Contato do Fornecedor</label>
                <input type="text" name="supplier_contact" id="supplier_contact" value="{{ old('supplier_contact') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Telefone ou email do fornecedor">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div>
                <label for="supplier_cnpj" class="block text-sm font-medium text-gray-700 mb-1">CNPJ do Fornecedor</label>
                <input type="text" name="supplier_cnpj" id="supplier_cnpj" value="{{ old('supplier_cnpj') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="00.000.000/0001-00" maxlength="18">
                <span class="text-sm text-gray-500" id="cnpj-status"></span>
            </div>

            <div>
                <label for="supplier_name" class="block text-sm font-medium text-gray-700 mb-1">Razão Social</label>
                <input type="text" name="supplier_name" id="supplier_name" value="{{ old('supplier_name') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Fornecedor">
            </div>

            <div>
                <label for="supplier_state" class="block text-sm font-medium text-gray-700 mb-1">UF</label>
                <input type="text" name="supplier_state" id="supplier_state" value="{{ old('supplier_state') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Estado" maxlength="2">
            </div>

            <div>
                <label for="supplier_city" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                <input type="text" name="supplier_city" id="supplier_city" value="{{ old('supplier_city') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Cidade">
            </div>
        </div>

        <h3 class="text-lg font-medium text-gray-900 mb-4">✔ Checklist de Produtos</h3>
        
        <button type="button" id="add-item" class="mb-4 px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm">
            + Adicionar Produto
        </button>

        <div class="overflow-x-auto mb-6">
            <table class="min-w-full border border-gray-200" id="items-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase border-b">Produto</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase border-b">Código</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase border-b">Qtd Esperada</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase border-b">Qtd Recebida</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase border-b">Observações</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase border-b">Conferido</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase border-b">Ações</th>
                    </tr>
                </thead>
                <tbody id="items-tbody">
                    <!-- Linhas serão adicionadas dinamicamente -->
                </tbody>
            </table>
        </div>

        <div class="mb-6">
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
            <textarea name="notes" id="notes" rows="3" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Informações adicionais, horários, ocorrências etc...">{{ old('notes') }}</textarea>
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('delivery_receipts.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                Salvar
            </button>
        </div>
    </form>
</div>

<script>
let itemIndex = 0;

// Adicionar item
document.getElementById('add-item').addEventListener('click', function() {
    addItemRow();
});

function addItemRow(productName = '', productCode = '', expectedQty = '', receivedQty = '', checked = false, notes = '') {
    const tbody = document.getElementById('items-tbody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td class="px-4 py-2 border-b">
            <input type="text" name="items[${itemIndex}][product_name]" value="${productName}"
                   class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                   placeholder="Nome do produto" required>
        </td>
        <td class="px-4 py-2 border-b">
            <input type="text" name="items[${itemIndex}][product_code]" value="${productCode}"
                   class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                   placeholder="Código">
        </td>
        <td class="px-4 py-2 border-b">
            <input type="number" name="items[${itemIndex}][expected_quantity]" value="${expectedQty}" step="0.01" min="0.01"
                   class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                   placeholder="Qtd Esperada" required>
        </td>
        <td class="px-4 py-2 border-b">
            <input type="number" name="items[${itemIndex}][received_quantity]" value="${receivedQty}" step="0.01" min="0"
                   class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                   placeholder="Qtd Recebida">
        </td>
        <td class="px-4 py-2 border-b">
            <textarea name="items[${itemIndex}][notes]" rows="1"
                      class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 text-xs"
                      placeholder="Observações">${notes}</textarea>
        </td>
        <td class="px-4 py-2 border-b text-center">
            <input type="checkbox" name="items[${itemIndex}][checked]" value="1" ${checked ? 'checked' : ''}
                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
        </td>
        <td class="px-4 py-2 border-b text-center">
            <button type="button" onclick="removeItemRow(this)" 
                    class="text-red-600 hover:text-red-900 text-sm">Remover</button>
        </td>
    `;
    tbody.appendChild(row);
    itemIndex++;
}

function removeItemRow(button) {
    button.closest('tr').remove();
}

// Buscar CNPJ
document.getElementById('supplier_cnpj').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    
    // Formatação do CNPJ
    if (value.length <= 14) {
        value = value.replace(/(\d{2})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1/$2');
        value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    }
    
    this.value = value;
});

document.getElementById('supplier_cnpj').addEventListener('blur', function() {
    const cnpj = this.value.replace(/\D/g, '');
    const status = document.getElementById('cnpj-status');
    
    if (cnpj.length !== 14) {
        status.textContent = 'CNPJ inválido';
        status.className = 'text-sm text-red-500';
        clearSupplierFields();
        return;
    }
    
    status.textContent = 'Buscando...';
    status.className = 'text-sm text-blue-500';
    
    // Simular busca de CNPJ (você pode implementar a integração real)
    setTimeout(() => {
        // Dados fictícios para demonstração
        document.getElementById('supplier_name').value = 'Empresa Exemplo LTDA';
        document.getElementById('supplier_state').value = 'SP';
        document.getElementById('supplier_city').value = 'São Paulo';
        
        status.textContent = '✓ CNPJ encontrado';
        status.className = 'text-sm text-green-500';
    }, 1000);
});

function clearSupplierFields() {
    document.getElementById('supplier_name').value = '';
    document.getElementById('supplier_state').value = '';
    document.getElementById('supplier_city').value = '';
}

// Adicionar primeira linha automaticamente
addItemRow();

// Restaurar itens do old() se houver erro de validação
@if(old('items'))
    // Limpar linha inicial
    document.getElementById('items-tbody').innerHTML = '';
    itemIndex = 0;
    
    @foreach(old('items') as $index => $item)
        addItemRow(
            '{{ $item['product_name'] ?? '' }}', 
            '{{ $item['product_code'] ?? '' }}', 
            '{{ $item['expected_quantity'] ?? '' }}', 
            '{{ $item['received_quantity'] ?? '' }}', 
            {{ isset($item['checked']) && $item['checked'] ? 'true' : 'false' }}, 
            '{{ $item['notes'] ?? '' }}'
        );
    @endforeach
@endif
</script>
@endsection
