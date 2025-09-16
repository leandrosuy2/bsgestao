@extends('dashboard.layout')
@section('title', 'Boletos Sicredi')
@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-barcode text-green-500"></i> Boletos Sicredi</h1>
            <p class="text-gray-600">Crie e consulte boletos Sicredi de forma fácil e rápida.</p>
        </div>
        <div class="text-sm text-gray-500">
            Última atualização: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card: Criar Boleto -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-plus-circle text-green-500"></i> Criar Boleto</h2>
            <form method="POST" action="{{ route('boletos.sicredi.create') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-1">Usuário</label>
                    <select name="user_id" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1">Valor</label>
                        <input type="number" step="0.01" name="valor" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Data de Vencimento</label>
                        <input type="date" name="dataVencimento" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm mb-1">Seu Número</label>
                    <input type="text" name="seuNumero" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1">Nome do Pagador</label>
                        <input type="text" name="pagador_nome" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Documento do Pagador</label>
                        <input type="text" name="pagador_documento" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm mb-1">Tipo Pessoa Pagador</label>
                        <select name="pagador_tipoPessoa" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900">
                            <option value="PESSOA_FISICA">Pessoa Física</option>
                            <option value="PESSOA_JURIDICA">Pessoa Jurídica</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">CEP Pagador</label>
                        <input type="text" name="pagador_cep" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Cidade Pagador</label>
                        <input type="text" name="pagador_cidade" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1">Endereço Pagador</label>
                        <input type="text" name="pagador_endereco" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">UF Pagador</label>
                        <input type="text" name="pagador_uf" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white font-bold transition">Criar Boleto</button>
                </div>
            </form>
        </div>
        <!-- Card: Consultar Boleto -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-search text-blue-500"></i> Consultar Boleto</h2>
            <form method="GET" action="{{ route('boletos.sicredi.consultar') }}" class="space-y-4">
                <div>
                    <label class="block text-sm mb-1">Usuário</label>
                    <select name="user_id" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Nosso Número</label>
                    <input type="text" name="nosso_numero" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" placeholder="Digite o Nosso Número do boleto">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white font-bold transition">Consultar Boleto</button>
                </div>
            </form>
        </div>
    </div>

    @if(session('boleto_result'))
        <div class="bg-blue-700 text-white p-4 rounded-lg mb-4">
            <pre class="whitespace-pre-wrap">{{ session('boleto_result') }}</pre>
        </div>
    @endif
</div>
@endsection
