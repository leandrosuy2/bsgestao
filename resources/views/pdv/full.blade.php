<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>PDV - Ponto de Venda</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/js/all.min.js" defer></script>
  <style>
    /* Centralização perfeita dos modais */
    dialog {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      margin: 0;
      padding: 0;
      border: none;
      background: transparent;
      max-height: 90vh;
      overflow-y: auto;
      width: 95%;
      max-width: 600px;
    }
    
    /* Responsividade para mobile */
    @media (max-width: 640px) {
      dialog {
        width: 98%;
        max-width: none;
        top: 50%;
        margin: 0;
        max-height: 95vh;
        overflow-y: auto;
      }
      
      .container {
        padding-left: 8px;
        padding-right: 8px;
      }
      
      .grid {
        gap: 12px;
      }
      
      .text-lg {
        font-size: 16px;
      }
      
      .text-xl {
        font-size: 18px;
      }
    }
    
    dialog::backdrop {
      background-color: rgba(0, 0, 0, 0.5);
    }
    
    /* Efeito de fade para os modais */
    dialog[open] {
      animation: fadeIn 0.3s ease-out;
    }
    
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.9);
      }
      to {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
      }
    }
    
    /* Estilo para o backdrop */
    .backdrop {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 40;
    }
    
    /* Estilo para destacar pagamento a prazo */
    .payment-installment {
      background-color: #fef3c7;
      border-color: #f59e0b;
    }
    
    .payment-installment:hover {
      background-color: #fde68a;
    }
    
    /* Classe para esconder formas de pagamento quando em modo a prazo */
    .payment-form-hidden {
      display: none !important;
    }
  </style>
</head>

<body class="bg-gradient-to-br from-purple-50 to-pink-100 font-sans">
  @if($register)
    <form action="{{ route('pdv.start') }}" method="POST" id="startSaleForm">
      @csrf
      <input type="hidden" name="seller_id" id="selectedSellerId">

      <div class="container mx-auto px-2 py-4 max-w-7xl">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-lg p-4 mb-4">
          <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center space-x-3 mb-3 md:mb-0">
              <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-2 rounded-lg">
                <i class="fas fa-cash-register text-white text-xl"></i>
              </div>
              <div>
                <h1 class="text-lg md:text-3xl font-bold text-gray-900">PDV - Ponto de Venda</h1>
                <p class="text-sm text-gray-600 hidden md:block">Sistema de Vendas Moderno</p>
              </div>
            </div>
            <div class="flex items-center space-x-2">
              @if(!$sale)
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors text-sm mr-2">
                  <i class="fas fa-cart-plus mr-1"></i>Iniciar Venda
                </button>
              @endif
              <div class="bg-green-100 px-3 py-1 rounded-lg">
                <span class="text-green-800 font-medium text-sm">Caixa Aberto</span>
              </div>
              <button type="button" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg transition-colors text-sm">
                <i class="fas fa-sign-out-alt mr-1"></i>Sair
              </button>
            </div>
          </div>
        </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Painel de Produtos -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4">
            <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
              <i class="fas fa-search text-purple-600 mr-2"></i>
              Pesquisar Produtos
            </h2>
            
            <!-- Barra de Pesquisa -->
            <div class="mb-3 relative">
              <div class="relative">
                <input 
                  type="text" 
                  id="searchProduct" 
                  placeholder="Digite o nome ou código do produto..."
                  class="w-full border-2 border-purple-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500 text-sm py-2 px-3 pr-10"
                  autocomplete="off"
                >
                <div class="absolute right-2 top-1/2 transform -translate-y-1/2">
                  <i class="fas fa-search text-gray-400 text-sm"></i>
                </div>
              </div>
              
              <!-- Dropdown de Resultados -->
              <div id="searchResults" class="absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                <div class="p-3 text-gray-500 text-center">
                  <i class="fas fa-search text-lg mb-2"></i>
                  <p class="text-sm">Digite para pesquisar produtos</p>
                </div>
              </div>
            </div>
            
            <!-- Botões de Ação -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-3">
              <button onclick="document.getElementById('modalProdutoAvulso').showModal()" 
                      class="bg-pink-600 hover:bg-pink-700 text-white py-2 px-3 rounded-lg font-semibold text-xs shadow-lg transition-all duration-200 hover:shadow-xl flex items-center justify-center space-x-1">
                <i class="fas fa-plus-circle text-sm"></i>
                <span>Venda Avulsa</span>
              </button>
              
              <button onclick="document.getElementById('modalConsultaPreco').showModal()" 
                      class="bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-3 rounded-lg font-semibold text-xs shadow-lg transition-all duration-200 hover:shadow-xl flex items-center justify-center space-x-1">
                <i class="fas fa-search-dollar text-sm"></i>
                <span>Consultar Preço</span>
              </button>
              
              <button onclick="cancelarVenda()" 
                      class="bg-red-500 hover:bg-red-600 text-white py-2 px-3 rounded-lg font-semibold text-xs shadow-lg transition-all duration-200 hover:shadow-xl flex items-center justify-center space-x-1">
                <i class="fas fa-times-circle text-sm"></i>
                <span>Cancelar Venda</span>
              </button>
              <!-- Botão Gerador Teste Boletos -->
              <!-- <button onclick="document.getElementById('modalTesteBoleto').showModal()" 
                      class="bg-blue-700 hover:bg-blue-800 text-white py-2 px-3 rounded-lg font-semibold text-xs shadow-lg transition-all duration-200 hover:shadow-xl flex items-center justify-center space-x-1">
                <i class="fas fa-file-invoice-dollar text-sm"></i>
                <span>Gerador Teste Boletos</span>
              </button> -->
            </div>

            <!-- Instruções de Uso -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-3">
              <div class="flex items-center mb-2">
                <i class="fas fa-info-circle text-purple-600 mr-2"></i>
                <h3 class="text-sm font-semibold text-gray-900">Como usar o sistema</h3>
              </div>
              <ul class="space-y-1 text-xs text-gray-700">
                <li class="flex items-center">
                  <i class="fas fa-search text-purple-500 mr-2 text-xs"></i>
                  <span>Digite o nome ou código do produto na barra de pesquisa</span>
                </li>
                <li class="flex items-center">
                  <i class="fas fa-plus text-pink-500 mr-2 text-xs"></i>
                  <span>Use "Venda Avulsa" para vender produtos cadastrados</span>
                </li>
                <li class="flex items-center">
                  <i class="fas fa-dollar-sign text-yellow-500 mr-2 text-xs"></i>
                  <span>Consulte preços sem adicionar ao carrinho</span>
                </li>
                <li class="flex items-center">
                  <i class="fas fa-shopping-cart text-green-500 mr-2 text-xs"></i>
                  <span>Finalize suas vendas com pagamento à vista ou a prazo</span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Carrinho -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-xl shadow-lg p-3 sm:p-4 sticky top-2">
            <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
              <i class="fas fa-shopping-cart text-green-600 mr-2"></i>
              Carrinho
            </h2>
            
            <div id="itensCarrinho" class="space-y-2 mb-3 max-h-48 overflow-y-auto">
              <div class="text-center text-gray-500 py-6">
                <i class="fas fa-cart-plus text-3xl mb-3"></i>
                <p class="text-sm">Carrinho vazio</p>
              </div>
            </div>
            
            <!-- Totais -->
            <div class="border-t pt-3 space-y-1">
              <div class="flex justify-between text-sm">
                <span>Subtotal:</span>
                <span id="subtotal">R$ 0,00</span>
              </div>
              <div class="flex justify-between text-sm">
                <span>Desconto:</span>
                <span id="desconto">R$ 0,00</span>
              </div>
              <div class="flex justify-between text-base font-bold border-t pt-2">
                <span>Total:</span>
                <span id="total">R$ 0,00</span>
              </div>
            </div>
            
            <!-- Botões de Ação -->
            <div class="mt-3 space-y-2">
              <button onclick="aplicarDesconto()" 
                      class="w-full hover:bg-orange-500 bg-orange-600 text-white py-2 rounded-lg font-semibold text-sm transition-colors flex items-center justify-center space-x-2">
                <i class="fas fa-percentage"></i>
                <span>Aplicar Desconto</span>
              </button>
              
              <button onclick="finalizarVenda()" 
                      id="btnFinalizar"
                      class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-bold text-sm transition-colors flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-credit-card"></i>
                <span>Finalizar Venda</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif

  <!-- Modal Venda Avulsa -->
  <dialog id="modalProdutoAvulso">
    <div class="bg-white rounded-xl shadow-2xl p-4 sm:p-6 w-full">
      <div class="text-center mb-4">
        <i class="fas fa-plus-circle text-2xl sm:text-3xl text-pink-500 mb-3"></i>
        <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Venda Avulsa</h2>
        <p class="text-sm text-gray-600">Selecione um produto da base de dados</p>
      </div>
      
      <div class="space-y-3 sm:space-y-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Produto</label>
          <div class="relative">
            <input 
              type="text" 
              id="avulsoProduto" 
              placeholder="Digite o nome ou código do produto..."
              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-2 focus:ring-pink-500 text-sm py-2 px-3 pr-10"
              autocomplete="off"
            >
            <div class="absolute right-2 top-1/2 transform -translate-y-1/2">
              <i class="fas fa-search text-gray-400 text-sm"></i>
            </div>
            
            <!-- Dropdown de Resultados -->
            <div id="avulsoSearchResults" class="absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
              <div class="p-3 text-gray-500 text-center">
                <i class="fas fa-search text-lg mb-2"></i>
                <p class="text-sm">Digite para pesquisar produtos</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Valor (R$)</label>
            <input type="number" id="avulsoValor" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-2 focus:ring-pink-500 text-sm py-2 px-3" placeholder="0,00" step="0.01">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Quantidade</label>
            <input type="number" id="avulsoQtd" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-2 focus:ring-pink-500 text-sm py-2 px-3" value="1" min="1">
          </div>
        </div>
      </div>
      
      <div class="mt-4 sm:mt-6 flex flex-col sm:flex-row gap-2 sm:gap-3">
        <button type="button" id="btnSalvarAvulso" class="w-full sm:flex-1 bg-pink-600 hover:bg-pink-700 text-white py-3 rounded-lg font-semibold text-sm shadow-lg transition-all duration-200 hover:shadow-xl flex items-center justify-center space-x-2">
          <i class="fas fa-plus"></i>
          <span>Adicionar ao Carrinho</span>
        </button>
        <button type="button" onclick="this.closest('dialog').close()" class="w-full sm:flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 rounded-lg font-semibold text-sm transition-all duration-200 flex items-center justify-center space-x-2">
          <i class="fas fa-times"></i>
          <span>Cancelar</span>
        </button>
      </div>
    </div>
  </dialog>

  <!-- Modal Consulta Preço -->
  <dialog id="modalConsultaPreco">
    <div class="bg-white rounded-xl shadow-2xl p-6 sm:p-8 w-full">
      <div class="text-center mb-6">
        <i class="fas fa-search-dollar text-3xl sm:text-4xl text-yellow-500 mb-4"></i>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">Consultar Preço</h2>
        <p class="text-sm sm:text-base text-gray-600">Selecione um produto para ver o preço</p>
      </div>
      <div class="space-y-4 sm:space-y-6">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Produto</label>
          <select id="produtoConsulta" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500 text-base py-3 px-4">
            <option value="">Selecione um produto...</option>
            @foreach($products as $product)
              <option value="{{ $product->id }}" data-preco="{{ $product->sale_price }}">
                {{ $product->name }} @if($product->internal_code) - {{ $product->internal_code }} @endif
              </option>
            @endforeach
          </select>
        </div>
        <div id="resultadoConsulta" class="hidden bg-yellow-50 p-4 sm:p-6 rounded-lg border-2 border-yellow-200">
          <div class="text-center">
            <div class="text-2xl sm:text-3xl font-bold text-yellow-600 mb-2" id="precoConsulta">R$ 0,00</div>
            <div class="text-sm text-gray-600" id="nomeConsulta">-</div>
          </div>
        </div>
      </div>
      <div class="mt-6 sm:mt-8 flex justify-center">
        <button type="button" onclick="this.closest('dialog').close()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-8 py-3 rounded-lg font-semibold text-base">
          Fechar
        </button>
      </div>
    </div>
  </dialog>

  <!-- Modal de Cancelamento de Venda -->
  <dialog id="modalCancelarVenda">
    <div class="bg-white rounded-xl shadow-2xl p-6 sm:p-8 w-full">
      <div class="text-center mb-6">
        <i class="fas fa-exclamation-triangle text-3xl sm:text-4xl text-red-500 mb-4"></i>
        <h2 class="text-xl sm:text-2xl font-bold text-red-600 mb-2">Cancelar Venda</h2>
        <p class="text-sm sm:text-base text-gray-600">Esta ação irá cancelar a venda atual e não pode ser desfeita</p>
      </div>
      
      <div class="space-y-4">
        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
          <div class="flex items-center space-x-3">
            <i class="fas fa-info-circle text-red-500"></i>
            <div>
              <p class="font-semibold text-red-800">Atenção!</p>
              <p class="text-red-600 text-sm">Se a venda já foi finalizada, o estoque será revertido automaticamente.</p>
            </div>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Motivo do cancelamento (opcional):</label>
          <textarea id="motivoCancelamento" rows="3" placeholder="Ex: Cliente desistiu, erro no pedido..." 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"></textarea>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 justify-end">
          <button onclick="document.getElementById('modalCancelarVenda').close()" 
                  class="w-full sm:w-auto px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors font-semibold">
            <i class="fas fa-times mr-2"></i>Não Cancelar
          </button>
          <button onclick="confirmarCancelamento()" 
                  class="w-full sm:w-auto px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold">
            <i class="fas fa-check mr-2"></i>Confirmar Cancelamento
          </button>
        </div>
      </div>
    </div>
  </dialog>

  <!-- Modal de Desconto por Item -->
  <dialog id="modalDescontoItem">
    <div class="bg-white rounded-xl shadow-2xl p-6 sm:p-8 w-full">
      <div class="text-center mb-6">
        <i class="fas fa-percentage text-3xl sm:text-4xl text-blue-500 mb-4"></i>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">Aplicar Desconto</h2>
        <p class="text-sm sm:text-base text-gray-600" id="itemDescontoNome">Produto</p>
      </div>
      
      <div class="space-y-4">
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
          <div class="flex items-center space-x-3">
            <i class="fas fa-info-circle text-blue-500"></i>
            <div>
              <p class="font-semibold text-blue-800">Informações do Item</p>
              <p class="text-blue-600 text-sm" id="itemDescontoInfo">Quantidade x Preço = Total</p>
            </div>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Desconto:</label>
          <div class="flex space-x-4 mb-4">
            <label class="flex items-center">
              <input type="radio" name="tipoDescontoItem" value="value" checked class="mr-2">
              <span class="text-sm">Valor (R$)</span>
            </label>
            <label class="flex items-center">
              <input type="radio" name="tipoDescontoItem" value="percentage" class="mr-2">
              <span class="text-sm">Porcentagem (%)</span>
            </label>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Valor do Desconto:</label>
          <input type="number" id="valorDescontoItem" step="0.01" min="0" 
                 class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                 placeholder="Digite o valor do desconto">
        </div>
        
        <div class="p-3 bg-gray-50 rounded-lg">
          <p class="text-sm text-gray-600">Total atual: <span id="totalAtualItem" class="font-semibold">R$ 0,00</span></p>
          <p class="text-sm text-gray-600">Total com desconto: <span id="totalComDescontoItem" class="font-semibold text-green-600">R$ 0,00</span></p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 justify-end">
          <button onclick="document.getElementById('modalDescontoItem').close()" 
                  class="w-full sm:w-auto px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors font-semibold">
            <i class="fas fa-times mr-2"></i>Cancelar
          </button>
          <button onclick="confirmarDescontoItem()" 
                  class="w-full sm:w-auto px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
            <i class="fas fa-check mr-2"></i>Aplicar Desconto
          </button>
        </div>
      </div>
    </div>
  </dialog>

  <!-- Modal Finalizar Venda -->
  <dialog id="modalFinalizar">
    <div class="bg-white rounded-xl shadow-2xl p-4 sm:p-6 w-full max-w-2xl">
      <div class="text-center mb-4">
        <i class="fas fa-credit-card text-2xl sm:text-3xl text-green-500 mb-3"></i>
        <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Finalizar Venda</h2>
        <p class="text-sm text-gray-600">Escolha a forma de pagamento</p>
      </div>
      
      <div class="space-y-4">
        <!-- Resumo da Venda -->
        <div class="bg-gray-50 p-3 rounded-lg">
          <h3 class="font-semibold text-gray-900 mb-2">Resumo da Venda</h3>
          <div class="space-y-1 text-sm">
            <div class="flex justify-between">
              <span>Subtotal:</span>
              <span id="resumoSubtotal">R$ 0,00</span>
            </div>
            <div class="flex justify-between">
              <span>Desconto:</span>
              <span id="resumoDesconto">R$ 0,00</span>
            </div>
            <div class="flex justify-between font-bold text-base border-t pt-2">
              <span>Total:</span>
              <span id="resumoTotal">R$ 0,00</span>
            </div>
          </div>
        </div>

        <!-- Modo de Pagamento -->
        <div>
          <h3 class="font-semibold text-gray-900 mb-3">Seleção do Cliente</h3>
          <div class="mb-4">
            <select id="customerSelect" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-500 text-sm py-2 px-3">
              <option value="">Selecione o cliente (opcional)...</option>
              @foreach($customers as $customer)
                <option value="{{ $customer->id }}">
                  {{ $customer->name }} @if($customer->cpf_cnpj) - {{ $customer->cpf_cnpj }} @endif
                </option>
              @endforeach
            </select>
          </div>

          <h3 class="font-semibold text-gray-900 mb-3">
            <span class="flex items-center">
              <span>Seleção do Vendedor</span>
              <span class="text-gray-500 ml-1 text-xs">(opcional)</span>
            </span>
          </h3>
          <div class="mb-4">
            <select id="sellerSelect" name="seller_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-500 text-sm py-2 px-3">
             
              @foreach($sellers as $seller)
                <option value="{{ $seller->id }}" data-commission="{{ $seller->commission_rate }}" {{ old('seller_id') == $seller->id ? 'selected' : '' }}>
                  {{ $seller->name }} - {{ number_format($seller->commission_rate, 1) }}%
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <!-- Modo de Pagamento -->
        <div>
          <h3 class="font-semibold text-gray-900 mb-3">Modo de Pagamento</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 transition-colors">
              <input type="radio" name="modoPagamento" value="cash" class="mr-2" checked>
              <div>
                <div class="font-semibold text-gray-900 text-sm">À Vista</div>
                <div class="text-xs text-gray-600">Pagamento imediato</div>
              </div>
            </label>
            <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-yellow-500 transition-colors payment-installment-option">
              <input type="radio" name="modoPagamento" value="installment" class="mr-2">
              <div>
                <div class="font-semibold text-gray-900 text-sm">A Prazo</div>
                <div class="text-xs text-gray-600">Pagamento futuro</div>
              </div>
            </label>
          </div>
        </div>

        <!-- Opções de Pagamento a Prazo -->
        <div id="opcoesPrazo" class="hidden">
          <h3 class="font-semibold text-gray-900 mb-3">Configurações do Pagamento a Prazo</h3>
          <div class="space-y-3">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Data de Vencimento</label>
              <input type="date" id="dataVencimento" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500 text-sm py-2 px-3">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Observações</label>
              <textarea id="observacoesPrazo" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500 text-sm py-2 px-3 resize-none" rows="2" placeholder="Observações sobre o pagamento a prazo..."></textarea>
            </div>
          </div>
        </div>

        <!-- Formas de Pagamento -->
        <div>
          <h3 class="font-semibold text-gray-900 mb-3">Formas de Pagamento</h3>
          <div id="formasPagamento" class="space-y-4">
            <div class="payment-form-container space-y-3">
              <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">
                <select class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-500 text-sm py-2 px-3 forma-pagamento">
                  <option value="">Selecione...</option>
                  <option value="dinheiro">Dinheiro</option>
                  <option value="pix">PIX</option>
                  <option value="cartao_debito">Cartão de Débito</option>
                  <option value="cartao_credito">Cartão de Crédito</option>
                  <option value="prazo" class="prazo-option hidden">A Prazo</option>
                </select>
                <input type="number" placeholder="R$ 0,00" class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-500 text-sm py-2 px-3 valor-pagamento" step="0.01">
              </div>
              
              <div class="flex justify-center">
                  <button type="button" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-black px-8 py-3 rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl btn-adicionar-pagamento flex items-center justify-center space-x-2 text-lg">
                    <i class="fas fa-plus text-lg"></i>
                    <span>Adicionar Valor</span>
                  </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagamentos Adicionados -->
        <div>
          <h3 class="font-semibold text-gray-900 mb-3">Pagamentos Adicionados</h3>
          <div id="pagamentosAdicionados" class="space-y-2 max-h-40 overflow-y-auto mb-4">
            <div class="text-center text-gray-500 py-4">
              <i class="fas fa-credit-card text-2xl mb-2"></i>
              <p>Nenhum pagamento adicionado</p>
            </div>
          </div>
           <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Observações</label>
              <textarea id="observacoesVenda" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500 text-sm py-2 px-3 resize-none" rows="2" placeholder="Observações sobre o pagamento..."></textarea>
            </div>
          <!-- Resumo de Troco/Falta -->
          <div id="resumoTroco" class="hidden">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
              <div class="flex items-center justify-between">
                <div>
                  <span class="text-sm font-medium text-gray-700">Diferença:</span>
                  <span id="textoTroco" class="text-sm text-gray-600 ml-2"></span>
                </div>
                <div>
                  <span id="valorTroco" class="text-lg font-bold"></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="mt-4 sm:mt-6 flex flex-col sm:flex-row gap-2 sm:gap-3">
        <button type="button" id="btnConfirmarVenda" class="w-full sm:flex-1 bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold text-sm shadow-lg transition-all duration-200 hover:shadow-xl flex items-center justify-center space-x-2">
          <i class="fas fa-check"></i>
          <span>Confirmar Venda</span>
        </button>
        <button type="button" onclick="this.closest('dialog').close()" class="w-full sm:flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 rounded-lg font-semibold text-sm transition-all duration-200 flex items-center justify-center space-x-2">
          <i class="fas fa-times"></i>
          <span>Cancelar</span>
        </button>
      </div>
    </div>
  </dialog>

  <!-- Modal Gerador Teste Boletos -->
  <dialog id="modalTesteBoleto">
    <div class="bg-white rounded-xl shadow-2xl p-6 sm:p-8 w-full max-w-lg">
      <div class="text-center mb-6">
        <i class="fas fa-file-invoice-dollar text-4xl text-blue-700 mb-3"></i>
        <h2 class="text-xl font-bold text-gray-900 mb-2">Gerador Teste Boletos Sicredi</h2>
        <p class="text-sm text-gray-600">Teste a geração de boletos Sicredi sem afetar vendas reais.</p>
      </div>
      <form id="formTesteBoleto" class="space-y-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Nome do Cliente</label>
          <input type="text" name="nome" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-700 focus:ring-2 focus:ring-blue-700 text-sm py-2 px-3" placeholder="Nome completo">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">CPF/CNPJ</label>
            <input type="text" name="documento" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-700 focus:ring-2 focus:ring-blue-700 text-sm py-2 px-3" placeholder="Somente números">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Valor (R$)</label>
            <input type="number" name="valor" required step="0.01" min="1" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-700 focus:ring-2 focus:ring-blue-700 text-sm py-2 px-3" placeholder="0,00">
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Data de Vencimento</label>
            <input type="date" name="vencimento" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-700 focus:ring-2 focus:ring-blue-700 text-sm py-2 px-3">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Seu Número</label>
            <input type="text" name="seu_numero" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-700 focus:ring-2 focus:ring-blue-700 text-sm py-2 px-3" placeholder="Identificação interna">
          </div>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Instruções</label>
          <input type="text" name="instrucao" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-700 focus:ring-2 focus:ring-blue-700 text-sm py-2 px-3" placeholder="Ex: Não aceitar após o vencimento">
        </div>
        <div class="mt-4 flex gap-2">
          <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white py-3 rounded-lg font-semibold text-sm shadow-lg transition-all duration-200 flex items-center justify-center space-x-2">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Testar Geração</span>
          </button>
          <button type="button" onclick="this.closest('dialog').close()" class="w-full bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 rounded-lg font-semibold text-sm transition-all duration-200 flex items-center justify-center space-x-2">
            <i class="fas fa-times"></i>
            <span>Cancelar</span>
          </button>
        </div>
        <div id="resultadoTesteBoleto" class="mt-4"></div>
      </form>
    </div>
  </dialog>

  @if(!$register)
    <div class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center">
      <div class="bg-white rounded-lg shadow-xl p-8 text-center max-w-md mx-4">
        <i class="fas fa-lock text-red-500 text-4xl mb-4"></i>
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Caixa Fechado</h2>
        <p class="text-gray-600 mb-6">Você precisa abrir um caixa para operar o PDV.</p>
        <a href="{{ route('caixa.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
          Ir para Frente de Caixa
        </a>
      </div>
    </div>
  @endif

  <script>
    // Função auxiliar para operações DOM seguras
    function safeGetElement(selector, parent = document) {
      const element = parent.querySelector ? parent.querySelector(selector) : parent.getElementById(selector.replace('#', ''));
      if (!element) {
        console.warn(`Elemento não encontrado: ${selector}`);
      }
      return element;
    }
    
    function safeGetElementById(id) {
      const element = document.getElementById(id);
      if (!element) {
        console.warn(`Elemento não encontrado com ID: ${id}`);
      }
      return element;
    }

    // Gerador Teste Boletos Sicredi
    document.addEventListener('DOMContentLoaded', function() {
      const formTesteBoleto = document.getElementById('formTesteBoleto');
      if (formTesteBoleto) {
        formTesteBoleto.addEventListener('submit', function(e) {
          e.preventDefault();
          const resultado = document.getElementById('resultadoTesteBoleto');
          resultado.innerHTML = '<div class="text-center text-blue-700"><i class="fas fa-spinner fa-spin text-2xl"></i><p class="mt-2">Testando geração de boleto...</p></div>';
          const dados = {
            nome: formTesteBoleto.nome.value,
            documento: formTesteBoleto.documento.value,
            valor: formTesteBoleto.valor.value,
            vencimento: formTesteBoleto.vencimento.value,
            seu_numero: formTesteBoleto.seu_numero.value,
            instrucao: formTesteBoleto.instrucao.value
          };
          fetch('/pdv/testar-boleto', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(dados)
          })
          .then(response => response.json())
          .then(data => {
            if (data.success && data.boleto_url) {
              resultado.innerHTML = `<div class="text-center text-green-700"><i class="fas fa-check-circle text-2xl mb-2"></i><p>Boleto gerado com sucesso!</p><a href="${data.boleto_url}" target="_blank" class="inline-block mt-3 bg-blue-700 hover:bg-blue-800 text-white px-6 py-2 rounded-lg font-semibold">Baixar Boleto PDF</a></div>`;
            } else {
              resultado.innerHTML = `<div class="text-center text-red-700"><i class="fas fa-times-circle text-2xl mb-2"></i><p>Falha ao gerar boleto: ${data.error || 'Erro desconhecido'}</p></div>`;
            }
          })
          .catch(error => {
            resultado.innerHTML = `<div class="text-center text-red-700"><i class="fas fa-times-circle text-2xl mb-2"></i><p>Erro: ${error.message}</p></div>`;
          });
        });
      }
    });

    // Variáveis globais
    let carrinho = [];
    let totalDesconto = 0;
    let pagamentos = [];
    
    // Variáveis para controle do desconto
    window.descontoTipo = 'value';
    window.descontoValor = 0;

    // Configurar data mínima para pagamento a prazo (amanhã)
    document.addEventListener('DOMContentLoaded', function() {
      const dataVencimento = safeGetElementById('dataVencimento');
      if (dataVencimento) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        dataVencimento.min = tomorrow.toISOString().split('T')[0];
        dataVencimento.value = tomorrow.toISOString().split('T')[0];
      }
      
      // Inicializar carrinho se houver itens do servidor
      atualizarCarrinho();
    });

    // Controlar exibição das opções de pagamento a prazo
    document.addEventListener('change', function(e) {
      if (e.target.name === 'modoPagamento') {
        const opcoesPrazo = safeGetElementById('opcoesPrazo');
        const prazoOptions = document.querySelectorAll('.prazo-option');
        const formasPagamento = document.querySelector('.payment-form-container');
        const pagamentosAdicionados = document.getElementById('pagamentosAdicionados');
        
        if (e.target.value === 'installment') {
          if (opcoesPrazo) opcoesPrazo.classList.remove('hidden');
          prazoOptions.forEach(option => option.classList.remove('hidden'));
          
          // Esconder formas de pagamento normais
          if (formasPagamento) formasPagamento.style.display = 'none';
          
          // Configurar pagamento automático a prazo quando uma data for selecionada
          const dataVencimento = safeGetElementById('dataVencimento');
          if (dataVencimento) {
            dataVencimento.addEventListener('change', function() {
              if (this.value) {
                // Limpar pagamentos anteriores
                pagamentos = [];
                
                // Calcular total da venda
                const totalVenda = carrinho.reduce((sum, item) => sum + item.total, 0) - totalDesconto;
                
                // Adicionar pagamento a prazo automaticamente
                pagamentos.push({ 
                  tipo: 'prazo', 
                  valor: totalVenda,
                  data_vencimento: this.value
                });
                
                // Atualizar exibição
                atualizarPagamentos();
                
                // Mostrar mensagem de confirmação
                if (pagamentosAdicionados) {
                  // Função para formatar data corretamente (evitar problemas de fuso horário)
                  const formatarDataVencimento = (dataISO) => {
                    const partes = dataISO.split('-');
                    const ano = partes[0];
                    const mes = partes[1];
                    const dia = partes[2];
                    return `${dia}/${mes}/${ano}`;
                  };
                  
                  pagamentosAdicionados.innerHTML = `
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                      <div class="flex items-center">
                        <i class="fas fa-calendar-alt text-yellow-600 text-xl mr-3"></i>
                        <div>
                          <h4 class="font-semibold text-gray-900">Pagamento A Prazo</h4>
                          <p class="text-sm text-gray-600">Vencimento: ${formatarDataVencimento(this.value)}</p>
                          <p class="text-sm font-semibold text-gray-800">Valor: R$ ${totalVenda.toFixed(2).replace('.', ',')}</p>
                        </div>
                      </div>
                    </div>
                  `;
                }
              }
            });
          }
        } else {
          if (opcoesPrazo) opcoesPrazo.classList.add('hidden');
          prazoOptions.forEach(option => option.classList.add('hidden'));
          
          // Mostrar formas de pagamento normais
          if (formasPagamento) formasPagamento.style.display = 'block';
          
          // Limpar pagamentos a prazo se mudou para à vista
          pagamentos = pagamentos.filter(pag => pag.tipo !== 'prazo');
          atualizarPagamentos();
        }
      }
    });

    // Adicionar produto ao carrinho
    function adicionarProduto(id, nome, preco) {
      const item = {
        id: id,
        nome: nome,
        unitario: preco,
        qtd: 1,
        total: preco
      };
      
      // Verificar se o produto já existe no carrinho
      const existente = carrinho.find(p => p.id === id);
      if (existente) {
        existente.qtd += 1;
        existente.total = existente.unitario * existente.qtd;
      } else {
        carrinho.push(item);
      }
      
      atualizarCarrinho();
    }

    // Adicionar produto avulso
    const btnSalvarAvulso = safeGetElementById('btnSalvarAvulso');
    if (btnSalvarAvulso) {
      btnSalvarAvulso.addEventListener('click', function() {
        const valorElement = safeGetElementById('avulsoValor');
        const qtdElement = safeGetElementById('avulsoQtd');
        
        if (!valorElement || !qtdElement) {
          alert('Erro ao encontrar elementos do formulário!');
          return;
        }
        
        if (!avulsoProdutoSelecionado) {
          alert('Selecione um produto da lista!');
          return;
        }
        
        const valor = parseFloat(valorElement.value);
        const qtd = parseInt(qtdElement.value);
        
        if (!valor || !qtd) {
          alert('Preencha todos os campos!');
          return;
        }

        // Validar estoque
        if (qtd > avulsoProdutoSelecionado.estoque) {
          alert(`Quantidade indisponível! Estoque atual: ${avulsoProdutoSelecionado.estoque}`);
          return;
        }

        // Verificar se o produto já está no carrinho
        const produtoExistente = carrinho.find(item => item.id === avulsoProdutoSelecionado.id);
        const qtdTotalNoCarrinho = produtoExistente ? produtoExistente.qtd + qtd : qtd;
        
        if (qtdTotalNoCarrinho > avulsoProdutoSelecionado.estoque) {
          alert(`Quantidade total excede o estoque! Já há ${produtoExistente ? produtoExistente.qtd : 0} no carrinho. Estoque disponível: ${avulsoProdutoSelecionado.estoque}`);
          return;
        }
      
      const item = {
        id: avulsoProdutoSelecionado.id,
        nome: avulsoProdutoSelecionado.nome,
        unitario: valor,
        qtd: qtd,
        total: valor * qtd
      };
      
      carrinho.push(item);
      atualizarCarrinho();
      
      // Limpar campos
      const avulsoProduto = safeGetElementById('avulsoProduto');
      const avulsoValor = safeGetElementById('avulsoValor');
      const avulsoQtd = safeGetElementById('avulsoQtd');
      const modal = safeGetElementById('modalProdutoAvulso');
      
      if (avulsoProduto) avulsoProduto.value = '';
      if (avulsoValor) avulsoValor.value = '';
      if (avulsoQtd) avulsoQtd.value = '1';
      avulsoProdutoSelecionado = null;
      
      // Fechar modal
      if (modal) modal.close();
    });
    }

    // Busca de produtos para venda avulsa
    const avulsoProdutoInput = safeGetElementById('avulsoProduto');
    const avulsoSearchResults = document.getElementById('avulsoSearchResults');
    let avulsoProdutoSelecionado = null;
    
    // Lista de produtos para busca
    const produtosAvulso = [
      @foreach($products as $product)
      {
        id: {{ $product->id }},
        nome: "{{ $product->name }}",
        codigo: "{{ $product->internal_code ?? '' }}",
        preco: {{ $product->sale_price }},
        estoque: {{ $product->stock_quantity ?? 0 }},
        texto: "{{ $product->name }} @if($product->internal_code) - {{ $product->internal_code }} @endif"
      },
      @endforeach
    ];
    
    if (avulsoProdutoInput && avulsoSearchResults) {
      avulsoProdutoInput.addEventListener('input', function() {
        const termo = this.value.toLowerCase().trim();
        
        if (termo.length < 2) {
          avulsoSearchResults.classList.add('hidden');
          avulsoProdutoSelecionado = null;
          const avulsoValor = safeGetElementById('avulsoValor');
          if (avulsoValor) avulsoValor.value = '';
          return;
        }
        
        const resultados = produtosAvulso.filter(produto => 
          produto.nome.toLowerCase().includes(termo) ||
          produto.codigo.toLowerCase().includes(termo) ||
          produto.texto.toLowerCase().includes(termo)
        ).slice(0, 10);
        
        if (resultados.length > 0) {
          avulsoSearchResults.innerHTML = resultados.map(produto => `
            <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 produto-resultado" 
                 data-id="${produto.id}" 
                 data-nome="${produto.nome}" 
                 data-preco="${produto.preco}"
                 data-estoque="${produto.estoque}">
              <div class="font-medium text-gray-900">${produto.nome}</div>
              <div class="text-sm text-gray-600">
                ${produto.codigo ? `Código: ${produto.codigo} - ` : ''}R$ ${produto.preco.toFixed(2).replace('.', ',')}
                <span class="ml-2 ${produto.estoque > 0 ? 'text-green-600' : 'text-red-600'}">
                  Estoque: ${produto.estoque}
                </span>
              </div>
            </div>
          `).join('');
          avulsoSearchResults.classList.remove('hidden');
        } else {
          avulsoSearchResults.innerHTML = `
            <div class="p-3 text-gray-500 text-center">
              <i class="fas fa-search text-lg mb-2"></i>
              <p class="text-sm">Nenhum produto encontrado</p>
            </div>
          `;
          avulsoSearchResults.classList.remove('hidden');
        }
      });
      
      // Selecionar produto do dropdown
      avulsoSearchResults.addEventListener('click', function(e) {
        const produtoDiv = e.target.closest('.produto-resultado');
        if (produtoDiv) {
          const id = produtoDiv.dataset.id;
          const nome = produtoDiv.dataset.nome;
          const preco = parseFloat(produtoDiv.dataset.preco);
          const estoque = parseInt(produtoDiv.dataset.estoque);
          
          avulsoProdutoInput.value = nome;
          avulsoProdutoSelecionado = { id, nome, preco, estoque };
          
          const avulsoValor = safeGetElementById('avulsoValor');
          const avulsoQtd = safeGetElementById('avulsoQtd');
          if (avulsoValor) avulsoValor.value = preco.toFixed(2);
          if (avulsoQtd) avulsoQtd.max = estoque;
          
          avulsoSearchResults.classList.add('hidden');
        }
      });
      
      // Fechar dropdown ao clicar fora
      document.addEventListener('click', function(e) {
        if (!e.target.closest('#avulsoProduto') && !e.target.closest('#avulsoSearchResults')) {
          avulsoSearchResults.classList.add('hidden');
        }
      });
    }

    // Atualizar carrinho
    function atualizarCarrinho() {
      const container = document.getElementById('itensCarrinho');
      
      if (carrinho.length === 0) {
        container.innerHTML = `
          <div class="text-center text-gray-500 py-6">
            <i class="fas fa-cart-plus text-3xl mb-3"></i>
            <p class="text-sm">Carrinho vazio</p>
          </div>
        `;
      } else {
        container.innerHTML = carrinho.map((item, index) => `
          <div class="bg-gray-50 p-2 rounded-lg mb-2">
            <div class="flex items-center justify-between mb-1">
              <div class="flex-1 min-w-0">
                <h4 class="font-semibold text-xs text-gray-900 truncate">${item.nome}</h4>
                <p class="text-xs text-gray-600">${item.qtd}x R$ ${item.unitario.toFixed(2).replace('.', ',')}</p>
              </div>
              <div class="flex items-center space-x-1">
                <button onclick="aplicarDescontoItem(${index})" class="text-blue-500 hover:text-blue-700 p-1" title="Aplicar desconto">
                  <i class="fas fa-percentage text-xs"></i>
                </button>
                <button onclick="removerItem(${index})" class="text-red-500 hover:text-red-700 p-1" title="Remover item">
                  <i class="fas fa-trash text-xs"></i>
                </button>
              </div>
            </div>
            <div class="flex justify-between items-center text-xs">
              ${item.desconto && item.desconto > 0 ? `
                <div class="text-gray-500">
                  <span class="line-through">R$ ${(item.unitario * item.qtd).toFixed(2).replace('.', ',')}</span>
                  <span class="text-red-600 ml-1">-R$ ${item.desconto.toFixed(2).replace('.', ',')}</span>
                </div>
              ` : ''}
              <span class="font-bold text-green-600">R$ ${item.total.toFixed(2).replace('.', ',')}</span>
            </div>
          </div>
        `).join('');
      }
      
      calcularTotais();
    }

    // Remover item do carrinho
    function removerItem(index) {
      carrinho.splice(index, 1);
      atualizarCarrinho();
    }

    // Calcular totais
    function calcularTotais() {
      const subtotal = carrinho.reduce((sum, item) => sum + item.total, 0);
      const total = subtotal - totalDesconto;
      
      const elements = {
        subtotal: safeGetElementById('subtotal'),
        desconto: safeGetElementById('desconto'),
        total: safeGetElementById('total'),
        resumoSubtotal: safeGetElementById('resumoSubtotal'),
        resumoDesconto: safeGetElementById('resumoDesconto'),
        resumoTotal: safeGetElementById('resumoTotal'),
        btnFinalizar: safeGetElementById('btnFinalizar')
      };
      
      if (elements.subtotal) elements.subtotal.textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
      if (elements.desconto) elements.desconto.textContent = `R$ ${totalDesconto.toFixed(2).replace('.', ',')}`;
      if (elements.total) elements.total.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
      
      // Atualizar resumo no modal
      if (elements.resumoSubtotal) elements.resumoSubtotal.textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
      if (elements.resumoDesconto) elements.resumoDesconto.textContent = `R$ ${totalDesconto.toFixed(2).replace('.', ',')}`;
      if (elements.resumoTotal) elements.resumoTotal.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
      
      // Habilitar/desabilitar botão finalizar
      if (elements.btnFinalizar) elements.btnFinalizar.disabled = carrinho.length === 0;
    }

    // Aplicar desconto
    function aplicarDesconto() {
      const modal = document.createElement('dialog');
      modal.innerHTML = `
        <div class="bg-white p-6 rounded-lg max-w-md">
          <h3 class="text-lg font-bold mb-4">Aplicar Desconto</h3>
          
          <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Tipo de Desconto:</label>
            <select id="tipoDesconto" class="w-full p-2 border rounded">
              <option value="value">Valor (R$)</option>
              <option value="percentage">Porcentagem (%)</option>
            </select>
          </div>
          
          <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Valor do Desconto:</label>
            <input type="number" id="valorDesconto" step="0.01" min="0" class="w-full p-2 border rounded" placeholder="0,00">
          </div>
          
          <div class="flex gap-2">
            <button onclick="confirmarDesconto()" class="flex-1 bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Aplicar</button>
            <button onclick="fecharModalDesconto()" class="flex-1 bg-gray-300 text-gray-700 p-2 rounded hover:bg-gray-400">Cancelar</button>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      modal.showModal();
      
      window.confirmarDesconto = function() {
        const tipo = document.getElementById('tipoDesconto').value;
        const valor = parseFloat(document.getElementById('valorDesconto').value);
        
        if (isNaN(valor) || valor < 0) {
          alert('Digite um valor válido!');
          return;
        }
        
        if (tipo === 'percentage') {
          if (valor > 100) {
            alert('Desconto não pode ser maior que 100%!');
            return;
          }
          const subtotal = carrinho.reduce((acc, item) => acc + item.total, 0);
          totalDesconto = (subtotal * valor) / 100;
          window.descontoTipo = 'percentage';
          window.descontoValor = valor;
        } else {
          const subtotal = carrinho.reduce((acc, item) => acc + item.total, 0);
          if (valor > subtotal) {
            alert('Desconto não pode ser maior que o total da venda!');
            return;
          }
          totalDesconto = valor;
          window.descontoTipo = 'value';
          window.descontoValor = valor;
        }
        
        calcularTotais();
        modal.remove();
      };
      
      window.fecharModalDesconto = function() {
        modal.remove();
      };
    }

    // Cancelar venda
    function cancelarVenda() {
      document.getElementById('modalCancelarVenda').showModal();
    }

    // Confirmar cancelamento de venda
    function confirmarCancelamento() {
      const motivo = document.getElementById('motivoCancelamento').value;
      
      // Fechar modal
      document.getElementById('modalCancelarVenda').close();
      
      // Mostrar loading
      const loadingToast = mostrarToast('Cancelando venda...', 'info');
      
      // Fazer requisição para cancelar venda
      fetch('{{ route("pdv.cancelSale") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          reason: motivo
        })
      })
      .then(response => response.json())
      .then(data => {
        loadingToast.remove();
        
        if (data.success) {
          // Limpar carrinho local
          carrinho = [];
          totalDesconto = 0;
          window.descontoTipo = 'value';
          window.descontoValor = 0;
          pagamentos = [];
          atualizarCarrinho();
          
          // Limpar motivo
          document.getElementById('motivoCancelamento').value = '';
          
          mostrarToast(data.message, 'success');
          
          // Recarregar página após 2 segundos
          setTimeout(() => {
            window.location.reload();
          }, 2000);
        } else {
          mostrarToast(data.message || 'Erro ao cancelar venda', 'error');
        }
      })
      .catch(error => {
        loadingToast.remove();
        console.error('Erro:', error);
        mostrarToast('Erro ao cancelar venda', 'error');
      });
    }

    // Função para mostrar toast
    function mostrarToast(mensagem, tipo = 'info') {
      const toast = document.createElement('div');
      const cores = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
      };
      
      toast.className = `fixed top-4 right-4 ${cores[tipo]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300`;
      toast.innerHTML = `
        <div class="flex items-center space-x-2">
          <i class="fas fa-${tipo === 'success' ? 'check' : tipo === 'error' ? 'times' : tipo === 'warning' ? 'exclamation' : 'info'}-circle"></i>
          <span>${mensagem}</span>
        </div>
      `;
      
      document.body.appendChild(toast);
      
      // Auto remover após 5 segundos
      setTimeout(() => {
        toast.remove();
      }, 5000);
      
      return toast;
    }

    // Variável para controlar qual item está sendo editado
    let itemIndexEditando = -1;

    // Aplicar desconto em item específico
    function aplicarDescontoItem(index) {
      if (index < 0 || index >= carrinho.length) return;
      
      const item = carrinho[index];
      itemIndexEditando = index;
      
      // Preencher informações do modal
      document.getElementById('itemDescontoNome').textContent = item.nome;
      document.getElementById('itemDescontoInfo').textContent = 
        `${item.qtd}x R$ ${item.unitario.toFixed(2).replace('.', ',')} = R$ ${(item.unitario * item.qtd).toFixed(2).replace('.', ',')}`;
      
      // Limpar campos
      document.getElementById('valorDescontoItem').value = '';
      document.querySelector('input[name="tipoDescontoItem"][value="value"]').checked = true;
      
      // Atualizar totais
      atualizarTotaisDescontoItem();
      
      // Mostrar modal
      document.getElementById('modalDescontoItem').showModal();
    }

    // Atualizar totais do modal de desconto
    function atualizarTotaisDescontoItem() {
      if (itemIndexEditando < 0 || itemIndexEditando >= carrinho.length) return;
      
      const item = carrinho[itemIndexEditando];
      const valorInput = document.getElementById('valorDescontoItem').value;
      const tipoDesconto = document.querySelector('input[name="tipoDescontoItem"]:checked').value;
      
      const totalAtual = item.unitario * item.qtd;
      document.getElementById('totalAtualItem').textContent = `R$ ${totalAtual.toFixed(2).replace('.', ',')}`;
      
      if (valorInput && valorInput > 0) {
        let desconto = parseFloat(valorInput);
        
        if (tipoDesconto === 'percentage') {
          if (desconto > 100) {
            desconto = 100;
            document.getElementById('valorDescontoItem').value = 100;
          }
          desconto = (totalAtual * desconto) / 100;
        } else {
          if (desconto > totalAtual) {
            desconto = totalAtual;
            document.getElementById('valorDescontoItem').value = totalAtual.toFixed(2);
          }
        }
        
        const totalComDesconto = totalAtual - desconto;
        document.getElementById('totalComDescontoItem').textContent = `R$ ${totalComDesconto.toFixed(2).replace('.', ',')}`;
      } else {
        document.getElementById('totalComDescontoItem').textContent = `R$ ${totalAtual.toFixed(2).replace('.', ',')}`;
      }
    }

    // Confirmar desconto do item
    function confirmarDescontoItem() {
      if (itemIndexEditando < 0 || itemIndexEditando >= carrinho.length) return;
      
      const valorInput = document.getElementById('valorDescontoItem').value;
      const tipoDesconto = document.querySelector('input[name="tipoDescontoItem"]:checked').value;
      
      if (!valorInput || valorInput <= 0) {
        mostrarToast('Digite um valor válido para o desconto', 'warning');
        return;
      }
      
      const item = carrinho[itemIndexEditando];
      const totalAtual = item.unitario * item.qtd;
      let desconto = parseFloat(valorInput);
      
      if (tipoDesconto === 'percentage') {
        if (desconto > 100) {
          mostrarToast('Desconto não pode ser maior que 100%', 'error');
          return;
        }
        desconto = (totalAtual * desconto) / 100;
      } else {
        if (desconto > totalAtual) {
          mostrarToast('Desconto não pode ser maior que o total do item', 'error');
          return;
        }
      }
      
      // Aplicar desconto ao item
      item.desconto = desconto;
      item.total = totalAtual - desconto;
      item.tipoDesconto = tipoDesconto;
      item.valorDesconto = parseFloat(valorInput);
      
      // Fechar modal
      document.getElementById('modalDescontoItem').close();
      
      // Atualizar carrinho
      atualizarCarrinho();
      
      mostrarToast(`Desconto de R$ ${desconto.toFixed(2).replace('.', ',')} aplicado ao item`, 'success');
      
      // Limpar variável
      itemIndexEditando = -1;
    }

    // Adicionar event listeners para o modal de desconto
    document.addEventListener('DOMContentLoaded', function() {
      const valorInput = document.getElementById('valorDescontoItem');
      const radioButtons = document.querySelectorAll('input[name="tipoDescontoItem"]');
      
      if (valorInput) {
        valorInput.addEventListener('input', atualizarTotaisDescontoItem);
      }
      
      radioButtons.forEach(radio => {
        radio.addEventListener('change', atualizarTotaisDescontoItem);
      });
    });

    // Finalizar venda
    function finalizarVenda() {
      if (carrinho.length === 0) {
        alert('Adicione produtos ao carrinho!');
        return;
      }
      
      const modal = safeGetElementById('modalFinalizar');
      if (modal) {
        modal.showModal();
        atualizarPagamentos();
      } else {
        console.error('Modal de finalização não encontrado');
        alert('Erro: Modal de finalização não encontrado!');
      }
    }

    // Função para pagar o total restante
    function pagarTotal() {
      const tipoElement = document.querySelector('.forma-pagamento');
      
      if (!tipoElement || !tipoElement.value) {
        alert('Selecione primeiro a forma de pagamento!');
        tipoElement?.focus();
        return;
      }
      
      const totalVenda = carrinho.reduce((sum, item) => sum + item.total, 0) - totalDesconto;
      const totalPagamentosAtual = pagamentos.reduce((sum, pag) => sum + pag.valor, 0);
      const valorRestante = totalVenda - totalPagamentosAtual;
      
      if (valorRestante <= 0) {
        alert('O pagamento já está completo!');
        return;
      }
      
      // Adicionar o pagamento do valor restante
      const tipo = tipoElement.value;
      pagamentos.push({ tipo, valor: valorRestante });
      
      // Limpar campos
      tipoElement.selectedIndex = 0;
      
      atualizarPagamentos();
      
      console.log('Pagamento total adicionado:', { tipo, valor: valorRestante });
    }

    // Atalhos de teclado para pagamento
    document.addEventListener('keydown', function(e) {
      // Enter no campo de valor do pagamento
      if (e.target.classList.contains('valor-pagamento') && e.key === 'Enter') {
        e.preventDefault();
        const btnAdicionar = e.target.closest('.payment-form-container').querySelector('.btn-adicionar-pagamento');
        if (btnAdicionar) {
          btnAdicionar.click();
        }
      }
    });

    // Adicionar pagamento
    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('btn-adicionar-pagamento') || e.target.closest('.btn-adicionar-pagamento')) {
        // Evitar cliques múltiplos
        const button = e.target.classList.contains('btn-adicionar-pagamento') ? e.target : e.target.closest('.btn-adicionar-pagamento');
        if (button.disabled) return;
        
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin text-lg"></i><span class="ml-2">Adicionando...</span>';
        
        setTimeout(() => {
          button.disabled = false;
          button.innerHTML = '<i class="fas fa-plus text-lg"></i><span class="ml-2">Adicionar Valor</span>';
        }, 1000);
        
        console.log('Botão de adicionar pagamento clicado');
        
        // Tentar encontrar o container pai
        let row = button.closest('.payment-form-container');
        
        console.log('Row encontrado:', row);
        
        if (!row) {
          alert('Erro: não foi possível encontrar o container do pagamento!');
          return;
        }
        
        // Buscar elementos dentro do container pai
        let tipoElement = row.querySelector('.forma-pagamento');
        let valorElement = row.querySelector('.valor-pagamento');
        
        console.log('Tipo element:', tipoElement);
        console.log('Valor element:', valorElement);
        
        if (!tipoElement || !valorElement) {
          console.error('Elementos de pagamento não encontrados');
          alert('Erro ao encontrar elementos de pagamento!');
          return;
        }
        
        const tipo = tipoElement.value;
        const valor = parseFloat(valorElement.value);
        
        console.log('Tipo selecionado:', tipo);
        console.log('Valor digitado:', valor);
        
        if (!tipo || !valor || valor <= 0) {
          alert('Selecione a forma de pagamento e informe o valor!');
          return;
        }
        
        // Verificar se não excede o total da venda
        const totalVenda = carrinho.reduce((sum, item) => sum + item.total, 0) - totalDesconto;
        const totalPagamentosAtual = pagamentos.reduce((sum, pag) => sum + pag.valor, 0);
        
        if ((totalPagamentosAtual + valor) > totalVenda) {
          const falta = totalVenda - totalPagamentosAtual;
          if (falta > 0) {
            if (!confirm(`O valor informado excede o total da venda. Falta apenas R$ ${falta.toFixed(2).replace('.', ',')}. Deseja continuar?`)) {
              return;
            }
          }
        }
        
        pagamentos.push({ tipo, valor });
        
        // Limpar campos
        tipoElement.selectedIndex = 0;
        valorElement.value = '';
        
        // Focar no campo de seleção para próximo pagamento
        tipoElement.focus();
        
        atualizarPagamentos();
        
        console.log('Pagamento adicionado com sucesso:', { tipo, valor });
      }
    });

    // Atualizar lista de pagamentos
    function atualizarPagamentos() {
      const container = document.getElementById('pagamentosAdicionados');
      const resumoTroco = document.getElementById('resumoTroco');
      
      if (pagamentos.length === 0) {
        container.innerHTML = `
          <div class="text-center text-gray-500 py-4">
            <i class="fas fa-credit-card text-2xl mb-2"></i>
            <p>Nenhum pagamento adicionado</p>
          </div>
        `;
        resumoTroco.classList.add('hidden');
      } else {
        container.innerHTML = pagamentos.map((pag, index) => {
          if (pag.tipo === 'prazo') {
            // Função para formatar data corretamente (evitar problemas de fuso horário)
            const formatarDataVencimento = (dataISO) => {
              if (!dataISO) return 'Data não definida';
              const partes = dataISO.split('-');
              const ano = partes[0];
              const mes = partes[1];
              const dia = partes[2];
              return `${dia}/${mes}/${ano}`;
            };
            
            const dataVencimento = formatarDataVencimento(pag.data_vencimento);
            return `
              <div class="bg-yellow-50 border-2 border-yellow-300 p-4 rounded-lg payment-installment">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <i class="fas fa-calendar-alt text-yellow-600 text-lg mr-3"></i>
                    <div>
                      <span class="font-semibold text-gray-900">${formatarTipoPagamento(pag.tipo)}</span>
                      <div class="text-sm text-gray-600">Vencimento: ${dataVencimento}</div>
                      <div class="text-sm font-semibold text-gray-800">Valor: R$ ${pag.valor.toFixed(2).replace('.', ',')}</div>
                    </div>
                  </div>
                  <button onclick="removerPagamento(${index})" class="text-red-500 hover:text-red-700 p-1">
                    <i class="fas fa-trash text-sm"></i>
                  </button>
                </div>
              </div>
            `;
          } else {
            return `
              <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                <div>
                  <span class="font-semibold">${formatarTipoPagamento(pag.tipo)}</span>
                  <span class="text-sm text-gray-600 ml-2">R$ ${pag.valor.toFixed(2).replace('.', ',')}</span>
                </div>
                <button onclick="removerPagamento(${index})" class="text-red-500 hover:text-red-700">
                  <i class="fas fa-trash text-xs"></i>
                </button>
              </div>
            `;
          }
        }).join('');
        
        // Calcular e mostrar troco/falta
        const totalVenda = carrinho.reduce((sum, item) => sum + item.total, 0) - totalDesconto;
        const totalPagamentos = pagamentos.reduce((sum, pag) => sum + pag.valor, 0);
        const diferenca = totalPagamentos - totalVenda;
        
        if (Math.abs(diferenca) > 0.01) {
          resumoTroco.classList.remove('hidden');
          
          if (diferenca > 0) {
            document.getElementById('textoTroco').textContent = 'Troco a devolver';
            document.getElementById('valorTroco').textContent = `R$ ${diferenca.toFixed(2).replace('.', ',')}`;
            document.getElementById('valorTroco').className = 'text-lg font-bold text-green-600';
          } else {
            document.getElementById('textoTroco').textContent = 'Falta receber';
            document.getElementById('valorTroco').textContent = `R$ ${Math.abs(diferenca).toFixed(2).replace('.', ',')}`;
            document.getElementById('valorTroco').className = 'text-lg font-bold text-red-600';
          }
        } else {
          resumoTroco.classList.add('hidden');
        }
      }
    }

    // Remover pagamento
    function removerPagamento(index) {
      const pagamentoRemovido = pagamentos[index];
      pagamentos.splice(index, 1);
      
      // Se removeu um pagamento a prazo, verificar se deve mostrar formas de pagamento normais
      if (pagamentoRemovido && pagamentoRemovido.tipo === 'prazo') {
        const modoPagamentoElement = document.querySelector('input[name="modoPagamento"]:checked');
        const formasPagamento = document.querySelector('.payment-form-container');
        
        if (modoPagamentoElement && modoPagamentoElement.value === 'installment') {
          // Se ainda está em modo a prazo mas removeu o pagamento, mostrar formas normais
          if (formasPagamento) formasPagamento.style.display = 'block';
        }
      }
      
      atualizarPagamentos();
    }

    // Formatar tipo de pagamento
    function formatarTipoPagamento(tipo) {
      const tipos = {
        'dinheiro': 'Dinheiro',
        'pix': 'PIX',
        'cartao_debito': 'Cartão de Débito',
        'cartao_credito': 'Cartão de Crédito',
        'prazo': 'A Prazo'
      };
      return tipos[tipo] || tipo;
    }

    // Confirmar venda
    const btnConfirmarVenda = safeGetElementById('btnConfirmarVenda');
    if (btnConfirmarVenda) {
      btnConfirmarVenda.addEventListener('click', function() {
        if (carrinho.length === 0) {
          alert('Adicione produtos ao carrinho!');
          return;
        }
      
      if (pagamentos.length === 0) {
        alert('Adicione pelo menos uma forma de pagamento!');
        return;
      }
      
      const totalVenda = carrinho.reduce((sum, item) => sum + item.total, 0) - totalDesconto;
      const totalPagamentos = pagamentos.reduce((sum, pag) => sum + pag.valor, 0);
      const diferenca = totalPagamentos - totalVenda;

      // Validar vendedor
      const sellerId = document.getElementById('sellerSelect').value;
      if (!sellerId) {
       alert('Por favor, selecione um vendedor antes de finalizar a venda.');
       document.getElementById('sellerSelect').focus();
        return;
      }
      
      // Permitir troco ou falta
      if (diferenca > 0) {
        const trocoFormatado = `R$ ${diferenca.toFixed(2).replace('.', ',')}`;
        if (!confirm(`Há troco de ${trocoFormatado}. Deseja continuar?`)) {
          return;
        }
      } else if (diferenca < 0) {
        const faltaFormatada = `R$ ${Math.abs(diferenca).toFixed(2).replace('.', ',')}`;
        if (!confirm(`Falta ${faltaFormatada} para completar o pagamento. Deseja continuar mesmo assim?`)) {
          return;
        }
      }
      
      // Preparar dados para envio
      const modoPagamentoElement = document.querySelector('input[name="modoPagamento"]:checked');
      const dataVencimentoElement = safeGetElementById('dataVencimento');
      const observacoesPrazoElement = safeGetElementById('observacoesPrazo');
      const observacoesVendaElement = safeGetElementById('observacoesVenda');
      const customerSelectElement = safeGetElementById('customerSelect');
      
      const sellerSelectElement = safeGetElementById('sellerSelect');

      // Preparar itens com descontos individuais
      const itensComDesconto = carrinho.map(item => ({
        id: item.id,
        nome: item.nome,
        qtd: item.qtd,
        unitario: item.unitario,
        total: item.total,
        desconto: item.desconto || 0,
        tipoDesconto: item.tipoDesconto || 'value',
        valorDesconto: item.valorDesconto || 0
      }));

      const dados = {
        itens: itensComDesconto,
        pagamentos: pagamentos,
        seller_id: sellerSelectElement.value || "Sem vendedor",
        customer_id: customerSelectElement ? customerSelectElement.value : null,
        desconto: totalDesconto,
        discount_type: window.descontoTipo || 'value',
        troco: diferenca > 0 ? diferenca : 0,
        falta: diferenca < 0 ? Math.abs(diferenca) : 0,
        modo_pagamento: modoPagamentoElement ? modoPagamentoElement.value : 'cash',
        data_vencimento: dataVencimentoElement ? dataVencimentoElement.value : null,
        observacoes_prazo: observacoesVendaElement ? observacoesVendaElement.value : (observacoesPrazoElement ? observacoesPrazoElement.value : null)
      };
      
      console.log('Dados enviados:', dados);
      
      // Enviar para o servidor
      fetch('/pdv/finalizar', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(dados)
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          let mensagem = 'Venda finalizada com sucesso!';
          
          if (diferenca > 0) {
            mensagem += `\n\nTroco: R$ ${diferenca.toFixed(2).replace('.', ',')}`;
          } else if (diferenca < 0) {
            mensagem += `\n\nFalta: R$ ${Math.abs(diferenca).toFixed(2).replace('.', ',')}`;
          }
          
          // Informar sobre o romaneio gerado
          mensagem += `\n\n📋 Romaneio de entrega gerado automaticamente!`;
          
          alert(mensagem);
          
          // Criar modal para download de documentos
          const downloadModal = document.createElement('div');
          downloadModal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
          downloadModal.innerHTML = `
            <div class="bg-white rounded-xl shadow-2xl p-6 max-w-md mx-4">
              <div class="text-center mb-6">
                <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Venda Finalizada!</h2>
                <p class="text-sm text-gray-600">Baixe os documentos da venda</p>
              </div>
              
              <div class="space-y-3 mb-6">
                <button onclick="window.open('${data.cupom_url}', '_blank')" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold text-sm transition-colors flex items-center justify-center space-x-2">
                  <i class="fas fa-receipt"></i>
                  <span>📧 Baixar Cupom de Venda</span>
                </button>
                
                <button onclick="window.open('${data.romaneio_url}', '_blank')" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-semibold text-sm transition-colors flex items-center justify-center space-x-2">
                  <i class="fas fa-truck"></i>
                  <span>📋 Baixar Romaneio de Entrega</span>
                </button>
                
                <button onclick="this.closest('div').parentElement.remove()" class="w-full bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 px-4 rounded-lg font-semibold text-sm transition-colors flex items-center justify-center space-x-2">
                  <i class="fas fa-times"></i>
                  <span>Fechar</span>
                </button>
              </div>
              
              <div class="text-xs text-gray-500 text-center">
                Você pode baixar estes documentos a qualquer momento
              </div>
            </div>
          `;
          
          document.body.appendChild(downloadModal);
          
          // Limpar carrinho
          carrinho = [];
          totalDesconto = 0;
          pagamentos = [];
          atualizarCarrinho();
          
          // Fechar modal
          const modalFinalizar = safeGetElementById('modalFinalizar');
          if (modalFinalizar) modalFinalizar.close();
        } else {
          alert('Erro ao finalizar venda: ' + (data.error || 'Erro desconhecido'));
        }
      })
      .catch(error => {
        console.error('Erro completo:', error);
        alert('Erro ao finalizar venda: ' + error.message);
      });
      });
    }



    // Atualizar ID do vendedor
    function updateSellerId() {
      const sellerId = document.getElementById('sellerSelect').value;
      document.getElementById('selectedSellerId').value = sellerId;
    }

    // Atualizar o ID do vendedor quando a seleção mudar
    document.getElementById('sellerSelect').addEventListener('change', function() {
      document.getElementById('selectedSellerId').value = this.value;
    });

    // Consulta de preço
    document.getElementById('produtoConsulta').addEventListener('change', function() {
      const option = this.selectedOptions[0];
      if (option && option.value) {
        const preco = parseFloat(option.dataset.preco);
        const nome = option.text;
        
        document.getElementById('precoConsulta').textContent = `R$ ${preco.toFixed(2).replace('.', ',')}`;
        document.getElementById('nomeConsulta').textContent = nome;
        document.getElementById('resultadoConsulta').classList.remove('hidden');
      } else {
        document.getElementById('resultadoConsulta').classList.add('hidden');
      }
    });

    // Inicializar
    document.addEventListener('DOMContentLoaded', function() {
      atualizarCarrinho();
      
      // Pesquisa de produtos
      let searchTimeout;
      let selectedIndex = -1;
      const searchInput = document.getElementById('searchProduct');
      const searchResults = document.getElementById('searchResults');
      
      // Dados dos produtos para pesquisa
      const produtos = [
        @foreach($products as $product)
        {
          id: '{{ $product->id }}',
          nome: '{{ $product->name }}',
          codigo: '{{ $product->internal_code ?? '' }}',
          preco: {{ $product->sale_price }},
          precoFormatado: 'R$ {{ number_format($product->sale_price, 2, ',', '.') }}'
        },
        @endforeach
      ];
      
      if (searchInput && searchResults) {
        searchInput.addEventListener('input', function(e) {
          clearTimeout(searchTimeout);
          const termo = e.target.value.trim();
          selectedIndex = -1;
          
          if (termo.length === 0) {
            searchResults.classList.add('hidden');
            return;
          }
          
          searchTimeout = setTimeout(() => {
            pesquisarProdutos(termo);
          }, 300);
        });
      } else {
        console.error('Elementos de busca não encontrados:', { searchInput, searchResults });
      }
      
      function pesquisarProdutos(termo) {
        const termoLower = termo.toLowerCase();
        const resultados = produtos.filter(produto => 
          produto.nome.toLowerCase().includes(termoLower) ||
          produto.codigo.toLowerCase().includes(termoLower)
        ).slice(0, 8); // Limitar a 8 resultados
        
        if (resultados.length === 0) {
          searchResults.innerHTML = `
            <div class="p-4 text-gray-500 text-center">
              <i class="fas fa-search text-xl mb-2"></i>
              <p class="text-sm">Nenhum produto encontrado</p>
            </div>
          `;
        } else {
          searchResults.innerHTML = resultados.map((produto, index) => `
            <div class="search-result-item p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 flex items-center justify-between"
                 data-index="${index}"
                 onclick="adicionarProdutoFromSearch('${produto.id}', '${produto.nome}', ${produto.preco})">
              <div class="flex-1 min-w-0">
                <h4 class="font-semibold text-gray-900 text-sm truncate">${produto.nome}</h4>
                ${produto.codigo ? `<p class="text-xs text-gray-500">Código: ${produto.codigo}</p>` : ''}
              </div>
              <div class="text-right ml-3">
                <p class="font-bold text-purple-600 text-sm">${produto.precoFormatado}</p>
                <p class="text-xs text-gray-500">Clique para adicionar</p>
              </div>
            </div>
          `).join('');
        }
        
        searchResults.classList.remove('hidden');
      }
      
      window.adicionarProdutoFromSearch = function(id, nome, preco) {
        adicionarProduto(id, nome, preco);
        searchInput.value = '';
        searchResults.classList.add('hidden');
        selectedIndex = -1;
      };
      
      // Fechar dropdown quando clicar fora
      document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
          searchResults.classList.add('hidden');
        }
      });
      
      // Navegação com teclas
      searchInput.addEventListener('keydown', function(e) {
        const items = searchResults.querySelectorAll('.search-result-item');
        
        if (e.key === 'ArrowDown') {
          e.preventDefault();
          selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
          updateSelectedItem(items);
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          selectedIndex = Math.max(selectedIndex - 1, -1);
          updateSelectedItem(items);
        } else if (e.key === 'Enter') {
          e.preventDefault();
          if (selectedIndex >= 0 && items[selectedIndex]) {
            items[selectedIndex].click();
          }
        } else if (e.key === 'Escape') {
          searchResults.classList.add('hidden');
          selectedIndex = -1;
        }
      });
      
      function updateSelectedItem(items) {
        items.forEach((item, index) => {
          if (index === selectedIndex) {
            item.classList.add('bg-purple-50');
          } else {
            item.classList.remove('bg-purple-50');
          }
        });
      }
    });
  </script>
</body>
</html>
