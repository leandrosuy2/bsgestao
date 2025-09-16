@extends('dashboard.layout')

@section('title', 'Detalhes do Usuário')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                <p class="text-gray-600 mt-1">{{ $user->email }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('users.edit', $user) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('users.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informações do Usuário -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações do Usuário</h2>

                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0 h-16 w-16">
                        <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-xl font-medium text-gray-700">{{ substr($user->name, 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <div class="mt-1">
                            @if($user->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    Ativo
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    Inativo
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Papéis atribuídos</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->roles->count() }} papel(éis)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total de permissões</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->getAllPermissions()->count() }} permissão(ões)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Membro desde</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Última atualização</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Papéis e Permissões -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Papéis e Permissões</h2>
                    <p class="text-sm text-gray-600 mt-1">Lista de papéis atribuídos e suas permissões</p>
                </div>

                <div class="p-6">
                    @if($user->roles->count() > 0)
                        <div class="space-y-6">
                            @foreach($user->roles as $role)
                                <div class="border border-gray-200 rounded-lg">
                                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-semibold text-gray-900">{{ $role->name }}</h3>
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
                                            <p class="text-xs text-gray-600 mt-1">{{ $role->description }}</p>
                                        @endif
                                    </div>
                                    <div class="p-4">
                                        @if($role->permissions->count() > 0)
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                @foreach($role->permissions->groupBy('module') as $module => $modulePermissions)
                                                    <div class="space-y-2">
                                                        <h4 class="text-xs font-medium text-gray-700 uppercase tracking-wide">{{ ucfirst($module) }}</h4>
                                                        <div class="space-y-1">
                                                            @foreach($modulePermissions as $permission)
                                                                <div class="flex items-center space-x-2">
                                                                    <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                    <span class="text-xs text-gray-600">{{ $permission->name }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500">Este papel não possui permissões definidas.</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum papel atribuído</h3>
                            <p class="mt-1 text-sm text-gray-500">Este usuário não possui papéis definidos.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo de Permissões -->
    @if($user->getAllPermissions()->count() > 0)
        <div class="mt-6">
            <div class="bg-white rounded-lg shadow-md border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Resumo de Permissões</h2>
                    <p class="text-sm text-gray-600 mt-1">Todas as permissões que este usuário possui através de seus papéis</p>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($user->getAllPermissions()->groupBy('module') as $module => $modulePermissions)
                            <div class="border border-gray-200 rounded-lg">
                                <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">
                                        {{ ucfirst($module) }}
                                    </h3>
                                </div>
                                <div class="p-3">
                                    <div class="space-y-1">
                                        @foreach($modulePermissions as $permission)
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="text-xs text-gray-600">{{ $permission->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
