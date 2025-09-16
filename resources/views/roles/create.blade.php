@extends('dashboard.layout')

@section('title', 'Criar Novo Papel')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Criar Novo Papel</h1>
        <p class="text-gray-600 mt-1">Defina as permissões que este papel terá no sistema.</p>
    </div>

    <div class="bg-white rounded-lg shadow-md border border-gray-200">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf

            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações do Papel</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome do Papel *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Ex: Estoquista, Financeiro, Administrativo" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Descreva as responsabilidades deste papel">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Permissões</h2>
                <p class="text-sm text-gray-600 mb-6">Selecione as permissões que este papel terá acesso:</p>

                <div class="space-y-6">
                    @foreach($permissions as $module => $modulePermissions)
                        <div class="border border-gray-200 rounded-lg">
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">
                                    {{ ucfirst($module) }}
                                </h3>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($modulePermissions as $permission)
                                        <label class="flex items-start space-x-3 cursor-pointer">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                   class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                   {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">{{ $permission->name }}</p>
                                                @if($permission->description)
                                                    <p class="text-xs text-gray-500 mt-1">{{ $permission->description }}</p>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('permissions')
                    <p class="mt-4 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                <div class="flex justify-between items-center">
                    <a href="{{ route('roles.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Cancelar
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Criar Papel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Selecionar/deselecionar todas as permissões de um módulo
    const moduleHeaders = document.querySelectorAll('.bg-gray-50');

    moduleHeaders.forEach(header => {
        const moduleDiv = header.parentElement;
        const checkboxes = moduleDiv.querySelectorAll('input[type="checkbox"]');

        // Adicionar checkbox "Selecionar Todos" no cabeçalho
        const selectAllCheckbox = document.createElement('input');
        selectAllCheckbox.type = 'checkbox';
        selectAllCheckbox.className = 'h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mr-2';

        const selectAllLabel = document.createElement('span');
        selectAllLabel.className = 'text-xs text-gray-500';
        selectAllLabel.textContent = 'Selecionar Todos';

        header.appendChild(selectAllCheckbox);
        header.appendChild(selectAllLabel);

        // Funcionalidade de selecionar todos
        selectAllCheckbox.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Atualizar checkbox "Selecionar Todos" baseado nos checkboxes individuais
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                const someChecked = Array.from(checkboxes).some(cb => cb.checked);

                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            });
        });
    });
});
</script>
@endsection
