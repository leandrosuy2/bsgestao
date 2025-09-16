<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BSEstoque</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-10 border border-gray-200">
        <div class="flex flex-col items-center mb-8">
                <!-- Logo no topo do login -->
    <div class="flex justify-center mt-10 mb-8">
        <img src="/imagens/logo.png" alt="Logo" class="h-16 hidden md:block">
        <img src="/imagens/logo_fechado.png" alt="Logo Mobile" class="h-10 md:hidden">
    </div>
            <p class="text-gray-500 text-sm">Acesso ao sistema de gestão de estoque</p>
        </div>
        @if(session('error'))
            <div class="text-red-600 text-center mb-4 text-sm font-medium">{{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-gray-700 mb-1 font-medium">E-mail</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#6b7280" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-.659 1.591l-7.091 7.091a2.25 2.25 0 01-3.182 0L3.409 8.584A2.25 2.25 0 012.75 6.993V6.75" />
                        </svg>
                    </span>
                    <input type="email" id="email" name="email" required autofocus class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 placeholder-gray-400" placeholder="Digite seu e-mail" />
                </div>
            </div>
            <div>
                <label for="password" class="block text-gray-700 mb-1 font-medium">Senha</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#6b7280" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7.125a4.125 4.125 0 10-8.25 0V10.5m12 0a1.5 1.5 0 01-1.5 1.5h-15a1.5 1.5 0 01-1.5-1.5m18 0v7.125A2.625 2.625 0 0118.375 20.25H5.625A2.625 2.625 0 013 17.625V10.5m18 0H3" />
                        </svg>
                    </span>
                    <input type="password" id="password" name="password" required class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 placeholder-gray-400" placeholder="Digite sua senha" />
                </div>
            </div>
            <button type="submit" class="w-full py-2 px-4 bg-gray-800 hover:bg-gray-900 text-white font-semibold rounded-lg shadow-sm transition">Entrar</button>
        </form>
    </div>
    <footer class="w-full flex justify-center absolute bottom-4 left-0">
        <div class="flex items-center text-gray-400 text-sm">
            <span>Feito com</span>
            <svg class="mx-1 w-4 h-4 text-red-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/></svg>
            <span>por <span class="font-semibold text-gray-600">Belém Sistemas</span></span>
        </div>
    </footer>
</body>
</html>
