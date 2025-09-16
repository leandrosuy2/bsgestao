<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Reader - Ambiente Geral do Projeto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <!-- Header -->
    <header class="bg-gray-800 border-b border-gray-700 px-6 py-4 sticky top-0 z-10">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('logs.reader') }}" class="text-blue-400 hover:text-blue-300 mr-4">
                    <i class="fas fa-arrow-left mr-1"></i>Voltar
                </a>
                <i class="fas fa-cogs text-2xl text-purple-400 mr-3"></i>
                <h1 class="text-xl font-bold">Ambiente Geral do Projeto</h1>
                <span class="ml-4 px-3 py-1 bg-purple-600 text-xs rounded-full">
                    <i class="fas fa-server mr-1"></i>Sistema Completo
                </span>
            </div>
            <div class="flex items-center space-x-4">
                <button onclick="refreshData()" class="bg-green-600 hover:bg-green-700 px-3 py-1 rounded text-sm transition">
                    <i class="fas fa-sync mr-1"></i>Atualizar
                </button>
                <span class="text-sm text-gray-400">
                    <i class="fas fa-clock mr-1"></i>{{ date('d/m/Y H:i:s') }}
                </span>
                <a href="{{ route('logs.logout') }}" class="text-red-400 hover:text-red-300 transition">
                    <i class="fas fa-sign-out-alt mr-1"></i>Sair
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-6 py-8">
        <!-- Status dos Serviços -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            @foreach($services as $service => $status)
                <div class="bg-gray-800 p-4 rounded-lg border border-gray-700 text-center">
                    <div class="flex items-center justify-center mb-2">
                        <div class="w-3 h-3 rounded-full bg-{{ $status['color'] }}-500 mr-2"></div>
                        <i class="fas fa-{{ $service == 'database' ? 'database' : ($service == 'cache' ? 'memory' : ($service == 'session' ? 'user-clock' : ($service == 'storage' ? 'hdd' : 'file-alt'))) }} text-lg"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-300 uppercase mb-1">{{ ucfirst($service) }}</h3>
                    <p class="text-{{ $status['color'] }}-400 font-bold text-sm">{{ $status['status'] }}</p>
                    @if(isset($status['message']))
                        <p class="text-xs text-red-400 mt-1">{{ Str::limit($status['message'], 30) }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Estatísticas Principais -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Usuários Total</p>
                        <p class="text-3xl font-bold text-blue-400">{{ number_format($stats['total_users']) }}</p>
                    </div>
                    <i class="fas fa-users text-4xl text-blue-400 opacity-20"></i>
                </div>
            </div>
            
            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Vendas Total</p>
                        <p class="text-3xl font-bold text-green-400">{{ number_format($stats['total_sales']) }}</p>
                        <p class="text-xs text-gray-500">Hoje: {{ number_format($stats['sales_today']) }}</p>
                    </div>
                    <i class="fas fa-shopping-cart text-4xl text-green-400 opacity-20"></i>
                </div>
            </div>
            
            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Produtos</p>
                        <p class="text-3xl font-bold text-yellow-400">{{ number_format($stats['total_products']) }}</p>
                    </div>
                    <i class="fas fa-box text-4xl text-yellow-400 opacity-20"></i>
                </div>
            </div>
            
            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Receita Mensal</p>
                        <p class="text-2xl font-bold text-purple-400">R$ {{ number_format($stats['revenue_this_month'], 2, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">Hoje: R$ {{ number_format($stats['revenue_today'], 2, ',', '.') }}</p>
                    </div>
                    <i class="fas fa-dollar-sign text-4xl text-purple-400 opacity-20"></i>
                </div>
            </div>
        </div>

        <!-- Informações Detalhadas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Informações do Sistema -->
            <div class="bg-gray-800 rounded-lg border border-gray-700">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h2 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-server mr-2 text-blue-400"></i>Informações do Sistema
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400">PHP Version:</span>
                        <span class="font-mono text-green-400">{{ $systemInfo['php_version'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Laravel Version:</span>
                        <span class="font-mono text-red-400">{{ $systemInfo['laravel_version'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Environment:</span>
                        <span class="font-mono text-yellow-400">{{ $systemInfo['app_env'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Debug Mode:</span>
                        <span class="font-mono {{ $systemInfo['debug_mode'] == 'Ativo' ? 'text-red-400' : 'text-green-400' }}">{{ $systemInfo['debug_mode'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Memory Limit:</span>
                        <span class="font-mono text-blue-400">{{ $systemInfo['memory_limit'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Max Execution Time:</span>
                        <span class="font-mono text-blue-400">{{ $systemInfo['max_execution_time'] }}s</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Upload Max Size:</span>
                        <span class="font-mono text-blue-400">{{ $systemInfo['upload_max_filesize'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Timezone:</span>
                        <span class="font-mono text-purple-400">{{ $systemInfo['timezone'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Informações do Banco de Dados -->
            <div class="bg-gray-800 rounded-lg border border-gray-700">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h2 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-database mr-2 text-green-400"></i>Banco de Dados
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    @if(isset($databaseInfo['status']) && $databaseInfo['status'] == 'Conectado')
                        <div class="flex justify-between">
                            <span class="text-gray-400">Status:</span>
                            <span class="font-mono text-green-400">{{ $databaseInfo['status'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Driver:</span>
                            <span class="font-mono text-blue-400">{{ $databaseInfo['driver'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Host:</span>
                            <span class="font-mono text-yellow-400">{{ $databaseInfo['host'] }}:{{ $databaseInfo['port'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Database:</span>
                            <span class="font-mono text-purple-400">{{ $databaseInfo['database'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Charset:</span>
                            <span class="font-mono text-gray-300">{{ $databaseInfo['charset'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Version:</span>
                            <span class="font-mono text-green-400">{{ $databaseInfo['version'] }}</span>
                        </div>
                    @else
                        <div class="bg-red-900 bg-opacity-20 p-4 rounded border border-red-700">
                            <p class="text-red-400 font-semibold">Erro de Conexão</p>
                            @if(isset($databaseInfo['error']))
                                <p class="text-red-300 text-sm mt-2">{{ $databaseInfo['error'] }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Configurações -->
            <div class="bg-gray-800 rounded-lg border border-gray-700">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h2 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-cog mr-2 text-yellow-400"></i>Configurações
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    @foreach($configs as $key => $value)
                        <div class="flex justify-between">
                            <span class="text-gray-400">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
                            <span class="font-mono text-blue-400">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Informações do Projeto -->
            <div class="bg-gray-800 rounded-lg border border-gray-700">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h2 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-code mr-2 text-purple-400"></i>Estrutura do Projeto
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Tamanho Total:</span>
                        <span class="font-mono text-blue-400">{{ $projectInfo['project_size'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Storage:</span>
                        <span class="font-mono text-green-400">{{ $projectInfo['storage_size'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Public:</span>
                        <span class="font-mono text-yellow-400">{{ $projectInfo['public_size'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Vendor:</span>
                        <span class="font-mono text-red-400">{{ $projectInfo['vendor_size'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Migrations:</span>
                        <span class="font-mono text-purple-400">{{ $projectInfo['total_migrations'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Models:</span>
                        <span class="font-mono text-blue-400">{{ $projectInfo['total_models'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Controllers:</span>
                        <span class="font-mono text-green-400">{{ $projectInfo['total_controllers'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Views:</span>
                        <span class="font-mono text-yellow-400">{{ $projectInfo['total_views'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Routes:</span>
                        <span class="font-mono text-red-400">{{ $projectInfo['total_routes'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas Detalhadas -->
        <div class="mt-8 bg-gray-800 rounded-lg border border-gray-700">
            <div class="px-6 py-4 border-b border-gray-700">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-chart-bar mr-2 text-green-400"></i>Estatísticas Detalhadas do Sistema
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-400 mb-2">{{ number_format($stats['total_companies']) }}</div>
                        <div class="text-sm text-gray-400">Empresas</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-400 mb-2">{{ number_format($stats['total_employees']) }}</div>
                        <div class="text-sm text-gray-400">Funcionários</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-yellow-400 mb-2">{{ number_format($stats['total_customers']) }}</div>
                        <div class="text-sm text-gray-400">Clientes</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-400 mb-2">{{ number_format($stats['total_suppliers']) }}</div>
                        <div class="text-sm text-gray-400">Fornecedores</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimas Atividades -->
        <div class="mt-8 bg-gray-800 rounded-lg border border-gray-700">
            <div class="px-6 py-4 border-b border-gray-700">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-history mr-2 text-yellow-400"></i>Últimas Atividades
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($recentActivities['last_user_login'])
                        <div class="flex items-center p-4 bg-gray-700 rounded-lg">
                            <i class="fas fa-user text-blue-400 text-xl mr-4"></i>
                            <div>
                                <p class="text-white font-semibold">Último Login</p>
                                <p class="text-gray-400 text-sm">{{ $recentActivities['last_user_login']->name ?? 'N/A' }}</p>
                                <p class="text-gray-500 text-xs">{{ date('d/m/Y H:i', strtotime($recentActivities['last_user_login']->updated_at)) }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($recentActivities['last_sale'])
                        <div class="flex items-center p-4 bg-gray-700 rounded-lg">
                            <i class="fas fa-shopping-cart text-green-400 text-xl mr-4"></i>
                            <div>
                                <p class="text-white font-semibold">Última Venda</p>
                                <p class="text-gray-400 text-sm">R$ {{ number_format($recentActivities['last_sale']->total, 2, ',', '.') }}</p>
                                <p class="text-gray-500 text-xs">{{ date('d/m/Y H:i', strtotime($recentActivities['last_sale']->created_at)) }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($recentActivities['last_product_added'])
                        <div class="flex items-center p-4 bg-gray-700 rounded-lg">
                            <i class="fas fa-box text-yellow-400 text-xl mr-4"></i>
                            <div>
                                <p class="text-white font-semibold">Último Produto</p>
                                <p class="text-gray-400 text-sm">{{ Str::limit($recentActivities['last_product_added']->name, 30) }}</p>
                                <p class="text-gray-500 text-xs">{{ date('d/m/Y H:i', strtotime($recentActivities['last_product_added']->created_at)) }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if($recentActivities['last_customer_added'])
                        <div class="flex items-center p-4 bg-gray-700 rounded-lg">
                            <i class="fas fa-user-plus text-purple-400 text-xl mr-4"></i>
                            <div>
                                <p class="text-white font-semibold">Último Cliente</p>
                                <p class="text-gray-400 text-sm">{{ Str::limit($recentActivities['last_customer_added']->name, 30) }}</p>
                                <p class="text-gray-500 text-xs">{{ date('d/m/Y H:i', strtotime($recentActivities['last_customer_added']->created_at)) }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function refreshData() {
            location.reload();
        }
        
        // Auto refresh a cada 60 segundos
        setInterval(() => {
            location.reload();
        }, 60000);
    </script>
</body>
</html>
