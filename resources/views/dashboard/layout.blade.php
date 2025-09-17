<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - BSEstoque</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200 fixed w-full z-30 top-0 left-0 shadow-sm h-16 flex items-center">
        <div class="flex justify-between items-center w-full max-w-full px-4">
            <div class="flex items-center gap-3">
                <!-- Botão do menu mobile -->
                <button id="sidebarToggle" class="md:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <!-- Logo na navbar -->
                <img src="/imagens/logo.png" alt="Logo" class="h-12 hidden md:block">
                <img src="/imagens/logo_fechado.png" alt="Logo Mobile" class="h-9 md:hidden">
            </div>
            <div class="flex items-center gap-4">
                <span class="text-gray-700 font-medium hidden sm:block">Olá, <span class="font-semibold">{{ Auth::user()->name }}</span></span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-800 text-white rounded hover:bg-gray-900 transition text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1"/></svg>
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Overlay para mobile -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden hidden transition-opacity duration-300 opacity-0" onclick="toggleSidebar()"></div>

    <!-- Sidebar fixa -->
    <aside id="sidebar" class="fixed left-0 z-50 md:z-20 bg-white border-r border-gray-200 flex-col transition-transform duration-300 ease-in-out transform -translate-x-full md:translate-x-0 shadow-lg md:shadow-none top-0 h-full md:top-16 md:h-[calc(100vh-4rem)] w-64">
        <nav class="flex-1 px-4 py-6 space-y-1 h-full max-h-[calc(100vh-4rem)] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
            <a href="/dashboard" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"/></svg>
                Dashboard
            </a>

            <!-- Seção de Estoque -->
           
            <div class="pt-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Estoque</h3>
               
                <a href="/products" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('products.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4"/></svg>
                    Produtos
                </a>
               
                <a href="/categories" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('categories.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Categorias
                </a>
                
                <a href="/stock_movements" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('stock_movements.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1"/></svg>
                    Movimentações
                </a>
               
                <a href="/suppliers" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('suppliers.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Fornecedores
                </a>
                

                <!-- Clientes -->
                <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('customers.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    Clientes
                </a>
                <a href="{{ route('sellers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('sellers.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-user-tie w-5 h-5 text-indigo-500"></i>
                    Vendedores
                </a>
                @if(Auth::id() === 12 || Auth::id() === 1)
                <!-- Romaneios -->
                <a href="{{ route('delivery_receipts.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('delivery_receipts.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                    Romaneios
                </a>
                @endif
            </div>
            

            <!-- Seção Financeira -->
            @canModule('finance')
            <div class="pt-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Financeiro</h3>
                @can('view-payables')
                <a href="/payables" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('payables.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    Contas a Pagar
                </a>
                @endcan
                @can('view-receivables')
                <a href="/receivables" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('receivables.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    Contas a Receber
                </a>
                @if(Auth::id() === 12 || Auth::id() === 1)
                    <a href="{{ route('nfe.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('nfe.*') ? 'bg-blue-100 text-blue-900 font-bold' : 'text-gray-700 hover:bg-blue-50' }}">
                        <i class="fas fa-receipt w-5 h-5 text-blue-500"></i>
                        Nota Fiscal
                    </a>
                    <a href="/venda-boleto" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->is('venda-boleto*') ? 'bg-green-100 text-green-900 font-bold' : 'text-gray-700 hover:bg-green-50' }}">
                        <i class="fas fa-money-check-alt w-5 h-5 text-green-500"></i>
                        Venda por Boleto
                    </a>
                    <a href="/pesquisar-boletos" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->is('pesquisar-boletos*') ? 'bg-green-100 text-green-900 font-bold' : 'text-gray-700 hover:bg-green-50' }}">
                        <i class="fas fa-search w-5 h-5 text-green-500"></i>
                        Pesquisar Boletos
                    </a>
                @endif
                @endcan
                @can('financial-reports')
                <a href="/financial-reports" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('financial-reports.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Relatórios Financeiros
                </a>
                @endcan
            </div>
            @endcanModule

            <!-- Seção Administrativa -->
            <div class="pt-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Administrativo</h3>
                @can('view-employees')
                <a href="/employees" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('employees.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Funcionários
                </a>
                @endcan
                @can('view-reports')
                <a href="/reports" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('reports.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17v-2a4 4 0 014-4h10a4 4 0 014 4v2M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75"/></svg>
                    Relatórios de Estoque
                </a>
                @endcan
                <a href="/sales-reports" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('sales-reports.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    Relatório de Vendas
                </a>
                <a href="/stock-control-reports" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('stock-control-reports.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4"/></svg>
                    Controle de Estoque
                </a>
                @can('manage-roles')
                <a href="/roles" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('roles.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Papéis e Permissões
                </a>
                @endcan
                @can('view-users')
                <a href="/users" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('users.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
                    Usuários
                </a>
                @endcan
                @if(Auth::id() === 1)
                <a href="/admin/companies" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->is('admin/companies*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Empresas do Sistema
                </a>
                @endif
            </div>
            <!-- Submenu RH / Departamento Pessoal -->
            @canModule('hr')
            <button type="button" id="rhMenuBtn" class="flex items-center w-full gap-3 px-3 py-2 rounded-lg font-medium transition text-gray-700 hover:bg-gray-100 focus:outline-none" onclick="toggleRHMenu()">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-7a4 4 0 11-8 0 4 4 0 018 0zm6 4a4 4 0 10-8 0 4 4 0 008 0z" />
                </svg>
                RH / Departamento Pessoal
                <svg id="rhMenuChevron" class="w-4 h-4 ml-auto transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="rhSubMenu" class="space-y-1 pl-8 py-1 hidden">
                @can('time-clock')
                <a href="/timeclocks" class="flex items-center gap-2 px-2 py-1 rounded font-medium transition text-sm {{ request()->routeIs('timeclocks.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                    Ponto
                </a>
                @endcan
                @can('payroll')
                <a href="/payrolls" class="flex items-center gap-2 px-2 py-1 rounded font-medium transition text-sm {{ request()->routeIs('payrolls.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    Folha de Pagamento
                </a>
                @endcan
                @can('vacations')
                <a href="/vacations" class="flex items-center gap-2 px-2 py-1 rounded font-medium transition text-sm {{ request()->routeIs('vacations.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 17l4 4 4-4m-4-5v9"/></svg>
                    Férias
                </a>
                @endcan
                @can('leaves')
                <a href="/leaves" class="flex items-center gap-2 px-2 py-1 rounded font-medium transition text-sm {{ request()->routeIs('leaves.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                    Licenças
                </a>
                @endcan
                @can('benefits')
                <a href="/benefits" class="flex items-center gap-2 px-2 py-1 rounded font-medium transition text-sm {{ request()->routeIs('benefits.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    Benefícios
                </a>
                @endcan
                <a href="/payslips" class="flex items-center gap-2 px-2 py-1 rounded font-medium transition text-sm {{ request()->routeIs('payslips.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                    Holerites
                </a>
            </div>
            @endcanModule

            <!-- Seção Frente de Caixa -->
            <div class="pt-4">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Frente de Caixa</h3>
                <a href="/caixa" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('caixa.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg>
                    Caixa
                </a>
                <a href="/pdv/full" target="_blank" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('pdv.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M6 10h.01M18 10h.01"/></svg>
                    PDV
                </a>
                <a href="/quotes" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition {{ request()->routeIs('quotes.*') ? 'bg-gray-100 text-gray-900 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Orçamentos
                </a>
            </div>

        </div>
    </aside>

    <!-- Conteúdo principal -->
    <div class="md:pl-64 pt-16">
        <main class="p-4 md:p-8 bg-gray-50 min-h-screen">
            @yield('content')
        </main>
    </div>

    <script>
        // Função para alternar a sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (sidebar.classList.contains('-translate-x-full')) {
                // Abrir sidebar
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    overlay.classList.add('opacity-100');
                }, 10);
                document.body.style.overflow = 'hidden';
            } else {
                // Fechar sidebar
                sidebar.classList.add('-translate-x-full');
                overlay.classList.remove('opacity-100');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300);
                document.body.style.overflow = 'auto';
            }
        }

        // Event listener para o botão do menu
        document.getElementById('sidebarToggle').addEventListener('click', toggleSidebar);

        // Fechar sidebar ao clicar em um link (mobile)
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    setTimeout(() => {
                        toggleSidebar();
                    }, 100);
                }
            });
        });

        // Fechar sidebar ao redimensionar a tela para desktop
        window.addEventListener('resize', () => {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (window.innerWidth >= 768) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
                overlay.classList.remove('opacity-100');
                document.body.style.overflow = 'auto';
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });

        // Fechar sidebar ao pressionar ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('sidebar');
                if (!sidebar.classList.contains('-translate-x-full')) {
                    toggleSidebar();
                }
            }
        });

        function toggleRHMenu() {
            const submenu = document.getElementById('rhSubMenu');
            const chevron = document.getElementById('rhMenuChevron');
            submenu.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }
        // Abrir automaticamente se estiver em rota de RH
        if ([
            'timeclocks.*','payrolls.*','vacations.*','leaves.*','benefits.*','payslips.*'
        ].some(r => window.location.pathname.includes(r.split('.')[0]))) {
            document.getElementById('rhSubMenu').classList.remove('hidden');
            document.getElementById('rhMenuChevron').classList.add('rotate-180');
        }
    </script>
    @yield('scripts')
</body>
</html>
