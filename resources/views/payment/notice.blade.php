@extends('dashboard.layout')

@section('title', 'Acesso Bloqueado')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh]">
    <div class="bg-white p-8 rounded-xl shadow-md border border-gray-200 max-w-lg text-center">
        <h1 class="text-2xl font-bold text-red-600 mb-4">Acesso Bloqueado</h1>
        <p class="text-gray-700 mb-4">
            O período de teste gratuito da sua empresa expirou.<br>
            Para continuar utilizando o sistema, realize o pagamento da assinatura.
        </p>
        <div class="mb-6">
            <span class="inline-block bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-semibold">Entre em contato com o administrador para liberar o acesso</span>
        </div>
        <a href="mailto:admin@seudominio.com" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">Falar com o Administrador</a>
    </div>
</div>
@endsection
