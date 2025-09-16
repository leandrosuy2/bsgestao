@extends('dashboard.layout')

@section('title', 'Nova Empresa')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Nova Empresa</h1>
                <a href="{{ route('admin.companies.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    Voltar
                </a>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.companies.store') }}" method="POST">
                @csrf
                <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative">
                        <label class="block text-gray-700 font-semibold mb-1">Nome da Empresa</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <input type="text" name="name" id="company_name" value="{{ old('name') }}" placeholder="Digite o nome da empresa" class="w-full border rounded pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>
                    <div class="relative">
                        <label class="block text-gray-700 font-semibold mb-1">CNPJ</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <input type="text" name="cnpj" id="cnpj" value="{{ old('cnpj') }}" placeholder="00.000.000/0000-00" class="w-full border rounded pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" maxlength="18">
                            <button type="button" id="search_cnpj" class="absolute inset-y-0 right-0 px-3 flex items-center bg-blue-500 hover:bg-blue-600 text-white rounded-r transition duration-200">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                        </div>
                        <div id="cnpj_loading" class="hidden mt-2 text-sm text-blue-600">
                            <svg class="animate-spin h-4 w-4 inline mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Buscando dados da empresa...
                        </div>
                        <div id="cnpj_error" class="hidden mt-2 text-sm text-red-600"></div>
                        <div class="mt-2 text-xs text-gray-500">
                            💡 <strong>Dica:</strong> Digite um CNPJ válido e clique na lupa ou saia do campo para buscar automaticamente os dados da empresa.
                            <br>
                            🧪 <strong>Exemplo para teste:</strong> <button type="button" onclick="fillTestCNPJ()" class="text-blue-600 hover:underline">45.590.374/0001-42</button> (CNPJ real)
                        </div>
                    </div>
                    <div class="relative">
                        <label class="block text-gray-700 font-semibold mb-1">E-mail Principal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="email" name="email" id="company_email" value="{{ old('email') }}" placeholder="contato@empresa.com" class="w-full border rounded pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>
                    <div class="relative">
                        <label class="block text-gray-700 font-semibold mb-1">Telefone</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <input type="text" name="phone" id="company_phone" value="{{ old('phone') }}" placeholder="(11) 99999-9999" class="w-full border rounded pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="relative">
                        <label class="block text-gray-700 font-semibold mb-1">Endereço</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <input type="text" name="address" id="company_address" value="{{ old('address') }}" placeholder="Rua das Flores" class="w-full border rounded pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="relative">
                        <label class="block text-gray-700 font-semibold mb-1">Número</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                            </div>
                            <input type="text" name="address_number" id="company_address_number" value="{{ old('address_number') }}" placeholder="123" class="w-full border rounded pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="relative">
                        <label class="block text-gray-700 font-semibold mb-1">Bairro</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <input type="text" name="neighborhood" id="company_neighborhood" value="{{ old('neighborhood') }}" placeholder="Centro" class="w-full border rounded pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="relative">
                        <label class="block text-gray-700 font-semibold mb-1">Cidade</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <input type="text" name="city" id="company_city" value="{{ old('city') }}" placeholder="São Paulo" class="w-full border rounded pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="relative">
                        <label class="block text-gray-700 font-semibold mb-1">Estado</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <input type="text" name="state" id="company_state" value="{{ old('state') }}" placeholder="SP" class="w-full border rounded pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="relative">
                        <label class="block text-gray-700 font-semibold mb-1">CEP</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <input type="text" name="zip" id="company_zip" value="{{ old('zip') }}" placeholder="00000-000" class="w-full border rounded pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
                <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative">
                        <label class="block text-gray-700 font-semibold mb-1">Nome do Responsável</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input type="text" name="responsible_name" value="{{ old('responsible_name') }}" placeholder="Nome completo do responsável" class="w-full border rounded pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="relative">
                        <label class="block text-gray-700 font-semibold mb-1">E-mail do Responsável</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="email" name="responsible_email" value="{{ old('responsible_email') }}" placeholder="responsavel@empresa.com" class="w-full border rounded pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="relative">
                        <label class="block text-gray-700 font-semibold mb-1">Telefone do Responsável</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <input type="text" name="responsible_phone" value="{{ old('responsible_phone') }}" placeholder="(11) 99999-9999" class="w-full border rounded pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">Observações</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 pt-3 flex items-start pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <textarea name="notes" placeholder="Informações adicionais sobre a empresa..." class="w-full border rounded pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="3">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <hr class="my-6">
                <h2 class="text-lg font-bold mb-2">Usuário Master da Empresa</h2>
                <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative">
                        <label class="block text-gray-700 font-semibold mb-1">Nome do Usuário</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input type="text" name="user_name" value="{{ old('user_name') }}" placeholder="Nome completo do usuário master" class="w-full border rounded pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>
                    <div class="relative">
                        <label class="block text-gray-700 font-semibold mb-1">E-mail do Usuário</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="email" name="user_email" value="{{ old('user_email') }}" placeholder="usuario@empresa.com" class="w-full border rounded pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>
                    <div class="relative">
                        <label class="block text-gray-700 font-semibold mb-1">Senha</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input type="password" name="user_password" placeholder="Mínimo 6 caracteres" class="w-full border rounded pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>
                    <div class="relative">
                        <label class="block text-gray-700 font-semibold mb-1">Confirme a Senha</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input type="password" name="user_password_confirmation" placeholder="Digite a senha novamente" class="w-full border rounded pl-10 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition duration-200">
                        Cadastrar Empresa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cnpjInput = document.getElementById('cnpj');
    const searchButton = document.getElementById('search_cnpj');
    const loadingDiv = document.getElementById('cnpj_loading');
    const errorDiv = document.getElementById('cnpj_error');

    let searchTimeout = null;
    let isSearching = false;
    let lastSearchedCNPJ = '';

    // Máscara para CNPJ
    function maskCNPJ(value) {
        return value
            .replace(/\D/g, '')
            .replace(/(\d{2})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1/$2')
            .replace(/(\d{4})(\d)/, '$1-$2')
            .replace(/(-\d{2})\d+?$/, '$1');
    }

    cnpjInput.addEventListener('input', function(e) {
        e.target.value = maskCNPJ(e.target.value);

        // Limpar dados quando CNPJ for alterado
        const cnpj = e.target.value.replace(/\D/g, '');
        if (cnpj.length < 14) {
            clearCompanyData();
            lastSearchedCNPJ = ''; // Reset do último CNPJ buscado
        }

        // Cancelar busca anterior se estiver digitando
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
    });

    // Função para limpar dados da empresa
    function clearCompanyData() {
        document.getElementById('company_name').value = '';
        document.getElementById('company_email').value = '';
        document.getElementById('company_phone').value = '';
        document.getElementById('company_address').value = '';
        document.getElementById('company_address_number').value = '';
        document.getElementById('company_neighborhood').value = '';
        document.getElementById('company_city').value = '';
        document.getElementById('company_state').value = '';
        document.getElementById('company_zip').value = '';

        // Limpar dados do responsável
        document.querySelector('input[name="responsible_name"]').value = '';
        document.querySelector('input[name="responsible_email"]').value = '';
        document.querySelector('input[name="responsible_phone"]').value = '';

        // Resetar variáveis de controle
        lastSearchedCNPJ = '';
        if (searchTimeout) {
            clearTimeout(searchTimeout);
            searchTimeout = null;
        }
    }

        // Função para validar CNPJ
    function validateCNPJ(cnpj) {
        // Remove caracteres não numéricos
        cnpj = cnpj.replace(/\D/g, '');

        // Verifica se tem 14 dígitos
        if (cnpj.length !== 14) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (/^(\d)\1+$/.test(cnpj)) {
            return false;
        }

        // Validação dos dígitos verificadores
        let soma = 0;
        let peso = 2;

        // Primeiro dígito verificador
        for (let i = 11; i >= 0; i--) {
            soma += parseInt(cnpj.charAt(i)) * peso;
            peso = peso === 9 ? 2 : peso + 1;
        }

        let digito = 11 - (soma % 11);
        if (digito > 9) digito = 0;

        if (parseInt(cnpj.charAt(12)) !== digito) {
            return false;
        }

        // Segundo dígito verificador
        soma = 0;
        peso = 2;

        for (let i = 12; i >= 0; i--) {
            soma += parseInt(cnpj.charAt(i)) * peso;
            peso = peso === 9 ? 2 : peso + 1;
        }

        digito = 11 - (soma % 11);
        if (digito > 9) digito = 0;

        if (parseInt(cnpj.charAt(13)) !== digito) {
            return false;
        }

        return true;
    }

    // Função para buscar dados do CNPJ
    async function searchCNPJ() {
        const cnpj = cnpjInput.value.replace(/\D/g, '');

        if (cnpj.length !== 14) {
            showError('CNPJ deve ter 14 dígitos');
            return;
        }

        if (!validateCNPJ(cnpj)) {
            showError('CNPJ inválido');
            return;
        }

        // Evitar requisições duplicadas
        if (isSearching) {
            return;
        }

        // Evitar buscar o mesmo CNPJ novamente
        if (lastSearchedCNPJ === cnpj) {
            return;
        }

        isSearching = true;
        lastSearchedCNPJ = cnpj;
        showLoading();
        hideError();

        try {
            const response = await fetch(`/api/cnpj/${cnpj}`);
            const data = await response.json();

            if (response.ok) {
                fillCompanyData(data);
                showSuccess('Dados da empresa carregados com sucesso!');
            } else {
                showError(data.message || 'Erro ao buscar dados do CNPJ');
            }
        } catch (error) {
            showError('Erro de conexão. Tente novamente.');
        } finally {
            hideLoading();
            isSearching = false;
        }
    }

    // Função para preencher os dados da empresa
    function fillCompanyData(data) {
        document.getElementById('company_name').value = data.nome || '';
        document.getElementById('company_email').value = data.email || '';
        document.getElementById('company_phone').value = data.telefone || '';
        document.getElementById('company_address').value = data.logradouro || '';
        document.getElementById('company_address_number').value = data.numero || '';
        document.getElementById('company_neighborhood').value = data.bairro || '';
        document.getElementById('company_city').value = data.municipio || '';
        document.getElementById('company_state').value = data.uf || '';
        document.getElementById('company_zip').value = data.cep || '';

        // Preencher dados do responsável
        document.querySelector('input[name="responsible_name"]').value = data.responsible_name || '';
        document.querySelector('input[name="responsible_email"]').value = data.responsible_email || '';
        document.querySelector('input[name="responsible_phone"]').value = data.responsible_phone || '';
    }



    // Funções auxiliares
    function showLoading() {
        loadingDiv.classList.remove('hidden');
        searchButton.disabled = true;
    }

    function hideLoading() {
        loadingDiv.classList.add('hidden');
        searchButton.disabled = false;
    }

    function showError(message) {
        errorDiv.textContent = message;
        errorDiv.classList.remove('hidden');
    }

    function hideError() {
        errorDiv.classList.add('hidden');
    }

    function showSuccess(message) {
        // Criar notificação de sucesso
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

        // Função para preencher CNPJ de teste
    window.fillTestCNPJ = function() {
        cnpjInput.value = '45.590.374/0001-42';
        lastSearchedCNPJ = ''; // Reset para permitir busca do CNPJ de teste
        searchCNPJ();
    };

    // Event listeners
    searchButton.addEventListener('click', searchCNPJ);

    // Buscar automaticamente quando CNPJ estiver completo (com debounce)
    cnpjInput.addEventListener('blur', function() {
        const cnpj = this.value.replace(/\D/g, '');
        if (cnpj.length === 14) {
            // Usar debounce para evitar múltiplas requisições
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }
            searchTimeout = setTimeout(() => {
                searchCNPJ();
            }, 300);
        }
    });
});
</script>
@endsection
