@extends('dashboard.layout')
@section('title', 'Painel de Emissão NFe')
@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-file-invoice text-green-500"></i> Painel de Emissão NFe</h1>
            <p class="text-gray-600">Emita notas fiscais eletrônicas de forma fácil e rápida.</p>
        </div>
        <div class="text-sm text-gray-500">
            Última atualização: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <form method="POST" action="{{ route('nfe.store') }}" id="nfe-form" class="space-y-6">
        @csrf
        
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <strong>Erro!</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif
        
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Card: Dados Gerais da NFe -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-info-circle text-blue-500"></i> Dados Gerais</h2>
                
                <div class="space-y-4">
                    <!-- Novo campo: Tipo de NFe -->
                    <div>
                        <label class="block text-sm mb-1 font-medium text-blue-700">Tipo de NFe *</label>
                        <select id="tipo_nfe" class="w-full border-2 border-blue-200 rounded-lg px-3 py-2 text-sm bg-blue-50 text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-200" onchange="configurarTipoNfe()">
                            <option value="">Selecione o tipo de NFe...</option>
                            <option value="venda_consumidor">🛒 Venda para Consumidor Final</option>
                            <option value="venda_empresa">🏢 Venda para Empresa</option>
                            <option value="devolucao_venda">↩️ Devolução de Venda</option>
                            <option value="devolucao_compra">↪️ Devolução de Compra</option>
                            <option value="entrada_compra">📦 Entrada de Compra</option>
                            <option value="transferencia">🔄 Transferência entre Filiais</option>
                            <option value="remessa_conserto">🔧 Remessa para Conserto</option>
                            <option value="retorno_conserto">🔙 Retorno de Conserto</option>
                            <option value="bonificacao">🎁 Bonificação</option>
                            <option value="demonstracao">👁️ Demonstração</option>
                            <option value="brinde">🎊 Brinde</option>
                            <option value="exportacao">🌍 Exportação</option>
                            <option value="homologacao">🧪 Teste de Homologação</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Natureza da Operação *</label>
                        <select name="natureza_operacao" id="natureza_operacao" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                            <option value="">Selecione...</option>
                            <option value="Venda de produção do estabelecimento">Venda de produção do estabelecimento</option>
                            <option value="Venda de mercadoria adquirida ou recebida de terceiros">Venda de mercadoria adquirida ou recebida de terceiros</option>
                            <option value="Venda de mercadoria adquirida ou recebida de terceiros em operação com mercadoria sujeita ao regime de substituição tributária">Venda de mercadoria adquirida ou recebida de terceiros em operação com mercadoria sujeita ao regime de substituição tributária</option>
                            <option value="Remessa para industrialização por conta e ordem do adquirente da mercadoria, quando esta não transitar pelo estabelecimento do adquirente">Remessa para industrialização por conta e ordem do adquirente da mercadoria, quando esta não transitar pelo estabelecimento do adquirente</option>
                            <option value="Remessa para industrialização por conta e ordem do adquirente da mercadoria, quando esta transitar pelo estabelecimento do adquirente">Remessa para industrialização por conta e ordem do adquirente da mercadoria, quando esta transitar pelo estabelecimento do adquirente</option>
                            <option value="Remessa de produção do estabelecimento, com fim específico de exportação">Remessa de produção do estabelecimento, com fim específico de exportação</option>
                            <option value="Remessa de mercadoria adquirida ou recebida de terceiros, com fim específico de exportação">Remessa de mercadoria adquirida ou recebida de terceiros, com fim específico de exportação</option>
                            <option value="Outros">Outros</option>
                            <option value="Venda">Venda</option>
                            <option value="Remessa">Remessa</option>
                            <option value="Devolução">Devolução</option>
                            <option value="Devolução de venda">Devolução de venda</option>
                            <option value="Devolução de compra">Devolução de compra</option>
                            <option value="Transferência">Transferência</option>
                            <option value="Bonificação">Bonificação</option>
                            <option value="Demonstração">Demonstração</option>
                            <option value="Brinde">Brinde</option>
                            <option value="Conserto">Conserto</option>
                            <option value="Garantia">Garantia</option>
                            <option value="Exportação">Exportação</option>
                            <option value="Venda para teste de homologacao">Venda para teste de homologacao</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">Tipo Documento *</label>
                            <select name="tipo_documento" id="tipo_documento" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                                <option value="1">1 - Saída</option>
                                <option value="0">0 - Entrada</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Finalidade *</label>
                            <select name="finalidade_emissao" id="finalidade_emissao" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                                <option value="1">1 - Normal</option>
                                <option value="2">2 - Complementar</option>
                                <option value="3">3 - Ajuste</option>
                                <option value="4">4 - Devolução</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Presença do Comprador *</label>
                        <select name="presenca_comprador" id="presenca_comprador" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                            <option value="1">1 - Presencial</option>
                            <option value="2">2 - Internet</option>
                            <option value="3">3 - Teleatendimento</option>
                            <option value="4">4 - NFCe Entrega Domicílio</option>
                            <option value="5">5 - Presencial fora do estabelecimento</option>
                            <option value="9">9 - Operação não presencial</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">Consumidor Final *</label>
                            <select name="consumidor_final" id="consumidor_final" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                                <option value="1">1 - Sim</option>
                                <option value="0">0 - Não</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Modalidade do Frete *</label>
                            <select name="modalidade_frete" id="modalidade_frete" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                                <option value="0">0 - Por conta do emitente</option>
                                <option value="1">1 - Por conta do destinatário</option>
                                <option value="2">2 - Por conta de terceiros</option>
                                <option value="9">9 - Sem frete</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">Data de Emissão *</label>
                            <input type="date" name="data_emissao" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Data Entrada/Saída</label>
                            <input type="date" name="data_entrada_saida" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Dados do Destinatário -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-user text-purple-500"></i> Destinatário</h2>
                
                <div class="space-y-4">
                    <!-- Busca de Cliente Existente -->
                    <div>
                        <label class="block text-sm mb-1 font-medium text-indigo-700">🔍 Buscar Cliente Cadastrado</label>
                        <div class="flex gap-2">
                            <select id="cliente_existente" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" style="border-color: #6366f1;" onchange="preencherDadosCliente()">
                                <option value="">Selecione um cliente cadastrado...</option>
                                <!-- Os clientes serão carregados via JavaScript -->
                            </select>
                            <button type="button" onclick="limparCamposCliente()" class="px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm transition-colors">
                                <i class="fas fa-eraser"></i>
                            </button>
                        </div>
                        <small class="text-indigo-600">Selecione um cliente para preencher automaticamente todos os dados</small>
                    </div>

                    <div class="border-t pt-4">
                        <p class="text-sm text-gray-600 mb-3">📝 <strong>Ou preencha os dados manualmente:</strong></p>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Nome/Razão Social *</label>
                        <input type="text" name="nome_destinatario" id="nome_destinatario" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                    </div>

                    <!-- Seletor de tipo de pessoa -->
                    <div>
                        <label class="block text-sm mb-1 font-medium text-purple-700">Tipo de Pessoa *</label>
                        <select id="tipo_pessoa" class="w-full border-2 border-purple-200 rounded-lg px-3 py-2 text-sm bg-purple-50 text-gray-900 focus:border-purple-500 focus:ring-2 focus:ring-purple-200" onchange="alternarDocumento(this.value)">
                            <option value="">Selecione o tipo...</option>
                            <option value="fisica">👤 Pessoa Física (CPF)</option>
                            <option value="juridica">🏢 Pessoa Jurídica (CNPJ)</option>
                        </select>
                    </div>

                    <!-- Campos de documento -->
                    <div id="documento-container">
                        <div id="campo-cpf" class="hidden">
                            <label class="block text-sm mb-1 font-medium text-green-700">CPF *</label>
                            <input type="text" name="cpf_destinatario" id="cpf_input" class="w-full border-2 border-green-200 rounded-lg px-3 py-2 text-sm bg-green-50 text-gray-900 focus:border-green-500" placeholder="000.000.000-00" maxlength="14">
                        </div>
                        <div id="campo-cnpj" class="hidden">
                            <label class="block text-sm mb-1 font-medium text-blue-700">CNPJ *</label>
                            <input type="text" name="cnpj_destinatario" id="cnpj_input" class="w-full border-2 border-blue-200 rounded-lg px-3 py-2 text-sm bg-blue-50 text-gray-900 focus:border-blue-500" placeholder="00.000.000/0000-00" maxlength="18">
                        </div>
                        <div id="campo-padrao" class="border-2 border-yellow-200 rounded-lg p-3 bg-yellow-50">
                            <p class="text-sm text-yellow-800 flex items-center gap-2">
                                <i class="fas fa-info-circle"></i>
                                Selecione o tipo de pessoa acima para informar CPF ou CNPJ
                            </p>
                        </div>
                    </div>

                    <!-- Campos de IE - só aparecem para CNPJ -->
                    <div id="campos-ie" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">Inscrição Estadual</label>
                            <input type="text" name="ie_destinatario" id="ie_input" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900">
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Indicador IE *</label>
                            <select name="indicador_ie_destinatario" id="indicador_ie_select" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                                <option value="1">1 - Contribuinte ICMS</option>
                                <option value="2">2 - Contribuinte isento</option>
                                <option value="9">9 - Não contribuinte</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Telefone</label>
                        <input type="text" name="telefone_destinatario" id="telefone_destinatario" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">CEP *</label>
                            <input type="text" name="cep_destinatario" id="cep_destinatario" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" maxlength="9" required>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">UF *</label>
                            <select name="uf_destinatario" id="uf_destinatario" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
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
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">Município *</label>
                            <input type="text" name="municipio_destinatario" id="municipio_destinatario" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Bairro *</label>
                            <input type="text" name="bairro_destinatario" id="bairro_destinatario" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">Logradouro *</label>
                            <input type="text" name="logradouro_destinatario" id="logradouro_destinatario" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Número *</label>
                            <input type="text" name="numero_destinatario" id="numero_destinatario" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Complemento</label>
                        <input type="text" name="complemento_destinatario" id="complemento_destinatario" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900">
                    </div>
                </div>
            </div>

            <!-- Card: Produtos/Itens -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-box text-orange-500"></i> Produtos/Itens</h2>
                
                <div id="itens-container" class="space-y-4">
                    <div class="item-row space-y-4" data-index="0">
                        <div>
                            <label class="block text-sm mb-1">Produto *</label>
                            <select name="itens[0][produto_id]" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" onchange="preencherProduto(0)">
                                <option value="">Selecione um produto ou digite manual abaixo...</option>
                                @if(isset($produtos))
                                    @foreach($produtos as $produto)
                                        <option value="{{ $produto->id }}" 
                                                data-name="{{ $produto->name }}" 
                                                data-sale_price="{{ $produto->sale_price }}" 
                                                data-codigo="{{ $produto->codigo }}"
                                                data-internal_code="{{ $produto->internal_code }}"
                                                data-ncm="{{ $produto->ncm }}"
                                                data-unit="{{ $produto->unit }}">
                                            {{ $produto->codigo ?: $produto->internal_code }} - {{ $produto->name }} (R$ {{ number_format($produto->sale_price, 2, ',', '.') }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Código do Produto *</label>
                            <input type="text" name="itens[0][codigo_produto]" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                            <small class="text-gray-500">Se não selecionou um produto acima, digite o código manual</small>
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Descrição *</label>
                            <input type="text" name="itens[0][descricao]" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                            <small class="text-gray-500">Se não selecionou um produto acima, digite a descrição manual</small>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm mb-1">NCM *</label>
                                <input type="text" name="itens[0][codigo_ncm]" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" maxlength="8" required>
                                <small class="text-gray-500">Se produto não tem NCM cadastrado, digite manual (8 dígitos)</small>
                            </div>
                            <div>
                                <label class="block text-sm mb-1">CFOP</label>
                                <select name="itens[0][cfop]" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900">
                                    <option value="">Selecione...</option>
                                    <option value="5102">5102 - Venda de mercadoria adquirida ou recebida de terceiros</option>
                                    <option value="5403">5403 - Venda de mercadoria adquirida ou recebida de terceiros, sujeita ao regime de substituição tributária</option>
                                    <option value="5101">5101 - Venda de produção do estabelecimento</option>
                                    <option value="5401">5401 - Venda de produção do estabelecimento em operação com produto sujeito ao regime de substituição tributária</option>
                                    <option value="6102">6102 - Venda de mercadoria adquirida ou recebida de terceiros (Interestadual)</option>
                                    <option value="6108">6108 - Venda de mercadoria adquirida ou recebida de terceiros, sujeita ao regime de substituição tributária (Interestadual)</option>
                                    <option value="6101">6101 - Venda de produção do estabelecimento (Interestadual)</option>
                                    <option value="6107">6107 - Venda de produção do estabelecimento, sujeita ao regime de substituição tributária (Interestadual)</option>
                                    <option value="5949">5949 - Outra saída de mercadoria ou prestação de serviço não especificado</option>
                                    <option value="6949">6949 - Outra saída de mercadoria ou prestação de serviço não especificado (Interestadual)</option>
                                </select>
                                <small class="text-gray-500">CFOP será definido automaticamente baseado na UF se não informado</small>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm mb-1">Unidade *</label>
                                <input type="text" name="itens[0][unidade_comercial]" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                            </div>
                            <div>
                                <label class="block text-sm mb-1">Quantidade *</label>
                                <input type="number" step="0.01" name="itens[0][quantidade_comercial]" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm mb-1">Valor Unitário *</label>
                                <input type="number" step="0.01" name="itens[0][valor_unitario_comercial]" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                            </div>
                            <div>
                                <label class="block text-sm mb-1">Valor Total</label>
                                <input type="number" step="0.01" name="itens[0][valor_bruto_produtos]" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" readonly>
                            </div>
                        </div>

                        <!-- Impostos -->
                        <div class="border-t pt-4 mt-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Impostos</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm mb-1">ICMS Origem *</label>
                                    <select name="itens[0][icms_origem]" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                                        <option value="0">0 - Nacional</option>
                                        <option value="1">1 - Estrangeira (importação direta)</option>
                                        <option value="2">2 - Estrangeira (adquirida no mercado interno)</option>
                                        <option value="3">3 - Nacional (> 40% importação)</option>
                                        <option value="4">4 - Nacional (processos produtivos básicos)</option>
                                        <option value="5">5 - Nacional (<= 40% importação)</option>
                                        <option value="6">6 - Estrangeira (sem similar nacional)</option>
                                        <option value="7">7 - Estrangeira (mercado interno, sem similar)</option>
                                        <option value="8">8 - Nacional (> 70% importação)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm mb-1">ICMS CST *</label>
                                    <select name="itens[0][icms_situacao_tributaria]" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                                        <option value="00">00 - Tributada integralmente</option>
                                        <option value="10">10 - Tributada e com cobrança do ICMS por ST</option>
                                        <option value="20">20 - Com redução de base de cálculo</option>
                                        <option value="30">30 - Isenta ou não tributada e com cobrança do ICMS por ST</option>
                                        <option value="40" selected>40 - Isenta</option>
                                        <option value="41">41 - Não tributada</option>
                                        <option value="50">50 - Suspensão</option>
                                        <option value="51">51 - Diferimento</option>
                                        <option value="60">60 - ICMS cobrado anteriormente por ST</option>
                                        <option value="70">70 - Com redução de base de cálculo e cobrança do ICMS por ST</option>
                                        <option value="90">90 - Outras</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm mb-1">ICMS Alíquota (%)</label>
                                    <input type="number" step="0.01" name="itens[0][icms_aliquota]" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                                <div>
                                    <label class="block text-sm mb-1">PIS CST *</label>
                                    <select name="itens[0][pis_situacao_tributaria]" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                                        <option value="01">01 - Operação Tributável (base de cálculo = valor da operação alíquota normal)</option>
                                        <option value="07">07 - Operação Isenta da Contribuição</option>
                                        <option value="08">08 - Operação Sem Incidência da Contribuição</option>
                                        <option value="09">09 - Operação com Suspensão da Contribuição</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm mb-1">PIS Alíquota (%)</label>
                                    <input type="number" step="0.01" name="itens[0][pis_aliquota]" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" value="0.65">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                                <div>
                                    <label class="block text-sm mb-1">COFINS CST *</label>
                                    <select name="itens[0][cofins_situacao_tributaria]" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                                        <option value="01">01 - Operação Tributável (base de cálculo = valor da operação alíquota normal)</option>
                                        <option value="07">07 - Operação Isenta da Contribuição</option>
                                        <option value="08">08 - Operação Sem Incidência da Contribuição</option>
                                        <option value="09">09 - Operação com Suspensão da Contribuição</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm mb-1">COFINS Alíquota (%)</label>
                                    <input type="number" step="0.01" name="itens[0][cofins_aliquota]" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" value="3.00">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="button" id="adicionar-item" class="px-3 py-1 rounded bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium transition">
                        <i class="fas fa-plus"></i> Adicionar Item
                    </button>
                </div>
            </div>
        </div>

        <!-- Card de Ações -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2"><i class="fas fa-cog text-gray-500"></i> Ações</h2>
                    <p class="text-sm text-gray-600">Escolha como deseja processar a NFe</p>
                </div>
                <div class="flex gap-3">
                    <button type="submit" name="action" value="save" class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                        <i class="fas fa-save"></i> Salvar Rascunho
                    </button>
                    <button type="submit" name="action" value="emit" class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white font-medium transition">
                        <i class="fas fa-paper-plane"></i> Emitir NFe
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Card: Teste Rápido -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-flask text-yellow-500"></i> Teste Rápido</h2>
        <p class="text-sm text-gray-600 mb-4">Crie uma NFe de teste para homologação com dados pré-preenchidos</p>
        <button type="button" id="criar-teste" class="px-4 py-2 rounded bg-yellow-600 hover:bg-yellow-700 text-white font-medium transition">
            <i class="fas fa-vial"></i> Criar NFe de Teste
        </button>
    </div>
</div>

<script>
// Função para alternar entre tipos de documento (CPF/CNPJ) - DEVE ESTAR FORA DO DOMContentLoaded
function alternarDocumento(tipo) {
    console.log('Função alternarDocumento chamada com tipo:', tipo);
    
    const campoCpf = document.getElementById('campo-cpf');
    const campoCnpj = document.getElementById('campo-cnpj');
    const campoPadrao = document.getElementById('campo-padrao');
    const camposIe = document.getElementById('campos-ie');
    const cpfInput = document.getElementById('cpf_input');
    const cnpjInput = document.getElementById('cnpj_input');
    const ieInput = document.getElementById('ie_input');
    const indicadorIeSelect = document.getElementById('indicador_ie_select');
    
    console.log('Elementos encontrados:', {
        campoCpf: campoCpf ? 'sim' : 'não',
        campoCnpj: campoCnpj ? 'sim' : 'não',
        campoPadrao: campoPadrao ? 'sim' : 'não',
        camposIe: camposIe ? 'sim' : 'não',
        cpfInput: cpfInput ? 'sim' : 'não',
        cnpjInput: cnpjInput ? 'sim' : 'não'
    });
    
    // CORRIGIR O BUG: Esconder TODOS os campos primeiro e limpar valores
    if (campoCpf) {
        campoCpf.classList.add('hidden');
        campoCpf.style.display = 'none';
    }
    if (campoCnpj) {
        campoCnpj.classList.add('hidden');
        campoCnpj.style.display = 'none';
    }
    if (campoPadrao) {
        campoPadrao.classList.add('hidden');
        campoPadrao.style.display = 'none';
    }
    if (camposIe) camposIe.style.display = 'none';
    
    // Limpar requirements e valores
    if (cpfInput) {
        cpfInput.required = false;
        cpfInput.value = '';
    }
    if (cnpjInput) {
        cnpjInput.required = false;
        cnpjInput.value = '';
    }
    if (ieInput) ieInput.value = '';
    
    if (tipo === 'fisica') {
        console.log('Mostrando campo CPF - escondendo campos IE');
        // Pessoa física - mostrar CPF, esconder IE
        if (campoCpf) {
            campoCpf.classList.remove('hidden');
            campoCpf.style.display = 'block';
        }
        if (cpfInput) cpfInput.required = true;
        
        // Para CPF: esconder campos IE e definir como não contribuinte
        if (camposIe) camposIe.style.display = 'none';
        if (indicadorIeSelect) indicadorIeSelect.value = '9'; // Não contribuinte
        
    } else if (tipo === 'juridica') {
        console.log('Mostrando campo CNPJ - mostrando campos IE');
        // Pessoa jurídica - mostrar CNPJ e IE
        if (campoCnpj) {
            campoCnpj.classList.remove('hidden');
            campoCnpj.style.display = 'block';
        }
        if (camposIe) camposIe.style.display = 'grid'; // Mostrar campos IE
        if (cnpjInput) cnpjInput.required = true;
        if (indicadorIeSelect) indicadorIeSelect.value = '1'; // Contribuinte ICMS por padrão
        
    } else {
        console.log('Mostrando campo padrão - escondendo campos IE');
        // Nenhuma opção selecionada - mostrar mensagem padrão
        if (campoPadrao) {
            campoPadrao.classList.remove('hidden');
            campoPadrao.style.display = 'block';
        }
        if (camposIe) camposIe.style.display = 'none';
        if (indicadorIeSelect) indicadorIeSelect.value = '9';
    }
}

// Função para configurar automaticamente os campos baseado no tipo de NFe
function configurarTipoNfe() {
    const tipoNfe = document.getElementById('tipo_nfe').value;
    const naturezaOperacao = document.getElementById('natureza_operacao');
    const tipoDocumento = document.getElementById('tipo_documento');
    const finalidadeEmissao = document.getElementById('finalidade_emissao');
    const presencaComprador = document.getElementById('presenca_comprador');
    const consumidorFinal = document.getElementById('consumidor_final');
    const modalidadeFrete = document.getElementById('modalidade_frete');

    // Configurações por tipo de NFe
    const configuracoes = {
        'venda_consumidor': {
            natureza: 'Venda de mercadoria adquirida ou recebida de terceiros',
            tipo_documento: '1', // Saída
            finalidade: '1', // Normal
            presenca: '1', // Presencial
            consumidor_final: '1', // Sim
            frete: '9' // Sem frete
        },
        'venda_empresa': {
            natureza: 'Venda de mercadoria adquirida ou recebida de terceiros',
            tipo_documento: '1', // Saída
            finalidade: '1', // Normal
            presenca: '9', // Não presencial
            consumidor_final: '0', // Não
            frete: '0' // Por conta do emitente
        },
        'devolucao_venda': {
            natureza: 'Devolução de venda',
            tipo_documento: '0', // Entrada
            finalidade: '4', // Devolução
            presenca: '1', // Presencial
            consumidor_final: '1', // Sim
            frete: '9' // Sem frete
        },
        'devolucao_compra': {
            natureza: 'Devolução de compra',
            tipo_documento: '1', // Saída
            finalidade: '4', // Devolução
            presenca: '9', // Não presencial
            consumidor_final: '0', // Não
            frete: '1' // Por conta do destinatário
        },
        'entrada_compra': {
            natureza: 'Compra para comercialização',
            tipo_documento: '0', // Entrada
            finalidade: '1', // Normal
            presenca: '9', // Não presencial
            consumidor_final: '0', // Não
            frete: '0' // Por conta do emitente
        },
        'transferencia': {
            natureza: 'Transferência',
            tipo_documento: '1', // Saída
            finalidade: '1', // Normal
            presenca: '9', // Não presencial
            consumidor_final: '0', // Não
            frete: '0' // Por conta do emitente
        },
        'remessa_conserto': {
            natureza: 'Remessa para conserto',
            tipo_documento: '1', // Saída
            finalidade: '1', // Normal
            presenca: '9', // Não presencial
            consumidor_final: '0', // Não
            frete: '0' // Por conta do emitente
        },
        'retorno_conserto': {
            natureza: 'Retorno de conserto',
            tipo_documento: '0', // Entrada
            finalidade: '1', // Normal
            presenca: '9', // Não presencial
            consumidor_final: '0', // Não
            frete: '1' // Por conta do destinatário
        },
        'bonificacao': {
            natureza: 'Bonificação',
            tipo_documento: '1', // Saída
            finalidade: '1', // Normal
            presenca: '1', // Presencial
            consumidor_final: '1', // Sim
            frete: '9' // Sem frete
        },
        'demonstracao': {
            natureza: 'Demonstração',
            tipo_documento: '1', // Saída
            finalidade: '1', // Normal
            presenca: '1', // Presencial
            consumidor_final: '1', // Sim
            frete: '9' // Sem frete
        },
        'brinde': {
            natureza: 'Brinde',
            tipo_documento: '1', // Saída
            finalidade: '1', // Normal
            presenca: '1', // Presencial
            consumidor_final: '1', // Sim
            frete: '9' // Sem frete
        },
        'exportacao': {
            natureza: 'Exportação',
            tipo_documento: '1', // Saída
            finalidade: '1', // Normal
            presenca: '9', // Não presencial
            consumidor_final: '0', // Não
            frete: '0' // Por conta do emitente
        },
        'homologacao': {
            natureza: 'Venda para teste de homologacao',
            tipo_documento: '1', // Saída
            finalidade: '1', // Normal
            presenca: '1', // Presencial
            consumidor_final: '1', // Sim
            frete: '9' // Sem frete
        }
    };

    if (tipoNfe && configuracoes[tipoNfe]) {
        const config = configuracoes[tipoNfe];
        
        // Aplicar configurações
        naturezaOperacao.value = config.natureza;
        tipoDocumento.value = config.tipo_documento;
        finalidadeEmissao.value = config.finalidade;
        presencaComprador.value = config.presenca;
        consumidorFinal.value = config.consumidor_final;
        modalidadeFrete.value = config.frete;

        // Destacar visualmente que foi configurado automaticamente
        [naturezaOperacao, tipoDocumento, finalidadeEmissao, presencaComprador, consumidorFinal, modalidadeFrete].forEach(field => {
            field.style.backgroundColor = '#e8f5e8';
            field.style.borderColor = '#4caf50';
            setTimeout(() => {
                field.style.backgroundColor = '';
                field.style.borderColor = '';
            }, 2000);
        });
    }
}

// Função para preencher dados do produto automaticamente
function preencherProduto(index) {
    const select = document.querySelector(`select[name="itens[${index}][produto_id]"]`);
    const option = select.options[select.selectedIndex];
    
    if (option.value) {
        // Preencher campos com dados do produto
        document.querySelector(`input[name="itens[${index}][codigo_produto]"]`).value = option.dataset.codigo || option.dataset.internal_code || '';
        document.querySelector(`input[name="itens[${index}][descricao]"]`).value = option.dataset.name || option.text;
        document.querySelector(`input[name="itens[${index}][codigo_ncm]"]`).value = option.dataset.ncm || '49111090';
        document.querySelector(`input[name="itens[${index}][valor_unitario_comercial]"]`).value = option.dataset.sale_price || option.dataset.price || '';
        document.querySelector(`input[name="itens[${index}][unidade_comercial]"]`).value = option.dataset.unit || 'UN';
        
        // Se tem CFOP cadastrado, selecionar
        if (option.dataset.cfop) {
            document.querySelector(`select[name="itens[${index}][cfop]"]`).value = option.dataset.cfop;
        } else {
            // Sugerir CFOP baseado na UF do destinatário
            suggestCfopBasedOnUf(index);
        }
        
        // Calcular total se quantidade já estiver preenchida
        calcularValorTotal(index);
    }
}

// Cálculo automático do valor total do item
document.addEventListener('DOMContentLoaded', function() {
    function calcularValorTotal(index) {
        const quantidade = document.querySelector(`input[name="itens[${index}][quantidade_comercial]"]`);
        const valorUnitario = document.querySelector(`input[name="itens[${index}][valor_unitario_comercial]"]`);
        const valorTotal = document.querySelector(`input[name="itens[${index}][valor_bruto_produtos]"]`);
        
        if (quantidade && valorUnitario && valorTotal) {
            const total = parseFloat(quantidade.value || 0) * parseFloat(valorUnitario.value || 0);
            valorTotal.value = total.toFixed(2);
        }
    }

    // Adicionar eventos aos campos existentes
    document.querySelectorAll('input[name*="quantidade_comercial"], input[name*="valor_unitario_comercial"]').forEach(input => {
        input.addEventListener('input', function() {
            const match = this.name.match(/\[(\d+)\]/);
            if (match) {
                calcularValorTotal(parseInt(match[1]));
            }
        });
    });

    // Adicionar novo item
    let itemIndex = 1;
    document.getElementById('adicionar-item').addEventListener('click', function() {
        const container = document.getElementById('itens-container');
        const newItem = document.querySelector('.item-row').cloneNode(true);
        
        // Atualizar indices dos campos
        newItem.querySelectorAll('input, select').forEach(field => {
            const name = field.name.replace(/\[\d+\]/, `[${itemIndex}]`);
            field.name = name;
            if (field.type !== 'select-one') {
                field.value = '';
            } else {
                field.selectedIndex = 0;
            }
        });
        
        // Atualizar onchange do select de produtos
        const selectProduto = newItem.querySelector('select[name*="produto_id"]');
        if (selectProduto) {
            selectProduto.setAttribute('onchange', `preencherProduto(${itemIndex})`);
        }
        
        // Adicionar botão de remover
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'px-2 py-1 rounded bg-red-600 hover:bg-red-700 text-white text-xs font-medium transition mt-2';
        removeBtn.innerHTML = '<i class="fas fa-trash"></i> Remover';
        removeBtn.addEventListener('click', function() {
            newItem.remove();
        });
        
        newItem.appendChild(removeBtn);
        container.appendChild(newItem);
        
        // Adicionar eventos de cálculo
        newItem.querySelectorAll('input[name*="quantidade_comercial"], input[name*="valor_unitario_comercial"]').forEach(input => {
            input.addEventListener('input', function() {
                calcularValorTotal(itemIndex);
            });
        });
        
        // Sugerir CFOP para o novo item baseado na UF atual
        setTimeout(() => {
            suggestCfopBasedOnUf(itemIndex);
        }, 100);
        
        itemIndex++;
    });

    // Teste rápido
    document.getElementById('criar-teste').addEventListener('click', function() {
        fetch('/nfe/criar-teste', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.sucesso) {
                alert('NFe de teste criada com sucesso! ID: ' + data.nfe_id);
                if (data.chave) {
                    alert('Chave da NFe: ' + data.chave);
                }
                console.log('Dados enviados para Focus NFe:', data.dados_enviados);
            } else {
                alert('Erro: ' + data.mensagem);
                if (data.dados_enviados) {
                    console.log('Dados enviados para Focus NFe:', data.dados_enviados);
                }
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao criar NFe de teste');
        });
    });

    // Carregar clientes ao inicializar a página
    carregarClientes();
});

// Função para carregar clientes cadastrados
function carregarClientes() {
    fetch('/api/customers')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('cliente_existente');
            if (select && data) {
                // Limpar opções existentes (manter apenas a primeira)
                select.innerHTML = '<option value="">Selecione um cliente cadastrado...</option>';
                
                data.forEach(cliente => {
                    const option = document.createElement('option');
                    option.value = cliente.id;
                    
                    // Detectar se é CPF ou CNPJ baseado no tamanho OU no tipo cadastrado
                    const documento = cliente.cpf_cnpj ? cliente.cpf_cnpj.replace(/\D/g, '') : '';
                    let tipoDoc = '';
                    
                    if (documento.length === 11 || cliente.type === 'pessoa_fisica') {
                        tipoDoc = ' (CPF)';
                    } else if (documento.length === 14 || cliente.type === 'pessoa_juridica') {
                        tipoDoc = ' (CNPJ)';
                    } else if (cliente.type === 'pessoa_fisica') {
                        tipoDoc = ' (PF)';
                    } else if (cliente.type === 'pessoa_juridica') {
                        tipoDoc = ' (PJ)';
                    }
                    
                    option.text = `${cliente.name}${tipoDoc} - ${cliente.cpf_cnpj || 'Sem documento'}`;
                    option.setAttribute('data-cliente', JSON.stringify(cliente));
                    select.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Erro ao carregar clientes:', error);
        });
}

// Função para preencher dados do cliente selecionado
function preencherDadosCliente() {
    const select = document.getElementById('cliente_existente');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value === '') {
        return;
    }
    
    try {
        const cliente = JSON.parse(selectedOption.getAttribute('data-cliente'));
        console.log('Cliente selecionado:', cliente);
        
        // Preencher nome
        const nomeField = document.getElementById('nome_destinatario');
        if (nomeField && cliente.name) {
            nomeField.value = cliente.name;
        }
        
        // Detectar e preencher CPF ou CNPJ
        if (cliente.cpf_cnpj) {
            const documento = cliente.cpf_cnpj.replace(/\D/g, '');
            const tipoPessoa = document.getElementById('tipo_pessoa');
            
            console.log('Documento encontrado:', cliente.cpf_cnpj, 'Limpo:', documento, 'Length:', documento.length);
            
            // Usar o tipo do cliente como fallback se o documento não estiver bem formatado
            const tipoCliente = cliente.type; // 'pessoa_fisica' ou 'pessoa_juridica'
            
            if (documento.length === 11 || tipoCliente === 'pessoa_fisica') {
                // É CPF - Pessoa Física
                console.log('Detectado CPF');
                tipoPessoa.value = 'fisica';
                alternarDocumento('fisica');
                
                // Aguardar um pouco para o campo aparecer
                setTimeout(() => {
                    const cpfInput = document.getElementById('cpf_input');
                    if (cpfInput) {
                        cpfInput.value = cliente.cpf_cnpj;
                        console.log('CPF preenchido:', cliente.cpf_cnpj);
                    } else {
                        console.error('Campo CPF não encontrado');
                    }
                }, 100);
                
            } else if (documento.length === 14 || tipoCliente === 'pessoa_juridica') {
                // É CNPJ - Pessoa Jurídica  
                console.log('Detectado CNPJ');
                tipoPessoa.value = 'juridica';
                alternarDocumento('juridica');
                
                // Aguardar um pouco para o campo aparecer
                setTimeout(() => {
                    const cnpjInput = document.getElementById('cnpj_input');
                    if (cnpjInput) {
                        cnpjInput.value = cliente.cpf_cnpj;
                        console.log('CNPJ preenchido:', cliente.cpf_cnpj);
                    } else {
                        console.error('Campo CNPJ não encontrado');
                    }
                }, 100);
            } else {
                console.warn('Documento com tamanho inválido:', documento.length, 'Tipo cliente:', tipoCliente);
                // Como fallback, usar o tipo cadastrado do cliente
                if (tipoCliente === 'pessoa_fisica') {
                    tipoPessoa.value = 'fisica';
                    alternarDocumento('fisica');
                    setTimeout(() => {
                        const cpfInput = document.getElementById('cpf_input');
                        if (cpfInput) cpfInput.value = cliente.cpf_cnpj;
                    }, 100);
                } else if (tipoCliente === 'pessoa_juridica') {
                    tipoPessoa.value = 'juridica';
                    alternarDocumento('juridica');
                    setTimeout(() => {
                        const cnpjInput = document.getElementById('cnpj_input');
                        if (cnpjInput) cnpjInput.value = cliente.cpf_cnpj;
                    }, 100);
                }
            }
        } else {
            console.warn('Cliente sem CPF/CNPJ');
        }
        
        // Preencher telefone
        const telefoneField = document.getElementById('telefone_destinatario');
        if (telefoneField && cliente.phone) {
            telefoneField.value = cliente.phone;
        }
        
        // Preencher endereço
        const cepField = document.getElementById('cep_destinatario');
        if (cepField && cliente.postal_code) {
            cepField.value = cliente.postal_code;
        }
        
        const ufField = document.getElementById('uf_destinatario');
        if (ufField && cliente.state) {
            ufField.value = cliente.state;
            
            // Atualizar CFOPs baseados na nova UF após um pequeno delay
            setTimeout(() => {
                const itemsContainer = document.getElementById('items-container');
                const items = itemsContainer.querySelectorAll('.item-row');
                
                items.forEach((item, index) => {
                    const cfopSelect = item.querySelector(`select[name="itens[${index}][cfop]"]`);
                    const produtoSelect = item.querySelector(`select[name="itens[${index}][codigo_produto]"]`);
                    
                    // Só atualizar se não tem produto com CFOP específico ou se está vazio
                    if (cfopSelect && (!produtoSelect?.value || !produtoSelect?.selectedOptions[0]?.dataset.cfop || cfopSelect.value === '')) {
                        suggestCfopBasedOnUf(index);
                    }
                });
            }, 200);
        }
        
        const municipioField = document.getElementById('municipio_destinatario');
        if (municipioField && cliente.city) {
            municipioField.value = cliente.city;
        }
        
        const logradouroField = document.getElementById('logradouro_destinatario');
        if (logradouroField && cliente.address) {
            // Tentar extrair número do endereço se ele vier junto
            const endereco = cliente.address;
            const numeroField = document.getElementById('numero_destinatario');
            
            // Regex para extrair número do endereço (ex: "Rua ABC, 123" -> logradouro="Rua ABC", numero="123")
            const match = endereco.match(/^(.+?),?\s*(\d+).*$/);
            if (match && numeroField) {
                logradouroField.value = match[1].trim();
                if (!numeroField.value) { // Só preenche se não houver número já
                    numeroField.value = match[2];
                }
            } else {
                logradouroField.value = endereco;
            }
        }
        
        // Para campos que podem não existir no modelo Customer, vamos tentar preencher com valores padrão
        const bairroField = document.getElementById('bairro_destinatario');
        if (bairroField) {
            bairroField.value = cliente.neighborhood || cliente.bairro || 'Centro';
        }
        
        const numeroField = document.getElementById('numero_destinatario');
        if (numeroField && !numeroField.value) { // Só preenche se não foi preenchido acima
            numeroField.value = cliente.number || cliente.numero || 'S/N';
        }
        
        const complementoField = document.getElementById('complemento_destinatario');
        if (complementoField) {
            complementoField.value = cliente.complement || cliente.complemento || '';
        }
        
        // Mostrar notificação de sucesso
        mostrarNotificacao('✅ Dados do cliente preenchidos automaticamente!', 'success');
        
    } catch (error) {
        console.error('Erro ao processar dados do cliente:', error);
        mostrarNotificacao('❌ Erro ao carregar dados do cliente', 'error');
    }
}

// Função para limpar campos do cliente
function limparCamposCliente() {
    // Limpar seleção
    document.getElementById('cliente_existente').value = '';
    
    // Limpar campos de dados
    document.getElementById('nome_destinatario').value = '';
    document.getElementById('telefone_destinatario').value = '';
    document.getElementById('cep_destinatario').value = '';
    document.getElementById('uf_destinatario').value = '';
    document.getElementById('municipio_destinatario').value = '';
    document.getElementById('bairro_destinatario').value = '';
    document.getElementById('logradouro_destinatario').value = '';
    document.getElementById('numero_destinatario').value = '';
    document.getElementById('complemento_destinatario').value = '';
    
    // Reset tipo de pessoa
    document.getElementById('tipo_pessoa').value = '';
    alternarDocumento('');
    
    mostrarNotificacao('🧹 Campos do cliente limpos', 'info');
}

// Função para mostrar notificações
function mostrarNotificacao(mensagem, tipo = 'info') {
    // Criar elemento de notificação
    const notificacao = document.createElement('div');
    notificacao.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white font-medium z-50 transition-all duration-300 ${
        tipo === 'success' ? 'bg-green-500' : 
        tipo === 'error' ? 'bg-red-500' : 
        tipo === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
    }`;
    notificacao.textContent = mensagem;
    
    // Adicionar ao corpo da página
    document.body.appendChild(notificacao);
    
    // Remover após 3 segundos
    setTimeout(() => {
        notificacao.style.opacity = '0';
        notificacao.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notificacao);
        }, 300);
    }, 3000);
}

// Função para sugerir CFOP baseado na UF do destinatário
function suggestCfopBasedOnUf(index) {
    const ufDestinatario = document.querySelector('select[name="uf_destinatario"]')?.value;
    const ufEmitente = 'PA'; // UF do emitente (Pará)
    
    let cfopSugerido = '5102'; // Default: venda interna
    
    if (ufDestinatario && ufDestinatario !== ufEmitente) {
        // Operação interestadual - CFOP 6xxx
        cfopSugerido = '6102'; // Venda para outro estado
    }
    
    // Aplicar CFOP sugerido
    const cfopSelect = document.querySelector(`select[name="itens[${index}][cfop]"]`);
    if (cfopSelect) {
        cfopSelect.value = cfopSugerido;
        
        // Destacar o campo brevemente para mostrar que foi alterado
        cfopSelect.style.backgroundColor = '#fef3c7';
        setTimeout(() => {
            cfopSelect.style.backgroundColor = '';
        }, 1000);
    }
}

// Função para atualizar todos os CFOPs quando a UF do destinatário mudar
function updateAllCfopsOnUfChange() {
    const ufSelect = document.querySelector('select[name="uf_destinatario"]');
    if (ufSelect) {
        ufSelect.addEventListener('change', function() {
            // Atualizar todos os itens existentes
            const itemsContainer = document.getElementById('items-container');
            const items = itemsContainer.querySelectorAll('.item-row');
            
            items.forEach((item, index) => {
                const cfopSelect = item.querySelector(`select[name="itens[${index}][cfop]"]`);
                const produtoSelect = item.querySelector(`select[name="itens[${index}][codigo_produto]"]`);
                
                // Só atualizar se não tem produto selecionado com CFOP específico
                if (cfopSelect && (!produtoSelect?.value || !produtoSelect?.selectedOptions[0]?.dataset.cfop)) {
                    suggestCfopBasedOnUf(index);
                }
            });
        });
    }
}

// Inicializar quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    updateAllCfopsOnUfChange();
    
    // Sugerir CFOP inicial para o primeiro item
    setTimeout(() => {
        suggestCfopBasedOnUf(0);
    }, 100);
});
</script>
@endsection
