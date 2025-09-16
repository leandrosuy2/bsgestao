@extends('dashboard.layout')

@section('title', 'Editar Conta a Receber')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Editar Conta a Receber</h1>
        <p class="text-gray-600">Atualize as informações da conta</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border">
        <form method="POST" action="{{ route('receivables.update', $receivable) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Descrição -->
                <div class="md:col-span-2">
                    <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">
                        Descrição <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="descricao" name="descricao" value="{{ old('descricao', $receivable->descricao) }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('descricao') border-red-500 @enderror"
                           placeholder="Ex: Venda de produto X, Serviço Y">
                    @error('descricao')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cliente -->
                <div>
                    <label for="pessoa" class="block text-sm font-medium text-gray-700 mb-1">
                        Cliente/Pessoa <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="pessoa" name="pessoa" value="{{ old('pessoa', $receivable->pessoa) }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('pessoa') border-red-500 @enderror"
                           placeholder="Nome do cliente ou pessoa">
                    @error('pessoa')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Categoria -->
                <div>
                    <label for="categoria" class="block text-sm font-medium text-gray-700 mb-1">
                        Categoria <span class="text-red-500">*</span>
                    </label>
                    <select id="categoria" name="categoria"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('categoria') border-red-500 @enderror">
                        <option value="">Selecione uma categoria</option>
                        <option value="vendas" {{ old('categoria', $receivable->categoria) == 'vendas' ? 'selected' : '' }}>Vendas</option>
                        <option value="servicos" {{ old('categoria', $receivable->categoria) == 'servicos' ? 'selected' : '' }}>Serviços</option>
                        <option value="emprestimos" {{ old('categoria', $receivable->categoria) == 'emprestimos' ? 'selected' : '' }}>Empréstimos</option>
                        <option value="investimentos" {{ old('categoria', $receivable->categoria) == 'investimentos' ? 'selected' : '' }}>Investimentos</option>
                        <option value="comissoes" {{ old('categoria', $receivable->categoria) == 'comissoes' ? 'selected' : '' }}>Comissões</option>
                        <option value="royalties" {{ old('categoria', $receivable->categoria) == 'royalties' ? 'selected' : '' }}>Royalties</option>
                        <option value="dividendos" {{ old('categoria', $receivable->categoria) == 'dividendos' ? 'selected' : '' }}>Dividendos</option>
                        <option value="outros" {{ old('categoria', $receivable->categoria) == 'outros' ? 'selected' : '' }}>Outros</option>
                    </select>
                    @error('categoria')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Valor -->
                <div>
                    <label for="valor" class="block text-sm font-medium text-gray-700 mb-1">
                        Valor <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">R$</span>
                        <input type="number" id="valor" name="valor" value="{{ old('valor', $receivable->valor) }}" step="0.01" min="0.01"
                               class="w-full pl-10 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('valor') border-red-500 @enderror"
                               placeholder="0,00">
                    </div>
                    @error('valor')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Data de Vencimento -->
                <div>
                    <label for="data_vencimento" class="block text-sm font-medium text-gray-700 mb-1">
                        Data de Vencimento <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="data_vencimento" name="data_vencimento" value="{{ old('data_vencimento', $receivable->data_vencimento) }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('data_vencimento') border-red-500 @enderror">
                    @error('data_vencimento')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                        <option value="pendente" {{ old('status', $receivable->status) == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="recebido" {{ old('status', $receivable->status) == 'recebido' ? 'selected' : '' }}>Recebido</option>
                        <option value="atrasado" {{ old('status', $receivable->status) == 'atrasado' ? 'selected' : '' }}>Atrasado</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Data de Recebimento -->
                <div>
                    <label for="data_recebimento" class="block text-sm font-medium text-gray-700 mb-1">
                        Data de Recebimento
                    </label>
                    <input type="date" id="data_recebimento" name="data_recebimento" value="{{ old('data_recebimento', $receivable->data_recebimento) }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('data_recebimento') border-red-500 @enderror">
                    @error('data_recebimento')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Forma de Recebimento -->
                <div>
                    <label for="forma_recebimento" class="block text-sm font-medium text-gray-700 mb-1">
                        Forma de Recebimento <span class="text-red-500">*</span>
                    </label>
                    <select id="forma_recebimento" name="forma_recebimento"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('forma_recebimento') border-red-500 @enderror">
                        <option value="">Selecione a forma de recebimento</option>
                        <option value="boleto" {{ old('forma_recebimento', $receivable->forma_recebimento) == 'boleto' ? 'selected' : '' }}>Boleto</option>
                        <option value="transferencia" {{ old('forma_recebimento', $receivable->forma_recebimento) == 'transferencia' ? 'selected' : '' }}>Transferência</option>
                        <option value="dinheiro" {{ old('forma_recebimento', $receivable->forma_recebimento) == 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                        <option value="pix" {{ old('forma_recebimento', $receivable->forma_recebimento) == 'pix' ? 'selected' : '' }}>PIX</option>
                        <option value="cartao_credito" {{ old('forma_recebimento', $receivable->forma_recebimento) == 'cartao_credito' ? 'selected' : '' }}>Cartão de Crédito</option>
                        <option value="cartao_debito" {{ old('forma_recebimento', $receivable->forma_recebimento) == 'cartao_debito' ? 'selected' : '' }}>Cartão de Débito</option>
                        <option value="cheque" {{ old('forma_recebimento', $receivable->forma_recebimento) == 'cheque' ? 'selected' : '' }}>Cheque</option>
                    </select>
                    @error('forma_recebimento')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Comprovante -->
                <div>
                    <label for="comprovante" class="block text-sm font-medium text-gray-700 mb-1">
                        Comprovante
                    </label>
                    <input type="text" id="comprovante" name="comprovante" value="{{ old('comprovante', $receivable->comprovante) }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('comprovante') border-red-500 @enderror"
                           placeholder="Número do comprovante ou referência">
                    @error('comprovante')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Observações -->
            <div>
                <label for="observacoes" class="block text-sm font-medium text-gray-700 mb-1">
                    Observações
                </label>
                <textarea id="observacoes" name="observacoes" rows="4"
                          class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('observacoes') border-red-500 @enderror"
                          placeholder="Informações adicionais sobre esta conta">{{ old('observacoes', $receivable->observacoes) }}</textarea>
                @error('observacoes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botões -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('receivables.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                    Atualizar Conta
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
