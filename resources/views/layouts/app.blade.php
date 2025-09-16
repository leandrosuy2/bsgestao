<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Painel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.tailwindcss.com" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <!-- Topbar -->
    <header class="bg-gray-900 shadow-lg border-b border-gray-800 sticky top-0 z-30">
        <div class="container mx-auto flex items-center justify-between px-6 py-4">
            <div class="flex items-center gap-3">
                <img src="/favicon.ico" alt="Logo" class="w-8 h-8 rounded shadow">
                <span class="text-green-400 font-extrabold text-2xl tracking-tight">Painel</span>
            </div>
            <nav class="flex gap-4">
                <a href="/dashboard" class="flex items-center gap-2 px-3 py-2 rounded-lg font-medium transition {{ request()->is('dashboard') ? 'bg-green-600 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-green-400' }}"><i class="fas fa-home"></i> Dashboard</a>
                <a href="/boletos/sicredi" class="flex items-center gap-2 px-3 py-2 rounded-lg font-medium transition {{ request()->is('boletos/sicredi*') ? 'bg-green-600 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-green-400' }}"><i class="fas fa-barcode"></i> Boletos Sicredi</a>
                <a href="/logs/sicredi-integrations" class="flex items-center gap-2 px-3 py-2 rounded-lg font-medium transition {{ request()->is('logs/sicredi-integrations*') ? 'bg-green-600 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-green-400' }}"><i class="fas fa-link"></i> Integração Sicredi</a>
                <a href="/admin/permissions" class="flex items-center gap-2 px-3 py-2 rounded-lg font-medium transition {{ request()->is('admin/permissions*') ? 'bg-green-600 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-green-400' }}"><i class="fas fa-user-shield"></i> Permissões</a>
                <a href="/venda-boleto" class="flex items-center gap-2 px-3 py-2 rounded-lg font-medium transition {{ request()->is('venda-boleto*') ? 'bg-green-600 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-green-400' }}"><i class="fas fa-money-check-alt"></i> Venda por Boleto</a>
                <a href="/pdv/full" class="flex items-center gap-2 px-3 py-2 rounded-lg font-medium transition {{ request()->is('pdv*') ? 'bg-green-600 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-green-400' }}"><i class="fas fa-cash-register"></i> PDV</a>
                <a href="/quotes" class="flex items-center gap-2 px-3 py-2 rounded-lg font-medium transition {{ request()->is('quotes*') ? 'bg-green-600 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-green-400' }}"><i class="fas fa-file-invoice"></i> Orçamentos</a>
                <a href="/customers" class="flex items-center gap-2 px-3 py-2 rounded-lg font-medium transition {{ request()->is('customers*') ? 'bg-green-600 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-green-400' }}"><i class="fas fa-users"></i> Clientes</a>
                <a href="/payables" class="flex items-center gap-2 px-3 py-2 rounded-lg font-medium transition {{ request()->is('payables*') ? 'bg-green-600 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-green-400' }}"><i class="fas fa-arrow-up"></i> Contas a Pagarrrr</a>
                <a href="/receivables" class="flex items-center gap-2 px-3 py-2 rounded-lg font-medium transition {{ request()->is('receivables*') ? 'bg-green-600 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-green-400' }}"><i class="fas fa-arrow-down"></i> Contas a Receber</a>
                <a href="/venda-boleto" class="flex items-center gap-2 px-3 py-2 rounded-lg font-medium transition {{ request()->is('venda-boleto*') ? 'bg-green-600 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-green-400' }}"><i class="fas fa-money-check-alt"></i> Venda por Boleto</a>
                <div class="border-t border-gray-700 my-2"></div>
                <a href="/products" class="flex items-center gap-2 px-3 py-2 rounded-lg font-medium transition {{ request()->is('products*') ? 'bg-green-600 text-white' : 'text-gray-200 hover:bg-gray-700 hover:text-green-400' }}"><i class="fas fa-box"></i> Produtos</a>
            </nav>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold transition"><i class="fas fa-sign-out-alt"></i> Sair</button>
            </form>
        </div>
    </header>
    <main class="container mx-auto px-4 py-8 min-h-screen">
        @yield('content')
    </main>
</body>
</html>
