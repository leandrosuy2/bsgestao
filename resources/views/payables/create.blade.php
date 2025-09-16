@extends('dashboard.layout')

@section('title', 'Nova Conta a Pagar')

@section('content')
<div class="w-full max-w-screen-xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <a href="{{ route('payables.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Nova Conta a Pagar
        </h2>
    </div>

    <form method="POST" action="{{ route('payables.store') }}" class="bg-white p-6 rounded-lg shadow-md">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div class="sm:col-span-2 xl:col-span-4">
                <label for="descricao" class="block text-sm text-gray-700 font-medium mb-1">Descrição <span class="text-red-500">*</span></label>
                <input type="text" id="descricao" name="descricao" value="{{ old('descricao') }}" placeholder="Ex: Pagamento do aluguel, Compra de produtos" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600 bg-gray-50 text-sm @error('descricao') border-red-500 @enderror" required>
                @error('descricao')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="pessoa" class="block text-sm text-gray-700 font-medium mb-1">Fornecedor/Pessoa <span class="text-red-500">*</span></label>
                <input type="text" id="pessoa" name="pessoa" value="{{ old('pessoa') }}" placeholder="Nome do fornecedor ou pessoa" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600 bg-gray-50 text-sm @error('pessoa') border-red-500 @enderror" required>
                @error('pessoa')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="categoria" class="block text-sm text-gray-700 font-medium mb-1">Categoria <span class="text-red-500">*</span></label>
                <select id="categoria" name="categoria" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600 bg-gray-50 text-sm @error('categoria') border-red-500 @enderror" required>
                    <option value="">Selecione uma categoria</option>
                    <option value="aluguel" {{ old('categoria') == 'aluguel' ? 'selected' : '' }}>Aluguel</option>
                    <option value="fornecedor" {{ old('categoria') == 'fornecedor' ? 'selected' : '' }}>Fornecedor</option>
                    <option value="servico" {{ old('categoria') == 'servico' ? 'selected' : '' }}>Serviço</option>
                    <option value="imposto" {{ old('categoria') == 'imposto' ? 'selected' : '' }}>Imposto</option>
                    <option value="salario" {{ old('categoria') == 'salario' ? 'selected' : '' }}>Salário</option>
                    <option value="energia" {{ old('categoria') == 'energia' ? 'selected' : '' }}>Energia</option>
                    <option value="agua" {{ old('categoria') == 'agua' ? 'selected' : '' }}>Água</option>
                    <option value="internet" {{ old('categoria') == 'internet' ? 'selected' : '' }}>Internet</option>
                    <option value="telefone" {{ old('categoria') == 'telefone' ? 'selected' : '' }}>Telefone</option>
                    <option value="outros" {{ old('categoria') == 'outros' ? 'selected' : '' }}>Outros</option>
                </select>
                @error('categoria')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="valor" class="block text-sm text-gray-700 font-medium mb-1">Valor <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">R$</span>
                    <input type="number" id="valor" name="valor" value="{{ old('valor') }}" step="0.01" min="0.01" placeholder="0,00" class="w-full pl-10 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600 bg-gray-50 text-sm @error('valor') border-red-500 @enderror" required>
                </div>
                @error('valor')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="data_vencimento" class="block text-sm text-gray-700 font-medium mb-1">Data de Vencimento <span class="text-red-500">*</span></label>
                <input type="date" id="data_vencimento" name="data_vencimento" value="{{ old('data_vencimento') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600 bg-gray-50 text-sm @error('data_vencimento') border-red-500 @enderror" required>
                @error('data_vencimento')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="forma_pagamento" class="block text-sm text-gray-700 font-medium mb-1">Forma de Pagamento <span class="text-red-500">*</span></label>
                <select id="forma_pagamento" name="forma_pagamento" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600 bg-gray-50 text-sm @error('forma_pagamento') border-red-500 @enderror" required>
                    <option value="">Selecione a forma de pagamento</option>
                    <option value="boleto" {{ old('forma_pagamento') == 'boleto' ? 'selected' : '' }}>Boleto</option>
                    <option value="transferencia" {{ old('forma_pagamento') == 'transferencia' ? 'selected' : '' }}>Transferência</option>
                    <option value="dinheiro" {{ old('forma_pagamento') == 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                    <option value="pix" {{ old('forma_pagamento') == 'pix' ? 'selected' : '' }}>PIX</option>
                    <option value="cartao_credito" {{ old('forma_pagamento') == 'cartao_credito' ? 'selected' : '' }}>Cartão de Crédito</option>
                    <option value="cartao_debito" {{ old('forma_pagamento') == 'cartao_debito' ? 'selected' : '' }}>Cartão de Débito</option>
                    <option value="cheque" {{ old('forma_pagamento') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                </select>
                @error('forma_pagamento')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="comprovante" class="block text-sm text-gray-700 font-medium mb-1">Comprovante</label>
                <input type="text" id="comprovante" name="comprovante" value="{{ old('comprovante') }}" placeholder="Número do comprovante ou referência" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600 bg-gray-50 text-sm @error('comprovante') border-red-500 @enderror">
                @error('comprovante')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="sm:col-span-2 xl:col-span-4">
                <label for="observacoes" class="block text-sm text-gray-700 font-medium mb-1">Observações</label>
                <textarea id="observacoes" name="observacoes" rows="3" placeholder="Informações adicionais sobre esta conta" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600 bg-gray-50 text-sm @error('observacoes') border-red-500 @enderror">{{ old('observacoes') }}</textarea>
                @error('observacoes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex justify-end mt-8 gap-3">
            <a href="{{ route('payables.index') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm border">Cancelar</a>
            <button type="submit" class="px-6 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 font-semibold text-sm shadow">Salvar Conta</button>
        </div>
    </form>
</div>
@endsection
