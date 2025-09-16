@extends('dashboard.layout')

@section('title', 'Editar Cliente')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Editar Cliente</h1>
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

    <form action="{{ route('customers.update', $customer) }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="col-span-1 md:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Nome do cliente" required>
            </div>
            
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select name="type" id="type" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="pessoa_fisica" {{ old('type', $customer->type) === 'pessoa_fisica' ? 'selected' : '' }}>Pessoa Física</option>
                    <option value="pessoa_juridica" {{ old('type', $customer->type) === 'pessoa_juridica' ? 'selected' : '' }}>Pessoa Jurídica</option>
                </select>
            </div>

            <div>
                <label for="cpf_cnpj" class="block text-sm font-medium text-gray-700 mb-1">CPF ou CNPJ</label>
                <input type="text" name="cpf_cnpj" id="cpf_cnpj" value="{{ old('cpf_cnpj', $customer->cpf_cnpj) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="000.000.000-00">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="(99) 99999-9999">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="email@cliente.com">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="col-span-1 md:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Logradouro</label>
                <input type="text" name="address" id="address" value="{{ old('address', $customer->address) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Rua, Avenida, etc">
            </div>

            <div>
                <label for="number" class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                <input type="text" name="number" id="number" value="{{ old('number', $customer->number) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="123">
            </div>

            <div>
                <label for="neighborhood" class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                <input type="text" name="neighborhood" id="neighborhood" value="{{ old('neighborhood', $customer->neighborhood) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Centro">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div>
                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                <input type="text" name="city" id="city" value="{{ old('city', $customer->city) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Cidade">
            </div>

            <div>
                <label for="state" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="state" id="state" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Selecione</option>
                    @foreach(['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $uf)
                        <option value="{{ $uf }}" {{ old('state', $customer->state) === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $customer->postal_code) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="00000-000">
            </div>

            <div class="flex items-center">
                <input type="hidden" name="active" value="0">
                <input type="checkbox" name="active" id="active" value="1" {{ old('active', $customer->active) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="active" class="ml-2 block text-sm text-gray-900">Cliente ativo</label>
            </div>
        </div>

        <div class="mb-6">
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
            <textarea name="notes" id="notes" rows="3" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Informações adicionais...">{{ old('notes', $customer->notes) }}</textarea>
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('customers.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                Atualizar
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('cpf_cnpj').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    
    if (value.length <= 11) {
        // CPF
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    } else {
        // CNPJ
        value = value.replace(/(\d{2})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1/$2');
        value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    }
    
    this.value = value;
});

document.getElementById('postal_code').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    value = value.replace(/(\d{5})(\d)/, '$1-$2');
    this.value = value;
});

document.getElementById('phone').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    
    if (value.length <= 10) {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
    } else {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    }
    
    this.value = value;
});
</script>
@endsection
