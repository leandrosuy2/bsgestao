<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin AI - Histórico de Logins</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <!-- Header -->
    <header class="bg-gray-800 border-b border-gray-700 px-6 py-4 sticky top-0 z-10">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('logs.admin-panel') }}" class="text-blue-400 hover:text-blue-300 mr-4">
                    <i class="fas fa-arrow-left mr-1"></i>Admin AI
                </a>
                <i class="fas fa-history text-2xl text-blue-400 mr-3"></i>
                <h1 class="text-xl font-bold">Histórico de Logins</h1>
            </div>
            <div class="flex items-center space-x-4">
                <button onclick="refreshData()" class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded text-sm transition">
                    <i class="fas fa-sync mr-1"></i>Atualizar
                </button>
                <a href="{{ route('logs.logout') }}" class="text-red-400 hover:text-red-300 transition">
                    <i class="fas fa-sign-out-alt mr-1"></i>Sair
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-6 py-8">
        <!-- Estatísticas de Login -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 text-center">
                <div class="text-3xl font-bold text-blue-400 mb-2">{{ number_format($loginStats['total_users']) }}</div>
                <div class="text-sm text-gray-400">Total Usuários</div>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 text-center">
                <div class="text-3xl font-bold text-green-400 mb-2">{{ number_format($loginStats['active_today']) }}</div>
                <div class="text-sm text-gray-400">Ativos Hoje</div>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 text-center">
                <div class="text-3xl font-bold text-yellow-400 mb-2">{{ number_format($loginStats['active_week']) }}</div>
                <div class="text-sm text-gray-400">Ativos Semana</div>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 text-center">
                <div class="text-3xl font-bold text-purple-400 mb-2">{{ number_format($loginStats['active_month']) }}</div>
                <div class="text-sm text-gray-400">Ativos Mês</div>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 text-center">
                <div class="text-3xl font-bold text-red-400 mb-2">{{ number_format($loginStats['new_users_month']) }}</div>
                <div class="text-sm text-gray-400">Novos Mês</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Histórico de Logs -->
            <div class="bg-gray-800 rounded-lg border border-gray-700">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h2 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-terminal mr-2 text-blue-400"></i>Logs de Autenticação
                    </h2>
                </div>
                <div class="p-6">
                    @if(count($loginHistory) > 0)
                        <div class="max-h-96 overflow-y-auto space-y-3">
                            @foreach($loginHistory as $log)
                                <div class="p-3 bg-gray-700 rounded-lg border-l-4 border-{{ 
                                    $log['type'] == 'error' ? 'red' : 
                                    ($log['type'] == 'warning' ? 'yellow' : 'blue') 
                                }}-500">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-mono text-gray-400">{{ $log['timestamp'] }}</span>
                                        <span class="px-2 py-1 text-xs rounded bg-{{ 
                                            $log['type'] == 'error' ? 'red' : 
                                            ($log['type'] == 'warning' ? 'yellow' : 'blue') 
                                        }}-900 text-{{ 
                                            $log['type'] == 'error' ? 'red' : 
                                            ($log['type'] == 'warning' ? 'yellow' : 'blue') 
                                        }}-300">
                                            {{ strtoupper($log['type']) }}
                                        </span>
                                    </div>
                                    <div class="text-sm font-mono text-gray-300">
                                        {{ Str::limit($log['content'], 100) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-gray-400 py-8">
                            <i class="fas fa-search text-4xl mb-4"></i>
                            <p>Nenhum log de autenticação encontrado</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Usuários Mais Ativos -->
            <div class="bg-gray-800 rounded-lg border border-gray-700">
                <div class="px-6 py-4 border-b border-gray-700">
                    <h2 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-users mr-2 text-green-400"></i>Usuários Mais Ativos
                    </h2>
                </div>
                <div class="p-6">
                    <div class="max-h-96 overflow-y-auto space-y-3">
                        @foreach($activeUsers as $user)
                            <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-white font-bold text-sm">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-white">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-green-400">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ date('d/m H:i', strtotime($user->updated_at)) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Desde {{ date('d/m/Y', strtotime($user->created_at)) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Análise de Segurança -->
        <div class="mt-8 bg-gray-800 rounded-lg border border-gray-700">
            <div class="px-6 py-4 border-b border-gray-700">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-shield-alt mr-2 text-yellow-400"></i>Análise de Segurança
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Tentativas Suspeitas -->
                    <div class="bg-red-900 bg-opacity-20 border border-red-700 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-red-400">Tentativas Suspeitas</h3>
                            <i class="fas fa-exclamation-triangle text-red-400"></i>
                        </div>
                        <div class="text-2xl font-bold text-red-400 mb-2">
                            {{ collect($loginHistory)->where('type', 'error')->count() }}
                        </div>
                        <p class="text-sm text-red-300">Falhas de login detectadas</p>
                    </div>

                    <!-- Logins Recentes -->
                    <div class="bg-green-900 bg-opacity-20 border border-green-700 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-green-400">Logins Bem-sucedidos</h3>
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="text-2xl font-bold text-green-400 mb-2">
                            {{ collect($loginHistory)->where('type', 'info')->count() }}
                        </div>
                        <p class="text-sm text-green-300">Acessos autorizados</p>
                    </div>

                    <!-- Alertas -->
                    <div class="bg-yellow-900 bg-opacity-20 border border-yellow-700 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-yellow-400">Alertas</h3>
                            <i class="fas fa-bell text-yellow-400"></i>
                        </div>
                        <div class="text-2xl font-bold text-yellow-400 mb-2">
                            {{ collect($loginHistory)->where('type', 'warning')->count() }}
                        </div>
                        <p class="text-sm text-yellow-300">Eventos importantes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recomendações de Segurança -->
        <div class="mt-8 bg-blue-900 bg-opacity-20 border border-blue-700 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-400 mb-4">
                <i class="fas fa-lightbulb mr-2"></i>Recomendações de Segurança
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start">
                    <i class="fas fa-check text-green-400 mt-1 mr-3"></i>
                    <div>
                        <h4 class="font-semibold text-white">Monitoramento Ativo</h4>
                        <p class="text-sm text-blue-200">Sistema está monitorando logins 24/7</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-info text-blue-400 mt-1 mr-3"></i>
                    <div>
                        <h4 class="font-semibold text-white">Logs Detalhados</h4>
                        <p class="text-sm text-blue-200">Todos os acessos são registrados</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-shield text-purple-400 mt-1 mr-3"></i>
                    <div>
                        <h4 class="font-semibold text-white">Proteção Avançada</h4>
                        <p class="text-sm text-blue-200">Detecção automática de anomalias</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-chart-line text-yellow-400 mt-1 mr-3"></i>
                    <div>
                        <h4 class="font-semibold text-white">Análise Inteligente</h4>
                        <p class="text-sm text-blue-200">IA analisa padrões de acesso</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function refreshData() {
            location.reload();
        }
        
        // Auto refresh a cada 2 minutos
        setInterval(() => {
            location.reload();
        }, 120000);
    </script>
</body>
</html>
