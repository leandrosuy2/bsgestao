@extends('dashboard.layout')

@section('title', 'Editar Nota Fiscal')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Editar Nota Fiscal</h1>
        <div class="flex space-x-2">
            <a href="{{ route('nfe.show', $nfe) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                Cancelar
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('nfe.update', $nfe) }}" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Dados do Destinatário -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Dados do Destinatário</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CNPJ/CPF *</label>
                    <input type="text" name="destinatario_cnpj_cpf" value="{{ old('destinatario_cnpj_cpf', $nfe->destinatario_cnpj_cpf) }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('destinatario_cnpj_cpf') border-red-500 @enderror"
                           placeholder="00.000.000/0000-00 ou 000.000.000-00" required>
                    @error('destinatario_cnpj_cpf')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome/Razão Social *</label>
                    <input type="text" name="destinatario_nome" value="{{ old('destinatario_nome', $nfe->destinatario_nome) }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('destinatario_nome') border-red-500 @enderror"
                           placeholder="Nome ou razão social" required>
                    @error('destinatario_nome')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" name="destinatario_email" value="{{ old('destinatario_email', $nfe->destinatario_email) }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('destinatario_email') border-red-500 @enderror"
                           placeholder="email@exemplo.com">
                    @error('destinatario_email')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <!-- Outros campos do destinatário -->
            </div>
        </div>

        <!-- Dados da Nota -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Dados da Nota</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Natureza da Operação *</label>
                    <input type="text" name="natureza_operacao" value="{{ old('natureza_operacao', $nfe->natureza_operacao) }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('natureza_operacao') border-red-500 @enderror"
                           placeholder="Venda" required>
                    @error('natureza_operacao')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Série *</label>
                    <input type="number" name="serie" value="{{ old('serie', $nfe->serie) }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('serie') border-red-500 @enderror"
                           placeholder="1" required>
                    @error('serie')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data de Emissão *</label>
                    <input type="date" name="data_emissao" value="{{ old('data_emissao', $nfe->data_emissao?->format('Y-m-d')) }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('data_emissao') border-red-500 @enderror"
                           required>
                    @error('data_emissao')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Itens existentes -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Itens da Nota</h2>
            <div id="itens-container">
                @foreach($nfe->items as $index => $item)
                    <div class="item-row border border-gray-200 rounded-lg p-4 mb-4" data-index="{{ $index }}">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Item {{ $index + 1 }}</h3>
                            <button type="button" onclick="removerItem({{ $index }})" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição *</label>
                                <input type="text" name="itens[{{ $index }}][descricao]" value="{{ $item->descricao }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Descrição do produto/serviço" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                                <input type="text" name="itens[{{ $index }}][codigo]" value="{{ $item->codigo }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Código">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NCM *</label>
                                <input type="text" name="itens[{{ $index }}][ncm]" value="{{ $item->ncm }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="00000000" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">CFOP *</label>
                                <input type="text" name="itens[{{ $index }}][cfop]" value="{{ $item->cfop }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="5102" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Unidade *</label>
                                <select name="itens[{{ $index }}][unidade]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="UN" {{ $item->unidade == 'UN' ? 'selected' : '' }}>Unidade</option>
                                    <option value="PC" {{ $item->unidade == 'PC' ? 'selected' : '' }}>Peça</option>
                                    <option value="KG" {{ $item->unidade == 'KG' ? 'selected' : '' }}>Quilograma</option>
                                    <option value="MT" {{ $item->unidade == 'MT' ? 'selected' : '' }}>Metro</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade *</label>
                                <input type="number" name="itens[{{ $index }}][quantidade]" value="{{ $item->quantidade }}" step="0.01" min="0.01"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       onchange="calcularTotais()" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Valor Unitário *</label>
                                <input type="number" name="itens[{{ $index }}][valor_unitario]" value="{{ $item->valor_unitario }}" step="0.01" min="0"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       onchange="calcularTotais()" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Valor Total</label>
                                <input type="number" name="itens[{{ $index }}][valor_total]" value="{{ $item->valor_total }}" step="0.01" min="0"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100" readonly>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <button type="button" onclick="adicionarItem()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                Adicionar Item
            </button>
        </div>

        <!-- Observações -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Observações</h2>
            <textarea name="observacoes" rows="3" 
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Observações adicionais">{{ old('observacoes', $nfe->observacoes) }}</textarea>
        </div>

        <!-- Botões de Ação -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('nfe.show', $nfe) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                Cancelar
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                Atualizar NFe
            </button>
        </div>
    </form>
</div>

<script>
let itemIndex = {{ count($nfe->items) }};

function adicionarItem() {
    // Código similar ao create.blade.php
}

function removerItem(index) {
    // Código similar ao create.blade.php
}

function calcularTotais() {
    // Código similar ao create.blade.php
}
</script>
@endsection
