@extends('dashboard.layout')
@section('content')
<div class="w-full max-w-screen-xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <a href="{{ route('suppliers.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <i class="fas fa-truck"></i> Novo Fornecedor
        </h2>
    </div>
    <form method="POST" action="{{ route('suppliers.store') }}" class="bg-white p-6 rounded-lg shadow-md">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Tipo</label>
                <select name="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
                    <option value="">Selecione</option>
                    <option value="empresa">Empresa</option>
                    <option value="pessoa">Pessoa Física</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">CNPJ</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <input type="text" name="cnpj" id="cnpj" placeholder="00.000.000/0000-00" class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
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
            <div class="sm:col-span-2 lg:col-span-3 xl:col-span-4">
                <label class="block text-sm text-gray-700 font-medium mb-1">Nome</label>
                <input type="text" name="name" id="name" placeholder="Nome do fornecedor" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
                    <option value="">Selecione</option>
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>
        </div>
        <h2 class="text-lg font-semibold text-blue-800 mt-6 mb-2">Contato Principal</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Nome</label>
                <input type="text" name="contact_name" id="contact_name" placeholder="Nome do contato" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">E-mail</label>
                <input type="email" name="contact_email" id="contact_email" placeholder="email@exemplo.com" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Telefone</label>
                <input type="text" name="contact_phone" id="contact_phone" placeholder="(99) 99999-9999" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Site</label>
                <input type="text" name="contact_site" id="contact_site" placeholder="www.exemplo.com" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm">
            </div>
        </div>
        <div class="sm:col-span-2 lg:col-span-3 xl:col-span-4">
            <label class="block text-sm text-gray-700 font-medium mb-1">Descrição</label>
            <textarea name="description" rows="3" placeholder="Informações adicionais..." class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm"></textarea>
        </div>
        <h2 class="text-lg font-semibold text-blue-800 mt-6 mb-2">Endereço</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">CEP</label>
                <input type="text" name="cep" id="cep" placeholder="00000-000" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Endereço</label>
                <input type="text" name="address" id="address" placeholder="Rua, avenida..." class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Número</label>
                <input type="text" name="number" id="number" placeholder="Número" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Complemento</label>
                <input type="text" name="complement" placeholder="Sala, apto, etc." class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Bairro</label>
                <input type="text" name="neighborhood" id="neighborhood" placeholder="Bairro" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Estado</label>
                <input type="text" name="state" id="state" placeholder="Estado" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Cidade</label>
                <input type="text" name="city" id="city" placeholder="Cidade" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">País</label>
                <input type="text" name="country" id="country" placeholder="Brasil" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="Brasil">
            </div>
        </div>
        <div class="flex justify-end mt-8 gap-3">
            <a href="{{ route('suppliers.index') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm border">Cancelar</a>
            <button type="submit" class="px-6 py-2 rounded bg-green-600 text-white hover:bg-green-700 font-semibold text-sm shadow transition-colors">Salvar</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cnpjInput = document.getElementById('cnpj');
    const searchButton = document.getElementById('search_cnpj');
    const loadingDiv = document.getElementById('cnpj_loading');
    const errorDiv = document.getElementById('cnpj_error');
    const typeSelect = document.getElementById('type');

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
            clearSupplierData();
            lastSearchedCNPJ = ''; // Reset do último CNPJ buscado
        }

        // Cancelar busca anterior se estiver digitando
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
    });

    // Função para limpar dados do fornecedor
    function clearSupplierData() {
        document.getElementById('name').value = '';
        document.getElementById('contact_name').value = '';
        document.getElementById('contact_email').value = '';
        document.getElementById('contact_phone').value = '';
        document.getElementById('contact_site').value = '';
        document.getElementById('cep').value = '';
        document.getElementById('address').value = '';
        document.getElementById('number').value = '';
        document.getElementById('neighborhood').value = '';
        document.getElementById('state').value = '';
        document.getElementById('city').value = '';
        document.getElementById('country').value = 'Brasil';

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
                fillSupplierData(data);
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

    // Função para preencher os dados do fornecedor
    function fillSupplierData(data) {
        // Definir tipo como empresa
        typeSelect.value = 'empresa';

        // Preencher dados básicos
        document.getElementById('name').value = data.nome || data.razao_social || '';
        document.getElementById('contact_name').value = data.nome_fantasia || data.nome || data.razao_social || '';

        // Preencher endereço
        document.getElementById('cep').value = data.cep || '';
        document.getElementById('address').value = data.logradouro || '';
        document.getElementById('number').value = data.numero || '';
        document.getElementById('neighborhood').value = data.bairro || '';
        document.getElementById('state').value = data.uf || '';
        document.getElementById('city').value = data.municipio || '';
        document.getElementById('country').value = 'Brasil';

        // Preencher contato (se disponível)
        if (data.email) {
            document.getElementById('contact_email').value = data.email;
        }
        if (data.telefone) {
            document.getElementById('contact_phone').value = data.telefone;
        }
        if (data.site) {
            document.getElementById('contact_site').value = data.site;
        }
    }

    // Função para mostrar loading
    function showLoading() {
        loadingDiv.classList.remove('hidden');
        searchButton.disabled = true;
    }

    // Função para esconder loading
    function hideLoading() {
        loadingDiv.classList.add('hidden');
        searchButton.disabled = false;
    }

    // Função para mostrar erro
    function showError(message) {
        errorDiv.textContent = message;
        errorDiv.classList.remove('hidden');
    }

    // Função para esconder erro
    function hideError() {
        errorDiv.classList.add('hidden');
    }

    // Função para mostrar sucesso
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
