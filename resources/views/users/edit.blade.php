@extends('dashboard.layout')

@section('title', 'Editar Usuário')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Editar Usuário</h1>
        <p class="text-gray-600 mt-1">Modifique as informações e papéis deste usuário.</p>
    </div>

    <div class="bg-white rounded-lg shadow-md border border-gray-200">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações do Usuário</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome Completo *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Digite o nome completo" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="usuario@empresa.com" required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-md font-medium text-gray-900 mb-4">Alterar Senha (Opcional)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Nova Senha</label>
                                <input type="password" name="password" id="password"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Deixe em branco para manter a atual">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nova Senha</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Confirme a nova senha">
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Deixe os campos de senha em branco se não quiser alterá-la.</p>
                    </div>
                </div>
            </div>

            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Papéis e Permissões</h2>
                <p class="text-sm text-gray-600 mb-6">Selecione os papéis que este usuário terá:</p>

                @if($roles->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($roles as $role)
                            <label class="flex items-start space-x-3 cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                       class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                       {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900">{{ $role->name }}</p>
                                        @if($role->is_active)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Ativo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Inativo
                                            </span>
                                        @endif
                                    </div>
                                    @if($role->description)
                                        <p class="text-xs text-gray-500 mt-1">{{ $role->description }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1">{{ $role->permissions->count() }} permissões</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum papel disponível</h3>
                        <p class="mt-1 text-sm text-gray-500">Crie papéis primeiro para poder atribuí-los aos usuários.</p>
                        <div class="mt-4">
                            <a href="{{ route('roles.create') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200">
                                Criar Papel
                            </a>
                        </div>
                    </div>
                @endif

                @error('roles')
                    <p class="mt-4 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                <div class="flex justify-between items-center">
                    <a href="{{ route('users.index') }}"
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
                        Atualizar Usuário
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
