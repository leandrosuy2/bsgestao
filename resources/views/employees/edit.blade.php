@extends('dashboard.layout')

@section('content')
<div class="flex items-center gap-3 mb-8">
    <div class="flex items-center gap-2">
        <a href="{{ route('employees.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar para Funcionários
        </a>
        <span class="text-gray-400">/</span>
        <span class="text-gray-700">Editar Funcionário</span>
    </div>
</div>

<div class="flex items-center gap-3 mb-8">
    <span class="bg-blue-600 p-2 rounded-full shadow">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2 2l-6 6m-2 2h6"/>
        </svg>
    </span>
    <h1 class="text-2xl font-bold text-gray-800">Editar Funcionário: {{ $employee->name }}</h1>
</div>

@if($errors->any())
    <div class="mb-4 px-4 py-3 bg-red-100 border border-red-200 text-red-700 rounded shadow-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm p-6">
    <form method="POST" action="{{ route('employees.update', $employee) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nome Completo -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome Completo *</label>
                <input type="text" name="name" id="name" 
                       value="{{ old('name', $employee->name) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm" required>
            </div>

            <!-- CPF -->
            <div>
                <label for="cpf" class="block text-sm font-medium text-gray-700 mb-2">CPF *</label>
                <input type="text" name="cpf" id="cpf" 
                       value="{{ old('cpf', $employee->cpf) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm" 
                       placeholder="000.000.000-00" required>
            </div>

            <!-- E-mail -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-mail *</label>
                <input type="email" name="email" id="email" 
                       value="{{ old('email', $employee->email) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm" required>
            </div>

            <!-- Telefone -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                <input type="text" name="phone" id="phone" 
                       value="{{ old('phone', $employee->phone) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm" 
                       placeholder="(00) 00000-0000">
            </div>

            <!-- Cargo -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Cargo *</label>
                <input type="text" name="role" id="role" 
                       value="{{ old('role', $employee->role) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm" 
                       placeholder="Ex: Vendedor, Gerente, etc." required>
            </div>

            <!-- Data de Admissão -->
            <div>
                <label for="admission_date" class="block text-sm font-medium text-gray-700 mb-2">Data de Admissão *</label>
                <input type="date" name="admission_date" id="admission_date" 
                       value="{{ old('admission_date', $employee->admission_date) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm" required>
            </div>

            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Nome de usuário *</label>
                <input type="text" name="username" id="username" 
                       value="{{ old('username', $employee->username) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm" 
                       placeholder="usuario.nome" required>
            </div>

            <!-- Nível de Permissão -->
            <div>
                <label for="permission_level" class="block text-sm font-medium text-gray-700 mb-2">Nível de Permissão *</label>
                <select name="permission_level" id="permission_level" 
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm" required>
                    <option value="">Selecione...</option>
                    <option value="administrador" {{ old('permission_level', $employee->permission_level) === 'administrador' ? 'selected' : '' }}>Administrador</option>
                    <option value="operador" {{ old('permission_level', $employee->permission_level) === 'operador' ? 'selected' : '' }}>Operador</option>
                    <option value="consulta" {{ old('permission_level', $employee->permission_level) === 'consulta' ? 'selected' : '' }}>Consulta</option>
                </select>
            </div>
        </div>

        <div class="border-t pt-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Segurança</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nova Senha -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Nova Senha</label>
                    <input type="password" name="password" id="password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm" 
                           placeholder="Deixe em branco para manter a atual">
                    <p class="mt-1 text-xs text-gray-500">Mínimo de 6 caracteres. Deixe em branco para não alterar.</p>
                </div>

                <!-- Confirmar Senha -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nova Senha</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" 
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm" 
                           placeholder="Confirme a nova senha">
                </div>
            </div>
        </div>

        <div class="border-t pt-6">
            <!-- Status Ativo -->
            <div class="flex items-center">
                <input type="checkbox" name="active" id="active" value="1" 
                       {{ old('active', $employee->active) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="active" class="ml-2 block text-sm text-gray-700">Funcionário ativo</label>
            </div>
        </div>

        <!-- Botões -->
        <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t">
            <button type="submit" 
                    class="flex-1 bg-gradient-to-r from-blue-600 to-blue-500 text-white px-6 py-3 rounded-lg hover:from-blue-700 hover:to-blue-600 transition font-semibold shadow-lg ring-1 ring-blue-600/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600">
                Salvar Alterações
            </button>
            <a href="{{ route('employees.index') }}" 
               class="flex-1 text-center px-6 py-3 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition font-medium border">
                Cancelar
            </a>
        </div>
    </form>
</div>

<script>
    // Máscara para CPF
    document.getElementById('cpf').addEventListener('input', function(e) {
        let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,3})(\d{0,2})/);
        e.target.value = !x[2] ? x[1] : '' + x[1] + '.' + x[2] + '.' + x[3] + (x[4] ? '-' + x[4] : '');
    });

    // Máscara para telefone
    document.getElementById('phone').addEventListener('input', function(e) {
        let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
        e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
    });
</script>
@endsection
