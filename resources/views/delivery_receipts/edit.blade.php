@extends('dashboard.layout')

@section('title', 'Editar Romaneio')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Editar Romaneio #{{ $deliveryReceipt->id }}</h1>
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

    <form action="{{ route('delivery_receipts.update', $deliveryReceipt) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Informações Gerais</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="supplier_cnpj" class="block text-sm font-medium text-gray-700 mb-1">CNPJ do Fornecedor</label>
                    <div class="flex">
                        <input type="text" name="supplier_cnpj" id="supplier_cnpj" 
                               value="{{ old('supplier_cnpj', $deliveryReceipt->supplier_cnpj) }}" 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="00.000.000/0001-00" maxlength="18">
                        <button type="button" onclick="lookupCNPJ()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700">
                            Consultar
                        </button>
                    </div>
                </div>

                <div>
                    <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-1">Data de Entrega</label>
                    <input type="date" name="delivery_date" id="delivery_date" 
                           value="{{ old('delivery_date', $deliveryReceipt->delivery_date->format('Y-m-d')) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="supplier_name" class="block text-sm font-medium text-gray-700 mb-1">Nome do Fornecedor</label>
                    <input type="text" name="supplier_name" id="supplier_name" 
                           value="{{ old('supplier_name', $deliveryReceipt->supplier_name) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Nome do fornecedor" required>
                </div>

                <div>
                    <label for="supplier_contact" class="block text-sm font-medium text-gray-700 mb-1">Contato do Fornecedor</label>
                    <input type="text" name="supplier_contact" id="supplier_contact" 
                           value="{{ old('supplier_contact', $deliveryReceipt->supplier_contact) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Telefone ou email">
                </div>
            </div>

            <div class="mb-4">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                <textarea name="notes" id="notes" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Observações gerais do romaneio...">{{ old('notes', $deliveryReceipt->notes) }}</textarea>
            </div>
        </div>

        <!-- Lista de Produtos -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-800">Produtos do Romaneio</h3>
                <button type="button" onclick="addProduct()" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Adicionar Produto
                </button>
            </div>

            <div id="products-container">
                @foreach($deliveryReceipt->items as $index => $item)
                    <div class="product-item border border-gray-200 rounded-lg p-4 mb-4" data-index="{{ $index }}">
                        <div class="flex justify-between items-start mb-4">
                            <h4 class="font-medium text-gray-800">Produto {{ $index + 1 }}</h4>
                            <button type="button" onclick="removeProduct(this)" 
                                    class="text-red-600 hover:text-red-800">
                                Remover
                            </button>
                        </div>

                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto</label>
                                <input type="text" name="items[{{ $index }}][product_name]" 
                                       value="{{ old('items.' . $index . '.product_name', $item->product_name) }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Nome do produto" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Código do Produto</label>
                                <input type="text" name="items[{{ $index }}][product_code]" 
                                       value="{{ old('items.' . $index . '.product_code', $item->product_code) }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Código">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade Esperada</label>
                                <input type="number" name="items[{{ $index }}][expected_quantity]" 
                                       value="{{ old('items.' . $index . '.expected_quantity', $item->expected_quantity) }}" 
                                       step="0.01" min="0" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="0,00" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade Recebida</label>
                                <input type="number" name="items[{{ $index }}][received_quantity]" 
                                       value="{{ old('items.' . $index . '.received_quantity', $item->received_quantity) }}" 
                                       step="0.01" min="0" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="0,00">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Observações do Item</label>
                                <textarea name="items[{{ $index }}][notes]" rows="2" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                          placeholder="Observações específicas...">{{ old('items.' . $index . '.notes', $item->notes) }}</textarea>
                            </div>

                            <div class="flex items-center">
                                <input type="hidden" name="items[{{ $index }}][checked]" value="0">
                                <input type="checkbox" name="items[{{ $index }}][checked]" id="checked_{{ $index }}" 
                                       value="1" {{ old('items.' . $index . '.checked', $item->checked) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="checked_{{ $index }}" class="ml-2 block text-sm text-gray-900">
                                    Item conferido
                                </label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($deliveryReceipt->items->count() == 0)
                <div id="no-products" class="text-center py-8 text-gray-500">
                    <p>Nenhum produto adicionado. Clique em "Adicionar Produto" para começar.</p>
                </div>
            @endif
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('delivery_receipts.show', $deliveryReceipt) }}" 
               class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                Atualizar Romaneio
            </button>
        </div>
    </form>
</div>

<script>
let productIndex = {{ $deliveryReceipt->items->count() }};

function addProduct() {
    const container = document.getElementById('products-container');
    const noProducts = document.getElementById('no-products');
    
    if (noProducts) {
        noProducts.remove();
    }

    const productHtml = `
        <div class="product-item border border-gray-200 rounded-lg p-4 mb-4" data-index="${productIndex}">
            <div class="flex justify-between items-start mb-4">
                <h4 class="font-medium text-gray-800">Produto ${productIndex + 1}</h4>
                <button type="button" onclick="removeProduct(this)" 
                        class="text-red-600 hover:text-red-800">
                    Remover
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto</label>
                    <input type="text" name="items[${productIndex}][product_name]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Nome do produto" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código do Produto</label>
                    <input type="text" name="items[${productIndex}][product_code]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Código">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade Esperada</label>
                    <input type="number" name="items[${productIndex}][expected_quantity]" 
                           step="0.01" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0,00" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade Recebida</label>
                    <input type="number" name="items[${productIndex}][received_quantity]" 
                           step="0.01" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0,00">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observações do Item</label>
                    <textarea name="items[${productIndex}][notes]" rows="2" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Observações específicas..."></textarea>
                </div>

                <div class="flex items-center">
                    <input type="hidden" name="items[${productIndex}][checked]" value="0">
                    <input type="checkbox" name="items[${productIndex}][checked]" id="checked_${productIndex}" 
                           value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="checked_${productIndex}" class="ml-2 block text-sm text-gray-900">
                        Item conferido
                    </label>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', productHtml);
    productIndex++;
}

function removeProduct(button) {
    const productItem = button.closest('.product-item');
    productItem.remove();

    // Verificar se não há mais produtos
    const container = document.getElementById('products-container');
    if (container.children.length === 0) {
        container.innerHTML = '<div id="no-products" class="text-center py-8 text-gray-500"><p>Nenhum produto adicionado. Clique em "Adicionar Produto" para começar.</p></div>';
    }
}

function lookupCNPJ() {
    const cnpjInput = document.getElementById('supplier_cnpj');
    const cnpj = cnpjInput.value.replace(/\D/g, '');
    
    if (cnpj.length !== 14) {
        alert('Por favor, insira um CNPJ válido');
        return;
    }

    // Indicador de carregamento
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Consultando...';
    button.disabled = true;

    fetch(`https://www.receitaws.com.br/v1/cnpj/${cnpj}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'ERROR') {
                alert('CNPJ não encontrado ou inválido');
            } else {
                document.getElementById('supplier_name').value = data.nome || data.fantasia || '';
                
                // Tentar preencher o contato
                let contact = '';
                if (data.telefone) {
                    contact = data.telefone;
                }
                if (data.email) {
                    contact += (contact ? ' / ' : '') + data.email;
                }
                document.getElementById('supplier_contact').value = contact;
            }
        })
        .catch(error => {
            console.error('Erro ao consultar CNPJ:', error);
            alert('Erro ao consultar CNPJ. Tente novamente.');
        })
        .finally(() => {
            button.textContent = originalText;
            button.disabled = false;
        });
}

// Máscara para CNPJ
document.getElementById('supplier_cnpj').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    value = value.replace(/(\d{2})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1/$2');
    value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    this.value = value;
});
</script>
@endsection
