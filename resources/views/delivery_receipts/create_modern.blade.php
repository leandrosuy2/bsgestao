@extends('dashboard.layout')

@section('title', 'Novo Romaneio')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-clipboard-list mr-2 text-blue-500"></i> Novo Romaneio
        </h1>
        <div class="flex space-x-2">
            <button id="printBtn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                <i class="fas fa-print mr-2"></i>Imprimir
            </button>
            <a href="{{ route('delivery_receipts.index') }}" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition">
                <i class="fas fa-history mr-2"></i>Histórico
            </a>
        </div>
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

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('delivery_receipts.store') }}" method="POST">
        @csrf
        
        <!-- Main Form -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 mb-8">
            <!-- Form Header -->
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h2 class="text-xl font-semibold text-gray-700">
                    <i class="fas fa-truck mr-2 text-blue-500"></i>Informações da Entrega
                </h2>
                <p class="text-sm text-gray-500">Preencha os dados básicos do romaneio</p>
            </div>

            <!-- Form Fields - First Row -->
            <div class="flex flex-col md:flex-row md:space-x-4 space-y-4 md:space-y-0 mb-6">
                <div class="w-full md:w-1/2">
                    <label for="receipt_number" class="block text-sm font-medium text-gray-700 mb-1">Número do Romaneio</label>
                    <input type="text" name="receipt_number" id="receipt_number" 
                           value="{{ old('receipt_number', $nextNumber) }}"
                           class="w-full form-input px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           required>
                </div>
                <div class="w-full md:w-1/2">
                    <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-1">Data da Entrega</label>
                    <input type="date" name="delivery_date" id="delivery_date" 
                           value="{{ old('delivery_date', date('Y-m-d')) }}"
                           class="w-full form-input px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           required>
                </div>
            </div>

            <!-- Form Fields - Second Row -->
            <div class="flex flex-col md:flex-row md:space-x-4 space-y-4 md:space-y-0 mb-6">
                <div class="w-full md:w-1/2">
                    <label for="supplier_contact" class="block text-sm font-medium text-gray-700 mb-1">Contato do Fornecedor</label>
                    <input type="text" name="supplier_contact" id="supplier_contact" 
                           value="{{ old('supplier_contact') }}"
                           class="w-full form-input px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="w-full md:w-1/2">
                    <label for="supplier_phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                    <input type="tel" name="supplier_phone" id="supplier_phone" 
                           value="{{ old('supplier_phone') }}"
                           class="w-full form-input px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Form Fields - Third Row -->
            <div class="flex flex-col lg:flex-row lg:space-x-4 space-y-4 lg:space-y-0 mb-6">
                <div class="w-full lg:w-1/3">
                    <label for="supplier_cnpj" class="block text-sm font-medium text-gray-700 mb-1">CNPJ do Fornecedor</label>
                    <div class="relative">
                        <input type="text" name="supplier_cnpj" id="supplier_cnpj" 
                               value="{{ old('supplier_cnpj') }}"
                               class="w-full form-input px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="00.000.000/0000-00" maxlength="18" required>
                        <div id="supplier_suggestions" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-xl hidden mt-1 max-h-60 overflow-y-auto">
                            <div id="supplier_loading_message" class="px-4 py-2 text-gray-500 text-center hidden">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Carregando fornecedores...
                            </div>
                            <div id="supplier_no_results_message" class="px-4 py-2 text-gray-500 text-center hidden">
                                <i class="fas fa-search mr-2"></i>Nenhum fornecedor encontrado
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full lg:w-1/3">
                    <label for="supplier_name" class="block text-sm font-medium text-gray-700 mb-1">Razão Social</label>
                    <div class="relative">
                        <input type="text" name="supplier_name" id="supplier_name" 
                               value="{{ old('supplier_name') }}"
                               class="w-full form-input px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="Digite o nome do fornecedor ou busque por CNPJ..." required>
                    </div>
                </div>
                <div class="w-full lg:w-1/6">
                    <label for="supplier_state" class="block text-sm font-medium text-gray-700 mb-1">UF</label>
                    <select name="supplier_state" id="supplier_state" 
                            class="w-full form-input px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Selecione</option>
                        <option value="AC" {{ old('supplier_state') == 'AC' ? 'selected' : '' }}>AC</option>
                        <option value="AL" {{ old('supplier_state') == 'AL' ? 'selected' : '' }}>AL</option>
                        <option value="AP" {{ old('supplier_state') == 'AP' ? 'selected' : '' }}>AP</option>
                        <option value="AM" {{ old('supplier_state') == 'AM' ? 'selected' : '' }}>AM</option>
                        <option value="BA" {{ old('supplier_state') == 'BA' ? 'selected' : '' }}>BA</option>
                        <option value="CE" {{ old('supplier_state') == 'CE' ? 'selected' : '' }}>CE</option>
                        <option value="DF" {{ old('supplier_state') == 'DF' ? 'selected' : '' }}>DF</option>
                        <option value="ES" {{ old('supplier_state') == 'ES' ? 'selected' : '' }}>ES</option>
                        <option value="GO" {{ old('supplier_state') == 'GO' ? 'selected' : '' }}>GO</option>
                        <option value="MA" {{ old('supplier_state') == 'MA' ? 'selected' : '' }}>MA</option>
                        <option value="MT" {{ old('supplier_state') == 'MT' ? 'selected' : '' }}>MT</option>
                        <option value="MS" {{ old('supplier_state') == 'MS' ? 'selected' : '' }}>MS</option>
                        <option value="MG" {{ old('supplier_state') == 'MG' ? 'selected' : '' }}>MG</option>
                        <option value="PA" {{ old('supplier_state') == 'PA' ? 'selected' : '' }}>PA</option>
                        <option value="PB" {{ old('supplier_state') == 'PB' ? 'selected' : '' }}>PB</option>
                        <option value="PR" {{ old('supplier_state') == 'PR' ? 'selected' : '' }}>PR</option>
                        <option value="PE" {{ old('supplier_state') == 'PE' ? 'selected' : '' }}>PE</option>
                        <option value="PI" {{ old('supplier_state') == 'PI' ? 'selected' : '' }}>PI</option>
                        <option value="RJ" {{ old('supplier_state') == 'RJ' ? 'selected' : '' }}>RJ</option>
                        <option value="RN" {{ old('supplier_state') == 'RN' ? 'selected' : '' }}>RN</option>
                        <option value="RS" {{ old('supplier_state') == 'RS' ? 'selected' : '' }}>RS</option>
                        <option value="RO" {{ old('supplier_state') == 'RO' ? 'selected' : '' }}>RO</option>
                        <option value="RR" {{ old('supplier_state') == 'RR' ? 'selected' : '' }}>RR</option>
                        <option value="SC" {{ old('supplier_state') == 'SC' ? 'selected' : '' }}>SC</option>
                        <option value="SP" {{ old('supplier_state') == 'SP' ? 'selected' : '' }}>SP</option>
                        <option value="SE" {{ old('supplier_state') == 'SE' ? 'selected' : '' }}>SE</option>
                        <option value="TO" {{ old('supplier_state') == 'TO' ? 'selected' : '' }}>TO</option>
                    </select>
                </div>
                <div class="w-full lg:w-1/6">
                    <label for="supplier_city" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                    <input type="text" name="supplier_city" id="supplier_city" 
                           value="{{ old('supplier_city') }}"
                           class="w-full form-input px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8 products-section" style="overflow: visible;">
            <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-700">
                        <i class="fas fa-boxes mr-2 text-blue-500"></i>Produtos
                    </h2>
                    <p class="text-sm text-gray-500">Lista de produtos incluídos no romaneio</p>
                </div>
                <button type="button" id="addProductBtn" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">
                    <i class="fas fa-plus mr-2"></i>Adicionar Produto
                </button>
            </div>

            <!-- Product Search -->
            <div class="mb-4">
                <label for="product_search" class="block text-sm font-medium text-gray-700 mb-1">Buscar Produto</label>
                <div class="relative">
                    <input type="text" id="product_search" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Digite o nome ou código do produto...">
                    <div id="loading_indicator" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-500"></div>
                    </div>
                    <div id="product_suggestions" class="absolute z-50 w-full bg-white border border-gray-300 rounded-md shadow-xl hidden mt-1 max-h-80 overflow-y-auto">
                        <div id="loading_message" class="px-4 py-2 text-gray-500 text-center hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Carregando produtos...
                        </div>
                        <div id="no_results_message" class="px-4 py-2 text-gray-500 text-center hidden">
                            <i class="fas fa-search mr-2"></i>Nenhum produto encontrado
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qtd. Esperada</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qtd. Recebida</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observações</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conferido</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody" class="bg-white divide-y divide-gray-200">
                        <!-- Sample Product Row -->
                        @if(old('items'))
                            @foreach(old('items') as $index => $item)
                                <tr class="product-row">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="text" name="items[{{ $index }}][product_name]" 
                                               value="{{ $item['product_name'] }}"
                                               class="form-input w-full px-2 py-1 border border-gray-300 rounded-md" required>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="text" name="items[{{ $index }}][product_code]" 
                                               value="{{ $item['product_code'] ?? '' }}"
                                               class="form-input w-full px-2 py-1 border border-gray-300 rounded-md">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" name="items[{{ $index }}][expected_quantity]" 
                                               value="{{ $item['expected_quantity'] }}"
                                               class="form-input w-full px-2 py-1 border border-gray-300 rounded-md" 
                                               step="0.01" min="0" required>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" name="items[{{ $index }}][received_quantity]" 
                                               value="{{ $item['received_quantity'] ?? '' }}"
                                               class="form-input w-full px-2 py-1 border border-gray-300 rounded-md" 
                                               step="0.01" min="0">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="text" name="items[{{ $index }}][notes]" 
                                               value="{{ $item['notes'] ?? '' }}"
                                               class="form-input w-full px-2 py-1 border border-gray-300 rounded-md">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input type="checkbox" name="items[{{ $index }}][checked]" 
                                               value="1" {{ isset($item['checked']) && $item['checked'] ? 'checked' : '' }}
                                               class="h-5 w-5 text-blue-500 rounded">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button type="button" class="remove-btn px-3 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition">
                                            <i class="fas fa-trash mr-1"></i>Remover
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Observations Section -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 mb-8">
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h2 class="text-xl font-semibold text-gray-700">
                    <i class="fas fa-edit mr-2 text-blue-500"></i>Observações Gerais
                </h2>
                <p class="text-sm text-gray-500">Informações adicionais sobre a entrega</p>
            </div>
            <textarea name="notes" id="notes" rows="4" 
                      class="w-full form-input px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                      placeholder="Descreva aqui qualquer observação relevante sobre a entrega...">{{ old('notes') }}</textarea>
        </div>

        <!-- Signature Section -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 mb-8">
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h2 class="text-xl font-semibold text-gray-700">
                    <i class="fas fa-signature mr-2 text-blue-500"></i>Assinaturas
                </h2>
                <p class="text-sm text-gray-500">Confirmação de recebimento</p>
            </div>
            <div class="flex flex-col md:flex-row md:space-x-4 space-y-4 md:space-y-0">
                <div class="w-full md:w-1/2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Responsável pela Entrega</label>
                    <div class="h-24 border-b-2 border-gray-400"></div>
                    <p class="text-xs text-gray-500 mt-1">Assinatura do entregador</p>
                </div>
                <div class="w-full md:w-1/2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Responsável pelo Recebimento</label>
                    <div class="h-24 border-b-2 border-gray-400"></div>
                    <p class="text-xs text-gray-500 mt-1">Assinatura do recebedor</p>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-4">
            <a href="{{ route('delivery_receipts.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition text-center">
                <i class="fas fa-times mr-2"></i>Cancelar
            </a>
            <button type="submit" class="px-6 py-3 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">
                <i class="fas fa-save mr-2"></i>Salvar Romaneio
            </button>
            <button type="button" id="savePdfBtn" class="px-6 py-3 bg-green-500 text-white rounded-md hover:bg-green-600 transition">
                <i class="fas fa-file-pdf mr-2"></i>Salvar como PDF
            </button>
        </div>
    </form>
</div>

<style>
    /* Custom styles that can't be done with Tailwind */
    .form-input {
        transition: all 0.3s ease;
    }
    .form-input:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }
    .remove-btn:hover {
        transform: scale(1.05);
    }
    .product-row:hover {
        background-color: #f8fafc;
    }
    
    /* Ensure dropdowns are not clipped */
    .relative {
        position: relative;
    }
    
    /* Dropdown styles */
    #product_suggestions,
    #supplier_suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1000;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        max-height: 320px;
        overflow-y: auto;
        margin-top: 4px;
    }
    
    /* Scrollbar styling */
    #product_suggestions::-webkit-scrollbar,
    #supplier_suggestions::-webkit-scrollbar {
        width: 6px;
    }
    
    #product_suggestions::-webkit-scrollbar-track,
    #supplier_suggestions::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    #product_suggestions::-webkit-scrollbar-thumb,
    #supplier_suggestions::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    #product_suggestions::-webkit-scrollbar-thumb:hover,
    #supplier_suggestions::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Ensure container doesn't clip dropdowns */
    .overflow-hidden {
        overflow: visible !important;
    }
    
    /* Products section should not clip */
    .products-section {
        overflow: visible !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = {{ old('items') ? count(old('items')) : 0 }};
    
    // Add product row functionality
    document.getElementById('addProductBtn').addEventListener('click', function() {
        addProductRow();
    });

    // Supplier search functionality
    const supplierName = document.getElementById('supplier_name');
    const supplierCnpj = document.getElementById('supplier_cnpj');
    const supplierSuggestions = document.getElementById('supplier_suggestions');
    const supplierLoadingMessage = document.getElementById('supplier_loading_message');
    const supplierNoResultsMessage = document.getElementById('supplier_no_results_message');
    let supplierSearchTimeout;
    let isSupplierSearching = false;

    // Search suppliers by name
    supplierName.addEventListener('input', function() {
        clearTimeout(supplierSearchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            supplierSuggestions.classList.add('hidden');
            return;
        }
        
        supplierSearchTimeout = setTimeout(() => {
            searchSuppliers(query);
        }, 300);
    });

    // Search suppliers by CNPJ
    supplierCnpj.addEventListener('input', function() {
        clearTimeout(supplierSearchTimeout);
        const query = this.value.trim();
        
        if (query.length < 8) {
            supplierSuggestions.classList.add('hidden');
            return;
        }
        
        supplierSearchTimeout = setTimeout(() => {
            searchSuppliers(query);
        }, 300);
    });

    function searchSuppliers(query) {
        if (isSupplierSearching) return;
        
        isSupplierSearching = true;
        supplierSuggestions.classList.remove('hidden');
        supplierLoadingMessage.classList.remove('hidden');
        supplierNoResultsMessage.classList.add('hidden');
        
        fetch(`{{ route('api.suppliers-search') }}?search=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(suppliers => {
                displaySupplierSuggestions(suppliers);
            })
            .catch(error => {
                console.error('Error searching suppliers:', error);
                supplierSuggestions.classList.add('hidden');
            })
            .finally(() => {
                isSupplierSearching = false;
                supplierLoadingMessage.classList.add('hidden');
            });
    }

    function displaySupplierSuggestions(suppliers) {
        // Clear previous results
        const existingItems = supplierSuggestions.querySelectorAll('.supplier-suggestion-item');
        existingItems.forEach(item => item.remove());
        
        if (suppliers.length === 0) {
            supplierNoResultsMessage.classList.remove('hidden');
            return;
        }
        
        supplierNoResultsMessage.classList.add('hidden');
        
        suppliers.forEach(supplier => {
            const item = document.createElement('div');
            item.className = 'supplier-suggestion-item px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors duration-150';
            item.innerHTML = `
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">${supplier.name}</div>
                        <div class="text-sm text-gray-500 mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mr-2">
                                <i class="fas fa-id-card mr-1"></i>${supplier.cnpj}
                            </span>
                            ${supplier.city && supplier.state ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-map-marker-alt mr-1"></i>${supplier.city}/${supplier.state}
                            </span>` : ''}
                        </div>
                    </div>
                </div>
            `;
            
            item.addEventListener('click', function() {
                fillSupplierData(supplier);
                supplierSuggestions.classList.add('hidden');
            });
            
            supplierSuggestions.appendChild(item);
        });
        
        supplierSuggestions.classList.remove('hidden');
    }

    function fillSupplierData(supplier) {
        document.getElementById('supplier_name').value = supplier.name;
        document.getElementById('supplier_cnpj').value = supplier.cnpj;
        document.getElementById('supplier_contact').value = supplier.contact || '';
        document.getElementById('supplier_state').value = supplier.state || '';
        document.getElementById('supplier_city').value = supplier.city || '';
    }

    // Product search functionality
    const productSearch = document.getElementById('product_search');
    const productSuggestions = document.getElementById('product_suggestions');
    const loadingIndicator = document.getElementById('loading_indicator');
    const loadingMessage = document.getElementById('loading_message');
    const noResultsMessage = document.getElementById('no_results_message');
    let searchTimeout;
    let isSearching = false;

    productSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            productSuggestions.classList.add('hidden');
            loadingIndicator.classList.add('hidden');
            return;
        }
        
        searchTimeout = setTimeout(() => {
            if (isSearching) return;
            
            isSearching = true;
            loadingIndicator.classList.remove('hidden');
            productSuggestions.classList.remove('hidden');
            loadingMessage.classList.remove('hidden');
            noResultsMessage.classList.add('hidden');
            
            fetch(`{{ route('api.products-search') }}?search=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(products => {
                    displayProductSuggestions(products);
                })
                .catch(error => {
                    console.error('Error searching products:', error);
                    productSuggestions.classList.add('hidden');
                })
                .finally(() => {
                    isSearching = false;
                    loadingIndicator.classList.add('hidden');
                    loadingMessage.classList.add('hidden');
                });
        }, 300);
    });

    function displayProductSuggestions(products) {
        // Clear previous results
        const existingItems = productSuggestions.querySelectorAll('.product-suggestion-item');
        existingItems.forEach(item => item.remove());
        
        if (products.length === 0) {
            noResultsMessage.classList.remove('hidden');
            return;
        }
        
        noResultsMessage.classList.add('hidden');
        
        products.forEach(product => {
            const item = document.createElement('div');
            item.className = 'product-suggestion-item px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors duration-150';
            item.innerHTML = `
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">${product.name}</div>
                        <div class="text-sm text-gray-500 mt-1">
                            ${product.internal_code ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mr-2">
                                <i class="fas fa-barcode mr-1"></i>${product.internal_code}
                            </span>` : ''}
                            ${product.category ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-tag mr-1"></i>${product.category}
                            </span>` : ''}
                        </div>
                    </div>
                    <div class="text-right text-sm">
                        <div class="text-gray-900 font-medium">R$ ${parseFloat(product.sale_price || 0).toFixed(2)}</div>
                        <div class="text-gray-500">${product.unit || 'UN'}</div>
                    </div>
                </div>
            `;
            
            item.addEventListener('click', function() {
                addProductRow(product);
                productSearch.value = '';
                productSuggestions.classList.add('hidden');
            });
            
            productSuggestions.appendChild(item);
        });
        
        productSuggestions.classList.remove('hidden');
    }

    function addProductRow(product = null) {
        const tableBody = document.getElementById('productsTableBody');
        const newRow = document.createElement('tr');
        newRow.className = 'product-row';
        
        const productName = product ? product.name : '';
        const productCode = product ? (product.internal_code || '') : '';
        
        newRow.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="text" name="items[${itemIndex}][product_name]" 
                       value="${productName}"
                       class="form-input w-full px-2 py-1 border border-gray-300 rounded-md" required>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="text" name="items[${itemIndex}][product_code]" 
                       value="${productCode}"
                       class="form-input w-full px-2 py-1 border border-gray-300 rounded-md">
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="number" name="items[${itemIndex}][expected_quantity]" 
                       class="form-input w-full px-2 py-1 border border-gray-300 rounded-md" 
                       step="0.01" min="0" required>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="number" name="items[${itemIndex}][received_quantity]" 
                       class="form-input w-full px-2 py-1 border border-gray-300 rounded-md" 
                       step="0.01" min="0">
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="text" name="items[${itemIndex}][notes]" 
                       class="form-input w-full px-2 py-1 border border-gray-300 rounded-md">
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <input type="checkbox" name="items[${itemIndex}][checked]" 
                       value="1" class="h-5 w-5 text-blue-500 rounded">
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <button type="button" class="remove-btn px-3 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition">
                    <i class="fas fa-trash mr-1"></i>Remover
                </button>
            </td>
        `;
        
        tableBody.appendChild(newRow);
        itemIndex++;
        
        // Add event listener to the new remove button
        newRow.querySelector('.remove-btn').addEventListener('click', function() {
            tableBody.removeChild(newRow);
        });
    }

    // Add event listeners to existing remove buttons
    document.querySelectorAll('.remove-btn').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            document.getElementById('productsTableBody').removeChild(row);
        });
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(event) {
        // Hide product suggestions
        if (!productSearch.contains(event.target) && !productSuggestions.contains(event.target)) {
            productSuggestions.classList.add('hidden');
        }
        
        // Hide supplier suggestions
        if (!supplierName.contains(event.target) && 
            !supplierCnpj.contains(event.target) && 
            !supplierSuggestions.contains(event.target)) {
            supplierSuggestions.classList.add('hidden');
        }
    });

    // Print functionality
    document.getElementById('printBtn').addEventListener('click', function() {
        window.print();
    });

    // PDF generation with jsPDF
    document.getElementById('savePdfBtn').addEventListener('click', function() {
        generatePDF();
    });

    function generatePDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        // Add title
        doc.setFontSize(18);
        doc.text('ROMANEIO DE ENTREGA', 105, 20, { align: 'center' });
        
        // Add basic info
        doc.setFontSize(12);
        let yPos = 35;
        doc.text(`Número: ${document.getElementById('receipt_number').value}`, 20, yPos);
        yPos += 10;
        doc.text(`Data: ${document.getElementById('delivery_date').value}`, 20, yPos);
        yPos += 10;
        doc.text(`Fornecedor: ${document.getElementById('supplier_name').value}`, 20, yPos);
        yPos += 10;
        doc.text(`CNPJ: ${document.getElementById('supplier_cnpj').value}`, 20, yPos);
        yPos += 15;
        
        // Add products table
        const products = [];
        document.querySelectorAll('#productsTableBody tr').forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length >= 6) {
                products.push([
                    cells[0].querySelector('input').value || '',
                    cells[1].querySelector('input').value || '',
                    cells[2].querySelector('input').value || '',
                    cells[3].querySelector('input').value || '',
                    cells[4].querySelector('input').value || '',
                    cells[5].querySelector('input').checked ? '✓' : '✗'
                ]);
            }
        });
        
        if (products.length > 0) {
            doc.autoTable({
                startY: yPos,
                head: [['Produto', 'Código', 'Qtd. Esperada', 'Qtd. Recebida', 'Observações', 'Conferido']],
                body: products,
                styles: { 
                    cellPadding: 3, 
                    fontSize: 10,
                    halign: 'left'
                },
                headStyles: { 
                    fillColor: [66, 133, 244], 
                    textColor: 255,
                    fontSize: 10,
                    fontStyle: 'bold'
                },
                columnStyles: {
                    2: { halign: 'center' },
                    3: { halign: 'center' },
                    5: { halign: 'center' }
                }
            });
            
            yPos = doc.lastAutoTable.finalY + 15;
        }
        
        // Add observations
        const observations = document.getElementById('notes').value;
        if (observations) {
            doc.text('Observações:', 20, yPos);
            yPos += 10;
            const splitText = doc.splitTextToSize(observations, 170);
            doc.text(splitText, 20, yPos);
            yPos += splitText.length * 5 + 10;
        }
        
        // Add signature lines
        yPos += 20;
        doc.text('Assinatura do Entregador: ___________________________', 20, yPos);
        yPos += 15;
        doc.text('Assinatura do Recebedor: ___________________________', 20, yPos);
        
        // Save the PDF
        const receiptNumber = document.getElementById('receipt_number').value || 'sem_numero';
        doc.save(`Romaneio_${receiptNumber}.pdf`);
    }

    // CNPJ mask and API call
    const cnpjInput = document.getElementById('supplier_cnpj');
    cnpjInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        if (value.length > 2) {
            value = value.substring(0, 2) + '.' + value.substring(2);
        }
        if (value.length > 6) {
            value = value.substring(0, 6) + '.' + value.substring(6);
        }
        if (value.length > 10) {
            value = value.substring(0, 10) + '/' + value.substring(10);
        }
        if (value.length > 15) {
            value = value.substring(0, 15) + '-' + value.substring(15);
        }
        
        e.target.value = value.substring(0, 18);
        
        // Call CNPJ API when complete
        if (value.length === 18) {
            fetch(`{{ route('api.cnpj-search') }}?cnpj=${encodeURIComponent(value)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.nome) {
                        document.getElementById('supplier_name').value = data.nome;
                    }
                    if (data.uf) {
                        document.getElementById('supplier_state').value = data.uf;
                    }
                    if (data.municipio) {
                        document.getElementById('supplier_city').value = data.municipio;
                    }
                })
                .catch(error => {
                    console.error('Error fetching CNPJ data:', error);
                });
        }
    });
});
</script>

<!-- Scripts para jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

@endsection
