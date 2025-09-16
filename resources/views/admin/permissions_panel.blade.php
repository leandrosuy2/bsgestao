@extends('logs.admin-panel')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-6 text-blue-600">Painel de Permissões do Admin</h2>
    @if(session('success'))
        <div class="bg-green-600 text-white p-4 rounded mb-4">{{ session('success') }}</div>
    @endif
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div>
            <h3 class="font-semibold mb-2">Usuários</h3>
            <ul class="list-group">
                @foreach($users as $user)
                    <li class="list-group-item mb-2">
                        <strong>{{ $user->name }}</strong> <br>
                        <span class="text-xs text-gray-400">{{ $user->email }}</span>
                        <form method="POST" action="{{ route('admin.permissions.assignRole') }}" class="mt-2 flex gap-2">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <select name="role_id" class="rounded px-2 py-1">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" @if($user->hasRole($role->name)) selected @endif>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-blue-600 text-white px-2 py-1 rounded">Atribuir</button>
                        </form>
                        <form method="POST" action="{{ route('admin.permissions.removeRole') }}" class="mt-1 flex gap-2">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <select name="role_id" class="rounded px-2 py-1">
                                @foreach($user->roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded">Remover</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>
        <div>
            <h3 class="font-semibold mb-2">Papéis</h3>
            <ul class="list-group">
                @foreach($roles as $role)
                    <li class="list-group-item mb-2">
                        <strong>{{ $role->name }}</strong>
                        <form method="POST" action="{{ route('admin.permissions.assignPermission') }}" class="mt-2 flex gap-2">
                            @csrf
                            <input type="hidden" name="role_id" value="{{ $role->id }}">
                            <select name="permission_id" class="rounded px-2 py-1">
                                @foreach($permissions as $permission)
                                    <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-blue-600 text-white px-2 py-1 rounded">Atribuir</button>
                        </form>
                        <form method="POST" action="{{ route('admin.permissions.removePermission') }}" class="mt-1 flex gap-2">
                            @csrf
                            <input type="hidden" name="role_id" value="{{ $role->id }}">
                            <select name="permission_id" class="rounded px-2 py-1">
                                @foreach($role->permissions as $permission)
                                    <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded">Remover</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>
        <div>
            <h3 class="font-semibold mb-2">Permissões</h3>
            <ul class="list-group">
                @foreach($permissions as $permission)
                    <li class="list-group-item mb-2">
                        <strong>{{ $permission->name }}</strong>
                        <span class="text-xs text-gray-400">{{ $permission->slug }}</span>
                        <div class="mt-1">
                            <span class="text-xs">Papéis:</span>
                            @foreach($roles as $role)
                                @if($role->hasPermission($permission->slug))
                                    <span class="bg-blue-600 text-white px-2 py-1 rounded text-xs mr-1">{{ $role->name }}</span>
                                @endif
                            @endforeach
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
