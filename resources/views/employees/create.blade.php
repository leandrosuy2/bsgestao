@extends('dashboard.layout')

@section('content')
<div class="w-full max-w-screen-xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <a href="{{ route('employees.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>
        <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Novo Funcionário
        </h2>
    </div>

    {{-- Mensagens de alerta --}}
    @if(session('warning'))
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">Atenção:</span>
                <span class="ml-1">{{ session('warning') }}</span>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">Sucesso:</span>
                <span class="ml-1">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">Erro:</span>
            </div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('employees.store') }}" class="bg-white p-6 rounded-lg shadow-md">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Nome completo</label>
                <input type="text" name="name" placeholder="Nome completo" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('name', $suggestedData['name'] ?? '') }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">CPF</label>
                <input type="text" name="cpf" maxlength="14" placeholder="000.000.000-00" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('cpf') }}">
                <p class="text-xs text-gray-500 mt-1">Deve ser único para cada funcionário</p>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">E-mail</label>
                <input type="email" name="email" placeholder="email@exemplo.com" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('email', $suggestedData['email'] ?? '') }}">
                <p class="text-xs text-gray-500 mt-1">{{ isset($suggestedData['email']) ? 'Email sugerido com base na sua conta' : 'Deve ser único para cada funcionário' }}</p>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Telefone</label>
                <input type="text" name="phone" placeholder="(99) 99999-9999" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" value="{{ old('phone') }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Cargo/Função</label>
                <input type="text" name="role" placeholder="Cargo ou função" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('role', $suggestedData['role'] ?? '') }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Data de admissão</label>
                <input type="date" name="admission_date" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('admission_date', $suggestedData['admission_date'] ?? date('Y-m-d')) }}">
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Usuário</label>
                <input type="text" name="username" placeholder="Usuário de acesso" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required value="{{ old('username', $suggestedData['username'] ?? '') }}">
                <p class="text-xs text-gray-500 mt-1">Será gerado automaticamente baseado no nome</p>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Senha</label>
                <input type="password" name="password" placeholder="Senha de acesso" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Confirme a senha</label>
                <input type="password" name="password_confirmation" placeholder="Confirme a senha" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Permissão</label>
                <select name="permission_level" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
                    <option value="">Selecione</option>
                    <option value="administrador" @selected(old('permission_level', $suggestedData['permission_level'] ?? '') == 'administrador')>Administrador</option>
                    <option value="operador" @selected(old('permission_level', $suggestedData['permission_level'] ?? '') == 'operador')>Operador</option>
                    <option value="consulta" @selected(old('permission_level', $suggestedData['permission_level'] ?? '') == 'consulta')>Consulta</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-700 font-medium mb-1">Ativo</label>
                <select name="active" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-sm" required>
                    <option value="1" @selected(old('active', '1') == '1')>Sim</option>
                    <option value="0" @selected(old('active') == '0')>Não</option>
                </select>
            </div>
        </div>
        
        {{-- Informação sobre redirecionamento --}}
        @if(session('redirect_to'))
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg mt-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Informação:</span>
                    <span class="ml-1">Após cadastrar o funcionário, você será redirecionado automaticamente para continuar o processo anterior.</span>
                </div>
            </div>
        @endif
        
        <div class="flex justify-end mt-8 gap-3">
            @if(session('redirect_to'))
                @if(session('redirect_to') == 'payables.create')
                    <a href="{{ route('payables.index') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm border">Cancelar</a>
                @elseif(session('redirect_to') == 'payables.edit' && session('payable_id'))
                    <a href="{{ route('payables.index') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm border">Cancelar</a>
                @else
                    <a href="{{ route('employees.index') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm border">Cancelar</a>
                @endif
            @else
                <a href="{{ route('employees.index') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm border">Cancelar</a>
            @endif
            <button type="submit" class="px-6 py-2 rounded bg-green-600 text-white hover:bg-green-700 font-semibold text-sm shadow transition-colors">Salvar</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.querySelector('input[name="name"]');
    const usernameInput = document.querySelector('input[name="username"]');
    
    // Auto-gerar username baseado no nome
    nameInput.addEventListener('input', function() {
        if (!usernameInput.value || usernameInput.dataset.autoGenerated === 'true') {
            const name = this.value.toLowerCase()
                .replace(/[^a-z\s]/g, '') // Remove caracteres especiais
                .replace(/\s+/g, '.') // Substitui espaços por pontos
                .replace(/\.+/g, '.') // Remove pontos duplicados
                .replace(/^\.+|\.+$/g, ''); // Remove pontos do início e fim
            
            usernameInput.value = name;
            usernameInput.dataset.autoGenerated = 'true';
        }
    });
    
    // Marcar como manual se usuário editar username
    usernameInput.addEventListener('input', function() {
        this.dataset.autoGenerated = 'false';
    });
    
    // Alerta para campos únicos
    const uniqueFields = ['cpf', 'email', 'username'];
    uniqueFields.forEach(fieldName => {
        const field = document.querySelector(`input[name="${fieldName}"]`);
        if (field) {
            field.addEventListener('blur', function() {
                if (this.value) {
                    // Aqui você pode adicionar uma verificação AJAX se necessário
                    console.log(`Verificando unicidade de ${fieldName}: ${this.value}`);
                }
            });
        }
    });
});
</script>
@endsection
