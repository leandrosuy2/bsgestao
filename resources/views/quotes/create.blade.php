@extends('dashboard.layout')

@section('title', 'Criar Novo Orçamento')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Criar Novo Orçamento</h1>
        <p class="text-gray-600 mt-1">Crie um orçamento profissional para seus clientes.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('quotes.store') }}" method="POST" id="quoteForm">
        @csrf
        
        <!-- Container do Orçamento -->
        <div id="orcamento-container" class="bg-gradient-to-b from-orange-400 to-orange-300 p-8 rounded-lg shadow-lg">
            <div class="max-w-4xl mx-auto bg-orange-50 rounded-lg shadow-xl p-8">
                
                <!-- Header do Orçamento -->
                <header class="flex justify-between items-center border-b-2 border-black pb-4 mb-6">
                    <div class="logo text-3xl font-bold text-black">
                        <input type="text" id="company_name" name="company_name" 
                               value="{{ $userCompany->name ?? 'NOME DA EMPRESA' }}" 
                               class="bg-transparent border-2 border-dashed border-gray-400 rounded px-2 py-1 text-3xl font-bold focus:outline-none focus:border-orange-500">
                        <div class="text-base text-gray-700 mt-1">
                            <input type="text" id="company_subtitle" name="company_subtitle" 
                                   value="COMUNICAÇÃO VISUAL" 
                                   placeholder="Subtítulo da empresa"
                                   class="bg-transparent border-2 border-dashed border-gray-400 rounded px-2 py-1 text-base focus:outline-none focus:border-orange-500">
                        </div>
                    </div>
                    <div class="text-xl font-bold">
                        <input type="text" id="quote_number" name="quote_number" value="{{ sprintf('%05d/%s', ($lastQuoteId ?? 0) + 1, date('y')) }}" 
                               class="bg-transparent border-2 border-dashed border-gray-400 rounded px-2 py-1 text-xl font-bold focus:outline-none focus:border-orange-500 w-32">
                    </div>
                </header>

                <!-- Informações do Cliente -->
                <section class="cliente-info mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contratante *</label>
                            <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                   placeholder="Nome do cliente">
                            @error('customer_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                            @php
                                $meses = [
                                    1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
                                    5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
                                    9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
                                ];
                                $dia = date('d');
                                $mes = $meses[date('n')];
                                $ano = date('Y');
                            @endphp
                            <input type="text" value="{{ $dia }} de {{ $mes }} de {{ $ano }}" readonly
                                   class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                   placeholder="email@cliente.com">
                            @error('customer_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                            <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                   placeholder="(11) 99999-9999">
                            @error('customer_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <!-- Tabela de Itens -->
                <section class="tabela mb-6">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-400 mb-4">
                            <thead>
                                <tr class="bg-orange-200">
                                    <th class="border border-gray-400 px-4 py-3 text-left font-semibold">Produto</th>
                                    <th class="border border-gray-400 px-4 py-3 text-left font-semibold w-20">Qt</th>
                                    <th class="border border-gray-400 px-4 py-3 text-left font-semibold w-32">Valor Unit.</th>
                                    <th class="border border-gray-400 px-4 py-3 text-left font-semibold w-32">Valor Total</th>
                                    <th class="border border-gray-400 px-4 py-3 text-center font-semibold w-20">Ação</th>
                                </tr>
                            </thead>
                            <tbody id="items-table">
                                <tr class="item-row">
                                    <td class="border border-gray-400 p-2">
                                        <select name="items[0][product_id]" required class="w-full border-none focus:outline-none bg-transparent product-select">
                                            <option value="">Selecione um produto...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" 
                                                        data-price="{{ $product->sale_price }}"
                                                        data-name="{{ $product->name }}">
                                                    {{ $product->name }} - R$ {{ number_format($product->sale_price, 2, ',', '.') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="border border-gray-400 p-2">
                                        <input type="number" name="items[0][qtd]" min="1" value="1" required
                                               class="w-full border-none focus:outline-none bg-transparent text-center item-qty">
                                    </td>
                                    <td class="border border-gray-400 p-2">
                                        <input type="number" name="items[0][unitario]" step="0.01" min="0.01" required
                                               class="w-full border-none focus:outline-none bg-transparent text-center item-price"
                                               placeholder="0,00" readonly>
                                    </td>
                                    <td class="border border-gray-400 p-2">
                                        <input type="text" readonly
                                               class="w-full border-none focus:outline-none bg-transparent text-center font-semibold item-total"
                                               value="R$ 0,00">
                                    </td>
                                    <td class="border border-gray-400 p-2 text-center">
                                        <button type="button" onclick="removeItem(this)" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between items-center mb-4">
                        <button type="button" id="add-item" class="bg-orange-500 hover:bg-orange-600 text-black px-4 py-2 rounded-lg font-semibold">
                            <i class="fas fa-plus mr-2"></i>Adicionar Item
                        </button>
                        <div class="text-right text-xl font-bold text-red-700">
                            Sub-total: <span id="subtotal" class="font-bold">R$ 0,00</span>
                        </div>
                    </div>

                    <!-- Desconto -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Desconto (R$)</label>
                            <input type="number" name="discount" id="discount" step="0.01" min="0" value="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Validade do Orçamento</label>
                            <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until', date('Y-m-d', strtotime('+30 days'))) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        </div>
                        <div class="flex items-end">
                            <div class="text-right text-2xl font-bold text-red-700 w-full">
                                Total Final: <span id="final-total" class="font-bold">R$ 0,00</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Informações Extras -->
                <section class="info-extra bg-orange-100 p-4 rounded-lg border-2 border-dashed border-orange-300">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Formas de pagamento</label>
                            <textarea name="payment_terms" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                      placeholder="50% entrada + 50% na entrega. Aceitamos pix, dinheiro e cartão de crédito.">{{ old('payment_terms', '50% entrada + 50% na entrega. Aceitamos pix, dinheiro e cartão de crédito.') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prazo de entrega</label>
                            <input type="text" name="delivery_time" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                   placeholder="10 dias úteis ou menos" value="{{ old('delivery_time', '10 dias úteis ou menos') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Chave Pix</label>
                            <input type="text" name="pix_key" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                   placeholder="{{ $userCompany->email ?? 'seu.email@exemplo.com' }}" 
                                   value="{{ old('pix_key', $userCompany->email ?? '') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                            <textarea name="notes" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                      placeholder="Observações adicionais">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </section>

                <!-- Botões de Ação -->
                <div class="flex justify-center gap-4 mt-8">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-lg font-semibold text-lg shadow-lg">
                        <i class="fas fa-save mr-2"></i>Salvar Orçamento
                    </button>
                    <a href="{{ route('quotes.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-8 py-3 rounded-lg font-semibold text-lg shadow-lg">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #orcamento-container, #orcamento-container * {
        visibility: visible;
    }
    #orcamento-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        background: white !important;
        padding: 20px !important;
    }
    .btn-group, 
    button[type="submit"],
    button[onclick*="print"],
    a[href*="quotes"] {
        display: none !important;
    }
    input, textarea {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
    }
}
</style>

<script>
let itemIndex = 1;

document.addEventListener('DOMContentLoaded', function() {
    // Adicionar novo item
    document.getElementById('add-item').addEventListener('click', function() {
        const tbody = document.getElementById('items-table');
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <td class="border border-gray-400 p-2">
                <select name="items[${itemIndex}][product_id]" required class="w-full border-none focus:outline-none bg-transparent product-select">
                    <option value="">Selecione um produto...</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" 
                                data-price="{{ $product->sale_price }}"
                                data-name="{{ $product->name }}">
                            {{ $product->name }} - R$ {{ number_format($product->sale_price, 2, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="border border-gray-400 p-2">
                <input type="number" name="items[${itemIndex}][qtd]" min="1" value="1" required
                       class="w-full border-none focus:outline-none bg-transparent text-center item-qty">
            </td>
            <td class="border border-gray-400 p-2">
                <input type="number" name="items[${itemIndex}][unitario]" step="0.01" min="0.01" required
                       class="w-full border-none focus:outline-none bg-transparent text-center item-price"
                       placeholder="0,00" readonly>
            </td>
            <td class="border border-gray-400 p-2">
                <input type="text" readonly
                       class="w-full border-none focus:outline-none bg-transparent text-center font-semibold item-total"
                       value="R$ 0,00">
            </td>
            <td class="border border-gray-400 p-2 text-center">
                <button type="button" onclick="removeItem(this)" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(newRow);
        itemIndex++;
        
        // Adicionar eventos aos novos campos
        addItemEvents(newRow);
    });

    // Adicionar eventos aos itens existentes
    document.querySelectorAll('.item-row').forEach(addItemEvents);
    
    // Evento para desconto
    document.getElementById('discount').addEventListener('input', calculateTotal);
});

function addItemEvents(row) {
    const productSelect = row.querySelector('.product-select');
    const qtyInput = row.querySelector('.item-qty');
    const priceInput = row.querySelector('.item-price');
    
    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const price = parseFloat(selectedOption.dataset.price) || 0;
            priceInput.value = price.toFixed(2);
            calculateItemTotal(row);
            calculateTotal();
        } else {
            priceInput.value = '';
            calculateItemTotal(row);
            calculateTotal();
        }
    });
    
    qtyInput.addEventListener('input', function() {
        calculateItemTotal(row);
        calculateTotal();
    });
}

function calculateItemTotal(row) {
    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    const total = qty * price;
    
    row.querySelector('.item-total').value = formatCurrency(total);
}

function calculateTotal() {
    let subtotal = 0;
    
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        subtotal += qty * price;
    });
    
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const finalTotal = subtotal - discount;
    
    document.getElementById('subtotal').textContent = formatCurrency(subtotal);
    document.getElementById('final-total').textContent = formatCurrency(finalTotal);
}

function removeItem(button) {
    const row = button.closest('.item-row');
    const tbody = document.getElementById('items-table');
    
    if (tbody.children.length > 1) {
        row.remove();
        calculateTotal();
    } else {
        alert('Deve haver pelo menos um item no orçamento.');
    }
}

function formatCurrency(value) {
    return 'R$ ' + value.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Validação antes de enviar
document.getElementById('quoteForm').addEventListener('submit', function(e) {
    const customerName = document.getElementById('customer_name').value.trim();
    const items = document.querySelectorAll('.item-row');
    
    if (!customerName) {
        e.preventDefault();
        alert('Por favor, informe o nome do contratante.');
        document.getElementById('customer_name').focus();
        return;
    }
    
    let hasValidItem = false;
    items.forEach(row => {
        const productSelect = row.querySelector('select[name*="[product_id]"]');
        const qty = parseFloat(row.querySelector('input[name*="[qtd]"]').value) || 0;
        const price = parseFloat(row.querySelector('input[name*="[unitario]"]').value) || 0;
        
        if (productSelect.value && qty > 0 && price > 0) {
            hasValidItem = true;
        }
    });
    
    if (!hasValidItem) {
        e.preventDefault();
        alert('Por favor, selecione pelo menos um produto válido para o orçamento.');
        return;
    }
});
</script>
@endsection
