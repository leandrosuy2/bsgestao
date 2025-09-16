@extends('logs.admin-panel')

@section('content')
<div class="container mt-4">
    <h2 class="text-2xl font-bold mb-4 text-red-600">Painel de Permissões do SUPERADMIN</h2>
    <div class="row">
        <div class="col-md-4">
            <h4 class="font-semibold">Papéis</h4>
            <ul class="list-group mb-4">
                @foreach($roles as $role)
                    <li class="list-group-item">{{ $role->name }}</li>
                @endforeach
            </ul>
        </div>
        <div class="col-md-4">
            <h4 class="font-semibold">Permissões</h4>
            <ul class="list-group mb-4">
                @foreach($permissions as $permission)
                    <li class="list-group-item">{{ $permission->name ?? $permission->id }}</li>
                @endforeach
            </ul>
        </div>
        <div class="col-md-4">
            <h4 class="font-semibold">Usuários</h4>
            <ul class="list-group mb-4">
                @foreach($users as $user)
                    <li class="list-group-item">{{ $user->name }} <small>({{ $user->email }})</small></li>
                @endforeach
            </ul>
        </div>
    </div>
    <hr>
    <p class="text-muted">Aqui o SUPERADMIN pode gerenciar quem pode fazer o quê no sistema. Para editar permissões, selecione um usuário ou papel acima.</p>
</div>
@endsection
