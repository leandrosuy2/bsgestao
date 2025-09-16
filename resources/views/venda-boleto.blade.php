@extends('dashboard.layout')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <span class="bg-green-600 p-2 rounded-full shadow">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75M9 6h.008v.008H9V6zm3.75 0h.008v.008h-.008V6z"/>
            </svg>
        </span>
        <h1 class="text-2xl font-bold text-gray-800">Gerar Boleto Sicredi</h1>
    </div>
    <a href="/pesquisar-boletos" class="inline-flex items-center gap-2 bg-gradient-to-r from-gray-800 to-gray-700 text-white px-5 py-2 rounded-xl hover:from-gray-900 hover:to-gray-800 transition font-semibold shadow-lg ring-1 ring-gray-900/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        Pesquisar Boletos
    </a>
</div>

@if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-100 border border-green-200 text-green-800 rounded shadow-sm">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 px-4 py-3 bg-red-100 border border-red-200 text-red-800 rounded shadow-sm">
        {{ session('error') }}
    </div>
@endif
<div class="bg-white rounded-xl shadow-sm">
    <form method="POST" action="{{ route('venda-boleto.gerar') }}" class="p-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="relative lg:col-span-2">
                <label class="block text-sm text-gray-700 font-medium mb-1">Nome do Cliente</label>
                <input type="text" name="cliente_nome" id="cliente_nome" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 bg-gray-50 text-sm" required autocomplete="off" placeholder="Nome completo" value="{{ old('cliente_nome') }}">
                <div id="autocomplete-list" class="absolute z-50 bg-white border border-gray-200 rounded shadow mt-1 w-full hidden" style="max-height:180px;overflow-y:auto;"></div>
                <span class="text-xs text-gray-500">Digite para buscar clientes cadastrados</span>
            </div>
            
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Documento (CPF ou CNPJ)</label>
                <input type="text" name="cliente_documento" id="cliente_documento" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 bg-gray-50 text-sm" required placeholder="Somente números" value="{{ old('cliente_documento') }}">
            </div>

            <div class="lg:col-span-3">
                <label class="block text-sm text-gray-700 font-medium mb-1">Endereço</label>
                <input type="text" name="cliente_endereco" id="cliente_endereco" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 bg-gray-50 text-sm" required placeholder="Rua, número, complemento" value="{{ old('cliente_endereco') }}">
            </div>

            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Cidade</label>
                <input type="text" name="cliente_cidade" id="cliente_cidade" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 bg-gray-50 text-sm" required placeholder="Cidade" value="{{ old('cliente_cidade') }}">
            </div>

            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">UF</label>
                <input type="text" name="cliente_uf" id="cliente_uf" maxlength="2" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 bg-gray-50 text-sm" required placeholder="UF" value="{{ old('cliente_uf') }}">
            </div>

            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">CEP</label>
                <input type="text" name="cliente_cep" id="cliente_cep" maxlength="8" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 bg-gray-50 text-sm" required placeholder="CEP (8 dígitos)" value="{{ old('cliente_cep') }}">
            </div>

            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Valor do Boleto</label>
                <input type="number" step="0.01" name="valor" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 bg-gray-50 text-sm" required placeholder="Ex: 100.00" value="{{ old('valor') }}">
            </div>

            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Data de Vencimento</label>
                <input type="date" name="dataVencimento" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 bg-gray-50 text-sm" required value="{{ old('dataVencimento') }}">
            </div>

            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Identificação Interna</label>
                <input type="text" name="seuNumero" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 bg-gray-50 text-sm" placeholder="Seu número (opcional)" value="{{ old('seuNumero') }}">
            </div>

            <div class="lg:col-span-3">
                <label class="block text-sm text-gray-700 font-medium mb-1">Instruções (até 5, máx. 80 caracteres cada)</label>
                <textarea name="instrucoes" id="instrucoes" rows="3" maxlength="400" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-green-500 bg-gray-50 text-sm" required placeholder="Ex: Pagamento até o vencimento. Máximo 5 instruções, separadas por linha.">{{ old('instrucoes') }}</textarea>
                <span class="text-xs text-gray-500">Separe cada instrução por linha. Mínimo 1, máximo 5.</span>
            </div>
        </div>

        <div class="flex justify-end mt-8 gap-3">
            <button type="submit" id="btn-gerar-boleto" class="px-6 py-2 rounded bg-green-600 text-white hover:bg-green-700 font-semibold text-sm shadow" disabled>
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Gerar Boleto
                </span>
            </button>
        </div>
    </form>
</div>
    @if(session('boleto_pdf'))
        <div class="mb-4 px-4 py-3 bg-green-100 border border-green-200 text-green-800 rounded shadow-sm">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-semibold">Boleto gerado com sucesso!</span>
            </div>
            <div class="mt-3">
                <a href="{{ session('boleto_pdf') }}" target="_blank" 
                   class="inline-flex items-center gap-1 text-white bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 font-semibold px-3 py-1.5 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Abrir PDF do Boleto
                </a>
            </div>
        </div>
    @endif

    @if(session('boleto_result'))
        <div class="mb-4 px-4 py-3 bg-blue-100 border border-blue-200 text-blue-800 rounded shadow-sm">
            <pre class="whitespace-pre-wrap text-sm">{{ session('boleto_result') }}</pre>
        </div>
    @endif
@endsection
@section('scripts')
<script>
if (window.scriptJaCarregado) {
    console.log('Script já foi carregado, ignorando duplicação');
} else {
    window.scriptJaCarregado = true;
    console.log('Script carregado pela primeira vez!');

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action*="venda-boleto.gerar"]');
        const btn = document.getElementById('btn-gerar-boleto');
        const input = document.getElementById('cliente_nome');
        const autocompleteList = document.getElementById('autocomplete-list');
        let clientes = [];

        console.log('Elementos encontrados:', { 
            form: form ? 'SIM' : 'NÃO', 
            btn: btn ? 'SIM' : 'NÃO', 
            input: input ? 'SIM' : 'NÃO',
            formAction: form ? form.action : 'form não encontrado',
            btnId: btn ? btn.id : 'botão não encontrado'
        });

    // Funções de validação de CPF e CNPJ
    function validarCPF(cpf) {
        cpf = cpf.replace(/\D/g, '');
        if (cpf.length !== 11) return false;
        let soma = 0, resto;
        if (/^(\d)\1+$/.test(cpf)) return false;
        for (let i = 1; i <= 9; i++) soma += parseInt(cpf.substring(i-1, i)) * (11 - i);
        resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf.substring(9, 10))) return false;
        soma = 0;
        for (let i = 1; i <= 10; i++) soma += parseInt(cpf.substring(i-1, i)) * (12 - i);
        resto = (soma * 10) % 11;
        if (resto === 10 || resto === 11) resto = 0;
        if (resto !== parseInt(cpf.substring(10, 11))) return false;
        return true;
    }

    function validarCNPJ(cnpj) {
        cnpj = cnpj.replace(/\D/g, '');
        if (cnpj.length !== 14) return false;
        if (/^(\d)\1+$/.test(cnpj)) return false;
        let tamanho = cnpj.length - 2;
        let numeros = cnpj.substring(0, tamanho);
        let digitos = cnpj.substring(tamanho);
        let soma = 0, pos = tamanho - 7;
        for (let i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) pos = 9;
        }
        let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado !== parseInt(digitos.charAt(0))) return false;
        tamanho++;
        numeros = cnpj.substring(0, tamanho);
        soma = 0; pos = tamanho - 7;
        for (let i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) pos = 9;
        }
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado !== parseInt(digitos.charAt(1))) return false;
        return true;
    }

    // Função para mostrar erro em campo específico
    function mostrarErro(campo, mensagem) {
        campo.classList.add('border-red-500');
        campo.classList.remove('border-gray-300');
        let errorSpan = campo.parentNode.querySelector('.error-message');
        if (!errorSpan) {
            errorSpan = document.createElement('span');
            errorSpan.className = 'error-message text-xs text-red-600 mt-1 block';
            campo.parentNode.appendChild(errorSpan);
        }
        errorSpan.textContent = mensagem;
    }

    // Função simples para validar se todos os campos obrigatórios estão preenchidos
    function validarFormulario() {
        const campos = [
            'cliente_nome', 'cliente_documento', 'cliente_endereco', 
            'cliente_cidade', 'cliente_uf', 'cliente_cep'
        ];
        
        let todosPreenchidos = true;
        let camposStatus = {};
        
        campos.forEach(id => {
            const campo = document.getElementById(id);
            const preenchido = campo && campo.value.trim();
            camposStatus[id] = preenchido ? 'OK' : 'VAZIO';
            if (!preenchido) {
                todosPreenchidos = false;
            }
        });
        
        // Validar documento (CPF ou CNPJ)
        const doc = document.getElementById('cliente_documento');
        if (doc && doc.value.trim()) {
            const val = doc.value.replace(/\D/g, '');
            if (val.length === 11) {
                if (!validarCPF(val)) {
                    camposStatus.cliente_documento = 'CPF_INVALIDO';
                    todosPreenchidos = false;
                } else {
                    camposStatus.cliente_documento = 'CPF_OK';
                }
            } else if (val.length === 14) {
                if (!validarCNPJ(val)) {
                    camposStatus.cliente_documento = 'CNPJ_INVALIDO';
                    todosPreenchidos = false;
                } else {
                    camposStatus.cliente_documento = 'CNPJ_OK';
                }
            } else {
                camposStatus.cliente_documento = 'TAMANHO_INVALIDO';
                todosPreenchidos = false;
            }
        }
        
        // Verificar valor e data
        const valor = document.querySelector('input[name="valor"]');
        const data = document.querySelector('input[name="dataVencimento"]');
        const instrucoes = document.getElementById('instrucoes');
        const seuNumero = document.querySelector('input[name="seuNumero"]');
        
        camposStatus.valor = (valor && valor.value && parseFloat(valor.value) > 0) ? 'OK' : 'VAZIO';
        camposStatus.dataVencimento = (data && data.value) ? 'OK' : 'VAZIO';
        camposStatus.instrucoes = (instrucoes && instrucoes.value.trim()) ? 'OK' : 'VAZIO';
        camposStatus.seuNumero = (seuNumero && seuNumero.value.trim()) ? 'OK' : 'VAZIO';
        
        if (!valor || !valor.value || parseFloat(valor.value) <= 0) todosPreenchidos = false;
        if (!data || !data.value) todosPreenchidos = false;
        if (!instrucoes || !instrucoes.value.trim()) todosPreenchidos = false;
        if (!seuNumero || !seuNumero.value.trim()) todosPreenchidos = false;
        
        console.log('Status dos campos:', camposStatus);
        console.log('Validação simples:', todosPreenchidos);
        return todosPreenchidos;
    }    // Atualizar botão
    function atualizarBotao() {
        if (btn) {
            const valido = validarFormulario();
            btn.disabled = !valido;
            console.log('Botão disabled:', btn.disabled);
            
            // Mostrar erros específicos do documento
            const doc = document.getElementById('cliente_documento');
            if (doc && doc.value.trim()) {
                const val = doc.value.replace(/\D/g, '');
                if (val.length === 11 && !validarCPF(val)) {
                    mostrarErro(doc, 'CPF inválido');
                } else if (val.length === 14 && !validarCNPJ(val)) {
                    mostrarErro(doc, 'CNPJ inválido');
                } else if (val.length !== 11 && val.length !== 14) {
                    mostrarErro(doc, 'Documento deve ter 11 (CPF) ou 14 (CNPJ) dígitos');
                }
            }
        }
    }

    // Limpar erros visuais
    function limparErros() {
        document.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500');
            el.classList.add('border-gray-300');
        });
        document.querySelectorAll('.error-message').forEach(el => el.remove());
    }

    // Event listeners simples
    if (form) {
        form.addEventListener('input', function() {
            limparErros();
            atualizarBotao();
        });
        
        form.addEventListener('change', function() {
            limparErros();
            atualizarBotao();
        });
        
        // Validação final antes do envio
        form.addEventListener('submit', function(e) {
            const doc = document.getElementById('cliente_documento');
            if (doc && doc.value.trim()) {
                const val = doc.value.replace(/\D/g, '');
                let documentoValido = false;
                
                if (val.length === 11) {
                    documentoValido = validarCPF(val);
                    if (!documentoValido) {
                        mostrarErro(doc, 'CPF inválido. Corrija antes de continuar.');
                    }
                } else if (val.length === 14) {
                    documentoValido = validarCNPJ(val);
                    if (!documentoValido) {
                        mostrarErro(doc, 'CNPJ inválido. Corrija antes de continuar.');
                    }
                } else {
                    mostrarErro(doc, 'Documento deve ter 11 (CPF) ou 14 (CNPJ) dígitos.');
                }
                
                if (!documentoValido) {
                    e.preventDefault();
                    alert('Documento inválido! Corrija o CPF/CNPJ antes de continuar.');
                    return false;
                }
            }
            
            if (!validarFormulario()) {
                e.preventDefault();
                alert('Preencha todos os campos obrigatórios corretamente.');
                return false;
            }
        });
    }

    // Event listeners individuais para cada campo (garantia extra)
    const todosCampos = [
        'cliente_nome', 'cliente_documento', 'cliente_endereco', 
        'cliente_cidade', 'cliente_uf', 'cliente_cep'
    ];
    
    todosCampos.forEach(id => {
        const campo = document.getElementById(id);
        if (campo) {
            campo.addEventListener('input', () => {
                limparErros();
                atualizarBotao();
            });
            campo.addEventListener('change', () => {
                limparErros();
                atualizarBotao();
            });
        }
    });

    // Event listeners para campos especiais
    const valor = document.querySelector('input[name="valor"]');
    const data = document.querySelector('input[name="dataVencimento"]');
    const instrucoes = document.getElementById('instrucoes');
    const seuNumero = document.querySelector('input[name="seuNumero"]');

    [valor, data, instrucoes, seuNumero].forEach(campo => {
        if (campo) {
            campo.addEventListener('input', () => {
                limparErros();
                atualizarBotao();
            });
            campo.addEventListener('change', () => {
                limparErros();
                atualizarBotao();
            });
        }
    });

    // Autocomplete básico
    if (input && autocompleteList) {
        input.addEventListener('input', function() {
            const q = this.value;
            if (q.length < 2) {
                autocompleteList.classList.add('hidden');
                return;
            }
            
            fetch(`/api/clientes-autocomplete?q=${encodeURIComponent(q)}`)
                .then(res => res.json())
                .then(data => {
                    clientes = data;
                    if (clientes && clientes.length > 0) {
                        autocompleteList.innerHTML = clientes.map((c, i) => `
                            <div class="px-3 py-2 cursor-pointer hover:bg-green-100" data-index="${i}">
                                <span class="font-semibold">${c.name}</span><br>
                                <span class="text-xs text-gray-500">${c.cpf_cnpj || ''}</span>
                            </div>
                        `).join('');
                        autocompleteList.classList.remove('hidden');
                    }
                })
                .catch(err => console.error('Erro autocomplete:', err));
        });

        autocompleteList.addEventListener('click', function(e) {
            const item = e.target.closest('[data-index]');
            if (!item) return;
            
            const idx = parseInt(item.getAttribute('data-index'));
            const c = clientes[idx];
            
            // Preencher os campos
            document.getElementById('cliente_nome').value = c.name || '';
            document.getElementById('cliente_documento').value = c.cpf_cnpj || '';
            document.getElementById('cliente_endereco').value = c.address || '';
            document.getElementById('cliente_cidade').value = c.city || '';
            document.getElementById('cliente_uf').value = c.state || '';
            document.getElementById('cliente_cep').value = c.postal_code || '';
            
            autocompleteList.classList.add('hidden');
            
            // Disparar eventos para cada campo preenchido
            ['cliente_nome', 'cliente_documento', 'cliente_endereco', 'cliente_cidade', 'cliente_uf', 'cliente_cep'].forEach(id => {
                const campo = document.getElementById(id);
                if (campo) {
                    campo.dispatchEvent(new Event('input', { bubbles: true }));
                    campo.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
            
            // Forçar atualização da validação
            setTimeout(() => {
                limparErros();
                atualizarBotao();
            }, 50);
        });
    }

    // Validação inicial
    atualizarBotao();
});
}
</script>
@show
