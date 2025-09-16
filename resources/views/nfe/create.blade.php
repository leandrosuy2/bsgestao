@extends('dashboard.layout')

@section('title', 'Criar Nota Fiscal')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Criar Nova Nota Fiscal</h1>
            <a href="{{ route('nfe.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                Voltar
            </a>
        </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <strong>Erro!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('nfe.store') }}" class="space-y-6">
        @csrf
        
        <!-- Dados do Destinatário -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Dados do Destinatário</h2>
            
            <!-- Busca de Cliente -->
            <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h3 class="text-lg font-medium text-blue-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    🔍 Buscar Cliente Cadastrado
                </h3>
                <select id="cliente-select" onchange="preencherCliente(this.value)" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-2">
                    <option value="">Selecione um cliente cadastrado...</option>
                    @foreach(\App\Models\Customer::where('company_id', auth()->user()->company_id)->where('active', true)->get() as $customer)
                        <option value="{{ $customer->id }}" 
                                data-nome="{{ $customer->name }}"
                                data-documento="{{ $customer->cpf_cnpj }}"
                                data-email="{{ $customer->email }}"
                                data-telefone="{{ $customer->phone }}"
                                data-cep="{{ $customer->postal_code }}"
                                data-uf="{{ $customer->state }}"
                                data-cidade="{{ $customer->city }}"
                                data-bairro="{{ $customer->neighborhood }}"
                                data-endereco="{{ $customer->address }}"
                                data-numero="{{ $customer->number }}">
                            {{ $customer->name }} - {{ $customer->formatted_cpf_cnpj }}
                        </option>
                    @endforeach
                </select>
                <p class="text-sm text-blue-600">Selecione um cliente para preencher automaticamente todos os dados</p>
            </div>
            
            <!-- Dados Manuais -->
            <div class="border-t pt-4">
                <h3 class="text-lg font-medium text-gray-700 mb-3">📝 Ou preencha os dados manualmente:</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CNPJ/CPF *</label>
                        <input type="text" id="destinatario_cnpj_cpf" name="destinatario_cnpj_cpf" value="{{ old('destinatario_cnpj_cpf') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="00.000.000/0000-00 ou 000.000.000-00" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome/Razão Social *</label>
                        <input type="text" id="destinatario_nome" name="destinatario_nome" value="{{ old('destinatario_nome') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Nome ou razão social" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                        <input type="email" id="destinatario_email" name="destinatario_email" value="{{ old('destinatario_email') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="email@exemplo.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                        <input type="text" id="destinatario_telefone" name="destinatario_telefone" value="{{ old('destinatario_telefone') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="(11) 99999-9999">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CEP *</label>
                        <input type="text" id="cep_destinatario" name="cep_destinatario" value="{{ old('cep_destinatario') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="00000-000" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">UF *</label>
                        <select id="uf_destinatario" name="uf_destinatario" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Selecione...</option>
                            <option value="AC">AC - Acre</option>
                            <option value="AL">AL - Alagoas</option>
                            <option value="AP">AP - Amapá</option>
                            <option value="AM">AM - Amazonas</option>
                            <option value="BA">BA - Bahia</option>
                            <option value="CE">CE - Ceará</option>
                            <option value="DF">DF - Distrito Federal</option>
                            <option value="ES">ES - Espírito Santo</option>
                            <option value="GO">GO - Goiás</option>
                            <option value="MA">MA - Maranhão</option>
                            <option value="MT">MT - Mato Grosso</option>
                            <option value="MS">MS - Mato Grosso do Sul</option>
                            <option value="MG">MG - Minas Gerais</option>
                            <option value="PA">PA - Pará</option>
                            <option value="PB">PB - Paraíba</option>
                            <option value="PR">PR - Paraná</option>
                            <option value="PE">PE - Pernambuco</option>
                            <option value="PI">PI - Piauí</option>
                            <option value="RJ">RJ - Rio de Janeiro</option>
                            <option value="RN">RN - Rio Grande do Norte</option>
                            <option value="RS">RS - Rio Grande do Sul</option>
                            <option value="RO">RO - Rondônia</option>
                            <option value="RR">RR - Roraima</option>
                            <option value="SC">SC - Santa Catarina</option>
                            <option value="SP">SP - São Paulo</option>
                            <option value="SE">SE - Sergipe</option>
                            <option value="TO">TO - Tocantins</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Município *</label>
                        <input type="text" id="municipio_destinatario" name="municipio_destinatario" value="{{ old('municipio_destinatario') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Cidade" required>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bairro *</label>
                        <input type="text" id="bairro_destinatario" name="bairro_destinatario" value="{{ old('bairro_destinatario') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Bairro" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logradouro *</label>
                        <input type="text" id="logradouro_destinatario" name="logradouro_destinatario" value="{{ old('logradouro_destinatario') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Rua, Avenida, etc" required maxlength="60" >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número *</label>
                        <input type="text" id="numero_destinatario" name="numero_destinatario" value="{{ old('numero_destinatario') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="123" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dados da Nota -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Dados da Nota</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de NFe *</label>
                    <select name="tipo_documento" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="1" selected>1 - Saída</option>
                        <option value="0">0 - Entrada</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Finalidade *</label>
                    <select name="finalidade_emissao" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="1" selected>1 - Normal</option>
                        <option value="2">2 - Complementar</option>
                        <option value="3">3 - Ajuste</option>
                        <option value="4">4 - Devolução</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Consumidor Final *</label>
                    <select name="consumidor_final" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="1" selected>1 - Sim</option>
                        <option value="0">0 - Não</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Natureza da Operação *</label>
                    <input type="text" name="natureza_operacao" value="{{ old('natureza_operacao', 'Venda de mercadoria adquirida ou recebida de terceiros') }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Série *</label>
                    <input type="number" name="serie" value="{{ old('serie', '1') }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data de Emissão *</label>
                    <input type="date" name="data_emissao" value="{{ old('data_emissao', date('Y-m-d')) }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
            </div>
        </div>

        <!-- Itens da Nota -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Itens da Nota</h2>
                <button type="button" onclick="adicionarItem()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Adicionar Item
                </button>
            </div>
            <div id="itens-container">
                <!-- Os itens serão adicionados aqui dinamicamente -->
            </div>
        </div>

        <!-- Duplicatas -->
        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-500 p-3 rounded-lg text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">💰 Duplicatas / Parcelas</h2>
                        <p class="text-gray-600 text-sm">Configure as condições de pagamento da NFe (opcional)</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="adicionarDuplicata()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2 transition-colors shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Adicionar Parcela
                    </button>
                    <button type="button" onclick="gerarParcelasAutomaticas()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2 transition-colors shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Gerar Automático
                    </button>
                </div>
            </div>
            
            <div id="duplicatas-container" class="space-y-4">
                <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                    <div class="text-gray-400 mb-3">
                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Nenhuma duplicata adicionada</h3>
                    <p class="text-sm text-gray-500 mb-4">Configure o pagamento em parcelas para esta NFe</p>
                    <div class="flex justify-center gap-2">
                        <button type="button" onclick="adicionarDuplicata()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                            Adicionar Primeira Parcela
                        </button>
                        <button type="button" onclick="gerarParcelasAutomaticas()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                            Gerar Automaticamente
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Observações -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Observações</h2>
            <textarea name="observacoes" rows="3" 
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Observações adicionais">{{ old('observacoes') }}</textarea>
        </div>



        <!-- Botões de Ação -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('nfe.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                Cancelar
            </a>
            <button type="submit" name="action" value="save" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                Salvar Rascunho
            </button>
            <button type="submit" name="action" value="emit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                Salvar e Emitir
            </button>
        </div>
    </form>
</div>

<!-- Modal para Gerar Parcelas Automaticamente -->
<div id="modalParcelas" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden" style="z-index: 99999;">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 relative">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full mb-4">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
            
            <h3 class="text-xl font-semibold text-center text-gray-900 mb-4">🚀 NOVO MODAL - Gerar Parcelas Automaticamente</h3>
            <p class="text-center text-gray-600 mb-6">Configure o parcelamento para esta NFe de forma automática</p>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">💰 Valor Total da NFe (R$)</label>
                    <input type="number" id="valorTotalModal" step="0.01" min="0.01" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 bg-gray-50"
                           placeholder="Será preenchido automaticamente" readonly>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">📅 Número de Parcelas</label>
                    <select id="numeroParcelas" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="1">1x - À vista</option>
                        <option value="2" selected>2x - Duas parcelas</option>
                        <option value="3">3x - Três parcelas</option>
                        <option value="4">4x - Quatro parcelas</option>
                        <option value="5">5x - Cinco parcelas</option>
                        <option value="6">6x - Seis parcelas</option>
                        <option value="7">7x - Sete parcelas</option>
                        <option value="8">8x - Oito parcelas</option>
                        <option value="9">9x - Nove parcelas</option>
                        <option value="10">10x - Dez parcelas</option>
                        <option value="12">12x - Doze parcelas</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">📆 Intervalo entre Parcelas</label>
                    <select id="intervaloVencimento" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="30" selected>30 dias</option>
                        <option value="15">15 dias</option>
                        <option value="7">7 dias</option>
                        <option value="1">1 dia</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">🗓️ Data do Primeiro Vencimento</label>
                    <input type="date" id="primeiroVencimento" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button id="confirmarParcelas" type="button" 
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                    ✅ Gerar Parcelas
                </button>
                <button id="cancelarParcelas" type="button" 
                        class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                    ❌ Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
/* CACHE BUSTER: 2024-12-30-15:15:45 - CÁLCULO CORRIGIDO E FUNDO MODAL SUAVE */
let itemIndex = 0;
let duplicataIndex = 0;

// Função para preencher dados do cliente selecionado
function preencherCliente(clienteId) {
    if (!clienteId) {
        // Limpar campos se nenhum cliente selecionado
        document.getElementById('destinatario_nome').value = '';
        document.getElementById('destinatario_cnpj_cpf').value = '';
        document.getElementById('destinatario_email').value = '';
        document.getElementById('destinatario_telefone').value = '';
        document.getElementById('cep_destinatario').value = '';
        document.getElementById('uf_destinatario').value = '';
        document.getElementById('municipio_destinatario').value = '';
        document.getElementById('bairro_destinatario').value = '';
        document.getElementById('logradouro_destinatario').value = '';
        document.getElementById('numero_destinatario').value = '';
        return;
    }

    const select = document.getElementById('cliente-select');
    const option = select.options[select.selectedIndex];
    
    // Preencher campos com dados do cliente
    document.getElementById('destinatario_nome').value = option.getAttribute('data-nome') || '';
    document.getElementById('destinatario_cnpj_cpf').value = option.getAttribute('data-documento') || '';
    document.getElementById('destinatario_email').value = option.getAttribute('data-email') || '';
    document.getElementById('destinatario_telefone').value = option.getAttribute('data-telefone') || '';
    document.getElementById('cep_destinatario').value = option.getAttribute('data-cep') || '';
    document.getElementById('uf_destinatario').value = option.getAttribute('data-uf') || '';
    document.getElementById('municipio_destinatario').value = option.getAttribute('data-cidade') || '';
    document.getElementById('bairro_destinatario').value = option.getAttribute('data-bairro') || '';
    document.getElementById('logradouro_destinatario').value = option.getAttribute('data-endereco') || '';
    document.getElementById('numero_destinatario').value = option.getAttribute('data-numero') || '';
    
    // Atualizar CFOP de todos os itens baseado na UF
    atualizarCfopTodosItens();
}

// Função para atualizar CFOP de todos os itens baseado na UF do destinatário
function atualizarCfopTodosItens() {
    const ufDestinatario = document.getElementById('uf_destinatario').value;
    const ufEmitente = 'PA'; // Pará
    
    const itensCfop = document.querySelectorAll('select[name*="[cfop]"]');
    itensCfop.forEach(selectCfop => {
        if (ufDestinatario && ufDestinatario === ufEmitente) {
            // Mesma UF - CFOP 5xxx
            selectCfop.innerHTML = '<option value="5102" selected>5102 - Venda dentro do estado (PA)</option>';
        } else if (ufDestinatario && ufDestinatario !== ufEmitente) {
            // UF diferente - CFOP 6xxx
            selectCfop.innerHTML = '<option value="6102" selected>6102 - Venda fora do estado (interestadual)</option>';
        } else {
            // Padrão
            selectCfop.innerHTML = '<option value="5102" selected>5102 - Venda dentro do estado (PA)</option>';
        }
    });
}

// Função para abrir modal de geração de parcelas
function gerarParcelasAutomaticas() {
    console.log('🎯 INICIANDO gerarParcelasAutomaticas');
    
    // Primeiro, forçar atualização dos cálculos de todos os itens
    console.log('🔄 Atualizando cálculos dos itens antes de abrir o modal...');
    const itensContainer = document.getElementById('itens-container');
    const itens = itensContainer.querySelectorAll('.item-row');
    
    itens.forEach((item, index) => {
        const qtdInput = item.querySelector('input[name*="[quantidade]"]');
        const valorUnitInput = item.querySelector('input[name*="[valor_unitario]"]');
        const valorTotalInput = item.querySelector('input[name*="[valor_total]"]');
        
        if (qtdInput && valorUnitInput && valorTotalInput) {
            const quantidade = parseFloat(qtdInput.value) || 0;
            const valorUnitario = parseFloat(valorUnitInput.value) || 0;
            const valorTotal = quantidade * valorUnitario;
            valorTotalInput.value = valorTotal.toFixed(2);
            console.log(`📝 Item ${index + 1} atualizado: ${quantidade} x R$ ${valorUnitario} = R$ ${valorTotal}`);
        }
    });
    
    const modal = document.getElementById('modalParcelas');
    
    if (!modal) {
        alert('❌ ERRO: Modal não encontrado!\nVerifique se o JavaScript carregou corretamente.');
        return;
    }
    
    // Calcular valor total após atualizar os itens
    const valorTotal = calcularValorTotalNfe();
    
    // Verificar se há valor para parcelar
    if (valorTotal <= 0) {
        alert('⚠️ Aviso: Não há valor para parcelar!\n\nPor favor, adicione itens com quantidade e valor unitário antes de gerar as parcelas.');
        return;
    }
    
    // Preencher campos
    document.getElementById('valorTotalModal').value = valorTotal.toFixed(2);
    
    // Data padrão
    const hoje = new Date();
    hoje.setDate(hoje.getDate() + 30);
    document.getElementById('primeiroVencimento').value = hoje.toISOString().split('T')[0];
    
    // Mostrar modal - método direto
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    modal.style.zIndex = '99999';
    
    console.log('✅ Modal deveria estar visível agora!');
    
    // Verificação final
    setTimeout(() => {
        if (modal.style.display === 'flex') {
            console.log('✅ Modal confirmado como visível');
        } else {
            console.error('❌ Modal ainda não está visível');
            alert('Modal não apareceu. Verifique o console para logs de debug.');
        }
    }, 100);
}

// Função para calcular valor total da NFe
function calcularValorTotalNfe() {
    let total = 0;
    
    console.log('🧮 Iniciando cálculo do valor total...');
    
    // Primeiro, verificar se há valores totais já calculados nos itens
    const itensValorTotal = document.querySelectorAll('input[name*="[valor_total]"]');
    console.log('Campos de valor_total encontrados:', itensValorTotal.length);
    
    itensValorTotal.forEach((input, index) => {
        const valor = parseFloat(input.value) || 0;
        if (valor > 0) {
            console.log(`✅ Item ${index + 1} - Valor total: R$ ${valor}`);
            total += valor;
        }
    });
    
    // Se não há valores totais calculados, calcular a partir de quantidade x valor unitário
    if (total === 0) {
        console.log('⚠️ Nenhum valor total encontrado, calculando a partir de quantidade x valor unitário...');
        
        const quantidades = document.querySelectorAll('input[name*="[quantidade]"]');
        const valoresUnitarios = document.querySelectorAll('input[name*="[valor_unitario]"]');
        
        console.log('Quantidades encontradas:', quantidades.length);
        console.log('Valores unitários encontrados:', valoresUnitarios.length);
        
        quantidades.forEach((qtdInput, index) => {
            const quantidade = parseFloat(qtdInput.value) || 0;
            const valorUnitario = parseFloat(valoresUnitarios[index]?.value) || 0;
            const valorItem = quantidade * valorUnitario;
            
            if (valorItem > 0) {
                console.log(`✅ Item ${index + 1}: ${quantidade} x R$ ${valorUnitario} = R$ ${valorItem}`);
                total += valorItem;
            } else {
                console.log(`⚠️ Item ${index + 1}: valores zerados ou inválidos`);
            }
        });
    }
    
    console.log('💰 Valor total final calculado: R$', total);
    return total;
}

// Eventos do modal
document.addEventListener('DOMContentLoaded', function() {
    // Confirmar geração de parcelas
    document.getElementById('confirmarParcelas').addEventListener('click', function() {
        const valorTotal = parseFloat(document.getElementById('valorTotalModal').value) || 0;
        const numeroParcelas = parseInt(document.getElementById('numeroParcelas').value) || 1;
        const intervalo = parseInt(document.getElementById('intervaloVencimento').value) || 30;
        const primeiroVencimento = document.getElementById('primeiroVencimento').value;
        
        if (valorTotal <= 0) {
            alert('❌ Valor total deve ser maior que zero!');
            return;
        }
        
        if (!primeiroVencimento) {
            alert('❌ Data do primeiro vencimento é obrigatória!');
            return;
        }
        
        // Limpar parcelas existentes
        document.getElementById('duplicatas-container').innerHTML = '';
        
        // Gerar novas parcelas
        const valorParcela = valorTotal / numeroParcelas;
        
        for (let i = 0; i < numeroParcelas; i++) {
            const dataVencimento = new Date(primeiroVencimento);
            dataVencimento.setDate(dataVencimento.getDate() + (i * intervalo));
            
            adicionarDuplicataComDados(
                (i + 1).toString().padStart(3, '0'),
                dataVencimento.toISOString().split('T')[0],
                valorParcela.toFixed(2)
            );
        }
        
        // Fechar modal
        const modal = document.getElementById('modalParcelas');
        modal.classList.add('hidden');
        modal.style.display = 'none';
        console.log('✅ Modal fechado após gerar parcelas');
        
        // Mostrar sucesso
        alert(`✅ ${numeroParcelas} parcela(s) gerada(s) com sucesso!`);
    });
    
    // Cancelar modal
    document.getElementById('cancelarParcelas').addEventListener('click', function() {
        const modal = document.getElementById('modalParcelas');
        modal.classList.add('hidden');
        modal.style.display = 'none';
        console.log('🚫 Modal fechado pelo botão cancelar');
    });
    
    // Fechar modal clicando fora
    document.getElementById('modalParcelas').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
            this.style.display = 'none';
            console.log('🚫 Modal fechado clicando fora');
        }
    });
    
    // Listener para mudança de UF do destinatário
    document.getElementById('uf_destinatario').addEventListener('change', atualizarCfopTodosItens);
});

// Função para adicionar duplicata com dados específicos
function adicionarDuplicataComDados(numero, dataVencimento, valor) {
    const container = document.getElementById('duplicatas-container');
    const novaDuplicata = document.createElement('div');
    novaDuplicata.className = 'border border-gray-200 rounded-lg p-4 duplicata-row bg-blue-50';
    novaDuplicata.innerHTML = `
        <div class="flex justify-between items-center mb-3">
            <h4 class="font-medium text-blue-700">💰 Parcela ${numero}</h4>
            <button type="button" onclick="removerDuplicata(this)" class="text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-blue-700 mb-1">Número da Duplicata *</label>
                <input type="text" name="duplicatas[${duplicataIndex}][numero]" value="${numero}" 
                       class="w-full border border-blue-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-blue-700 mb-1">Data de Vencimento *</label>
                <input type="date" name="duplicatas[${duplicataIndex}][data_vencimento]" value="${dataVencimento}"
                       class="w-full border border-blue-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-blue-700 mb-1">Valor (R$) *</label>
                <input type="number" name="duplicatas[${duplicataIndex}][valor]" value="${valor}" step="0.01" min="0.01" 
                       class="w-full border border-blue-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
        </div>
    `;
    
    container.appendChild(novaDuplicata);
    duplicataIndex++;
}

// Dados dos produtos para busca
const produtos = [
    @foreach(\App\Models\Product::where('company_id', auth()->user()->company_id)->get() as $product)
    {
        id: '{{ $product->id }}',
        nome: '{{ $product->name }}',
        codigo: '{{ $product->internal_code ?? '' }}',
        ncm: '{{ $product->ncm ?? '' }}',
        unidade: '{{ $product->unit }}',
        preco: {{ $product->sale_price }},
        preco_custo: {{ $product->cost_price }}
    },
    @endforeach
];

function adicionarItem() {
    const container = document.getElementById('itens-container');
    const itemDiv = document.createElement('div');
    itemDiv.className = 'item-row border border-gray-200 rounded-lg p-4 mb-4';
    itemDiv.setAttribute('data-index', itemIndex);
    
    itemDiv.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Item ${itemIndex + 1}</h3>
            <button type="button" onclick="removerItem(${itemIndex})" class="text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar Produto</label>
                <select onchange="preencherProduto(${itemIndex}, this.value)" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Selecione um produto ou digite manualmente</option>
                    ${produtos.map(p => `<option value="${p.id}">${p.codigo} - ${p.nome}</option>`).join('')}
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                <input type="text" name="itens[${itemIndex}][codigo]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Código">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição *</label>
                <input type="text" name="itens[${itemIndex}][descricao]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Descrição do produto/serviço" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NCM *</label>
                <input type="text" name="itens[${itemIndex}][ncm]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Se produto não tem NCM cadastrado, digite manual (8 dígitos)" required>
                <small class="text-gray-500">Se produto não tem NCM cadastrado, digite manual (8 dígitos)</small>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CFOP *</label>
                <select name="itens[${itemIndex}][cfop]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="5102" selected>5102 - Venda de mercadoria adquirida ou recebida de terceiros</option>
                </select>
                <small class="text-gray-500">CFOP será definido automaticamente baseado na UF se não informado</small>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Unidade *</label>
                <select name="itens[${itemIndex}][unidade]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="UN">UN - Unidade</option>
                    <option value="PC">PC - Peça</option>
                    <option value="KG">KG - Quilograma</option>
                    <option value="MT">MT - Metro</option>
                    <option value="M2">M2 - Metro Quadrado</option>
                    <option value="M3">M3 - Metro Cúbico</option>
                    <option value="LT">LT - Litro</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade *</label>
                <input type="number" name="itens[${itemIndex}][quantidade]" step="0.01" min="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="calcularTotalItem(${itemIndex})" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Valor Unitário *</label>
                <input type="number" name="itens[${itemIndex}][valor_unitario]" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="calcularTotalItem(${itemIndex})" required>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Valor Total</label>
                <input type="number" name="itens[${itemIndex}][valor_total]" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100" readonly>
            </div>
        </div>
        
        <!-- ICMS -->
        <div class="border-t pt-4">
            <h4 class="font-medium text-blue-900 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                ICMS (Imposto sobre Circulação de Mercadorias)
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 bg-blue-50 p-4 rounded-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ICMS Origem *</label>
                    <select name="itens[${itemIndex}][icms_origem]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="0" selected>0 - Nacional</option>
                        <option value="1">1 - Estrangeira - Importação direta</option>
                        <option value="2">2 - Estrangeira - Adquirida no mercado interno</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ICMS CST *</label>
                    <select name="itens[${itemIndex}][icms_cst]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="40" selected>40 - Isenta</option>
                        <option value="00">00 - Tributada integralmente</option>
                        <option value="10">10 - Tributada e com cobrança do ICMS por substituição tributária</option>
                        <option value="20">20 - Com redução de base de cálculo</option>
                        <option value="30">30 - Isenta ou não tributada e com cobrança do ICMS por substituição tributária</option>
                        <option value="41">41 - Não tributada</option>
                        <option value="50">50 - Suspensão</option>
                        <option value="51">51 - Diferimento</option>
                        <option value="60">60 - ICMS cobrado anteriormente por substituição tributária</option>
                        <option value="70">70 - Com redução de base de cálculo e cobrança do ICMS por substituição tributária</option>
                        <option value="90">90 - Outras</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ICMS Alíquota (%)</label>
                    <input type="number" name="itens[${itemIndex}][icms_aliquota]" step="0.01" min="0" max="100" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0,00">
                </div>
            </div>
        </div>
        
        
    `;
    
    container.appendChild(itemDiv);
    itemIndex++;
    
    // Atualizar CFOP baseado na UF do destinatário
    setTimeout(() => {
        atualizarCfopTodosItens();
    }, 100);
}

function preencherProduto(index, produtoId) {
    if (!produtoId) return;
    
    const produto = produtos.find(p => p.id == produtoId);
    if (!produto) return;
    
    const itemDiv = document.querySelector(`[data-index="${index}"]`);
    
    // Preencher campos
    itemDiv.querySelector(`input[name="itens[${index}][codigo]"]`).value = produto.codigo;
    itemDiv.querySelector(`input[name="itens[${index}][descricao]"]`).value = produto.nome;
    itemDiv.querySelector(`input[name="itens[${index}][ncm]"]`).value = produto.ncm || '';
    itemDiv.querySelector(`input[name="itens[${index}][valor_unitario]"]`).value = produto.preco;
    
    // Definir unidade
    const unidadeSelect = itemDiv.querySelector(`select[name="itens[${index}][unidade]"]`);
    for (let option of unidadeSelect.options) {
        if (option.value === produto.unidade.toUpperCase()) {
            option.selected = true;
            break;
        }
    }
}

function removerItem(index) {
    const itemDiv = document.querySelector(`[data-index="${index}"]`);
    if (itemDiv) {
        itemDiv.remove();
    }
}

function calcularTotalItem(index) {
    const itemDiv = document.querySelector(`[data-index="${index}"]`);
    const quantidade = parseFloat(itemDiv.querySelector(`input[name="itens[${index}][quantidade]"]`).value) || 0;
    const valorUnitario = parseFloat(itemDiv.querySelector(`input[name="itens[${index}][valor_unitario]"]`).value) || 0;
    const valorTotal = quantidade * valorUnitario;
    
    itemDiv.querySelector(`input[name="itens[${index}][valor_total]"]`).value = valorTotal.toFixed(2);
}

function adicionarDuplicata() {
    const container = document.getElementById('duplicatas-container');
    
    // Remover mensagem de "nenhuma duplicata" se existir
    const emptyMessage = container.querySelector('.border-dashed');
    if (emptyMessage) {
        emptyMessage.remove();
    }
    
    const duplicataDiv = document.createElement('div');
    duplicataDiv.className = 'duplicata-row bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm';
    duplicataDiv.setAttribute('data-index', duplicataIndex);
    
    duplicataDiv.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-800 flex items-center gap-2">
                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">${duplicataIndex + 1}</span>
                Parcela ${duplicataIndex + 1}
            </h3>
            <button type="button" onclick="removerDuplicata(this)" class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número da Duplicata *</label>
                <input type="text" name="duplicatas[${duplicataIndex}][numero]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="001" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Data de Vencimento *</label>
                <input type="date" name="duplicatas[${duplicataIndex}][data_vencimento]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Valor *</label>
                <input type="number" name="duplicatas[${duplicataIndex}][valor]" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0,00" required>
            </div>
        </div>
    `;
    
    container.appendChild(duplicataDiv);
    duplicataIndex++;
}

function removerDuplicata(element) {
    // Se element é um número (índice), encontrar o elemento pelo data-index
    if (typeof element === 'number') {
        const duplicataDiv = document.querySelector(`.duplicata-row[data-index="${element}"]`);
        if (duplicataDiv) {
            duplicataDiv.remove();
        }
    } else {
        // Se element é o botão, usar closest
        element.closest('.duplicata-row').remove();
    }
    
    // Verificar se ainda há duplicatas
    const container = document.getElementById('duplicatas-container');
    const duplicatasRestantes = container.querySelectorAll('.duplicata-row');
    
    if (duplicatasRestantes.length === 0) {
        // Recolocar mensagem de "nenhuma duplicata"
        container.innerHTML = `
            <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                <div class="text-gray-400 mb-3">
                    <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Nenhuma duplicata adicionada</h3>
                <p class="text-sm text-gray-500 mb-4">Configure o pagamento em parcelas para esta NFe</p>
                <div class="flex justify-center gap-2">
                    <button type="button" onclick="adicionarDuplicata()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        Adicionar Primeira Parcela
                    </button>
                    <button type="button" onclick="gerarParcelasAutomaticas()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                        Gerar Automaticamente
                    </button>
                </div>
            </div>
        `;
    }
}

// Adicionar primeiro item automaticamente ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    adicionarItem();
    
    // Teste do modal - forçar funcionamento
    console.log('🔧 Testando modal...');
    const modal = document.getElementById('modalParcelas');
    if (modal) {
        console.log('✅ Modal encontrado no DOM');
    } else {
        console.error('❌ Modal NÃO encontrado!');
    }
    
    // Adicionar evento de teste
    window.testarModal = function() {
        console.log('🧪 Teste manual do modal');
        const modal = document.getElementById('modalParcelas');
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.remove('hidden');
            console.log('✅ Modal exibido via teste manual');
        } else {
            console.error('❌ Modal não encontrado no teste manual');
        }
    };
    
    window.verificarFuncoes = function() {
        console.log('🔍 Verificação de funções:');
        console.log('- gerarParcelasAutomaticas:', typeof window.gerarParcelasAutomaticas);
        console.log('- testarModal:', typeof window.testarModal);
        console.log('- fecharModal:', typeof window.fecharModal);
        
        const modal = document.getElementById('modalParcelas');
        console.log('- Modal no DOM:', modal ? '✅ Encontrado' : '❌ Não encontrado');
        
        if (modal) {
            console.log('- Classes do modal:', modal.className);
            console.log('- Style display:', modal.style.display);
        }
    };
    
    console.log('🎯 Para testar o modal manualmente, digite: testarModal() no console');
    console.log('🔍 Para verificar funções, digite: verificarFuncoes() no console');
});
</script>
@endsection
