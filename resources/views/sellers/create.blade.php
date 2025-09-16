@extends('dashboard.layout')

@section('content')
<div class="w-full max-w-screen-xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <a href="{{ route('sellers.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            {{ isset($seller) ? 'Editar Vendedor' : 'Novo Vendedor' }}
        </h2>
    </div>

    @include('dashboard.alerts')

    <form action="{{ isset($seller) ? route('sellers.update', $seller->id) : route('sellers.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md">
        @csrf
        @if(isset($seller))
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div class="sm:col-span-2">
                <label class="block text-sm text-gray-700 font-medium mb-1">Nome Completo *</label>
                <input type="text" name="name" placeholder="Digite o nome completo do vendedor" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm @error('name') border-red-500 @enderror" 
                       required value="{{ old('name', $seller->name ?? '') }}">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">CPF/CNPJ *</label>
                <input type="text" name="cpf_cnpj" placeholder="000.000.000-00" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm @error('cpf_cnpj') border-red-500 @enderror" 
                       required value="{{ old('cpf_cnpj', $seller->cpf_cnpj ?? '') }}">
                @error('cpf_cnpj')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Taxa de Comissão (%) *</label>
                <input type="number" step="0.01" min="0" max="100" name="commission_rate" placeholder="0.00" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm @error('commission_rate') border-red-500 @enderror" 
                       required value="{{ old('commission_rate', $seller->commission_rate ?? '0') }}">
                @error('commission_rate')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">E-mail</label>
                <input type="email" name="email" placeholder="email@exemplo.com" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm @error('email') border-red-500 @enderror" 
                       value="{{ old('email', $seller->email ?? '') }}">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Telefone</label>
                <input type="text" name="phone" placeholder="(00) 00000-0000" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm @error('phone') border-red-500 @enderror" 
                       value="{{ old('phone', $seller->phone ?? '') }}">
                @error('phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="sm:col-span-2 lg:col-span-3 xl:col-span-2">
                <label class="block text-sm text-gray-700 font-medium mb-1">Observações</label>
                <textarea name="notes" rows="3" placeholder="Informações adicionais sobre o vendedor..." 
                          class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm @error('notes') border-red-500 @enderror">{{ old('notes', $seller->notes ?? '') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end mt-8 gap-3">
            <a href="{{ route('sellers.index') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm border transition-colors">Cancelar</a>
            <button type="submit" class="px-6 py-2 rounded bg-green-600 text-white hover:bg-green-700 font-semibold text-sm shadow transition-colors">
                {{ isset($seller) ? 'Atualizar' : 'Salvar' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Aplicar máscaras
    const cpfCnpjInput = document.querySelector('input[name="cpf_cnpj"]');
    const phoneInput = document.querySelector('input[name="phone"]');
    
    if (cpfCnpjInput) {
        cpfCnpjInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length <= 11) {
                // CPF
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            } else {
                // CNPJ
                value = value.replace(/^(\d{2})(\d)/, '$1.$2');
                value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            }
            this.value = value;
        });
    }
    
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
            value = value.replace(/(\d)(\d{4})$/, '$1-$2');
            this.value = value;
        });
    }
});
</script>
@endpush
@endsection
