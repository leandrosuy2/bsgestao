<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin AI - Painel de Controle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <!-- Header -->
    <header class="bg-gray-800 border-b border-gray-700 px-6 py-4 sticky top-0 z-10">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('logs.reader') }}" class="text-blue-400 hover:text-blue-300 mr-4">
                    <i class="fas fa-arrow-left mr-1"></i>Voltar
                </a>
                <i class="fas fa-crown text-2xl text-red-400 mr-3"></i>
                <h1 class="text-xl font-bold">Admin AI - Painel de Controle</h1>
                <span class="ml-4 px-3 py-1 bg-red-600 text-xs rounded-full animate-pulse">
                    <i class="fas fa-robot mr-1"></i>AI Powered
                </span>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-400">
                    <i class="fas fa-shield-alt mr-1"></i>Super Admin Access
                </span>
                <a href="{{ route('logs.logout') }}" class="text-red-400 hover:text-red-300 transition">
                    <i class="fas fa-sign-out-alt mr-1"></i>Sair
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-6 py-8">
        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-red-600 to-purple-600 p-6 rounded-lg mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">
                        <i class="fas fa-magic mr-2"></i>Bem-vindo ao Admin AI
                    </h2>
                    <p class="text-red-100">
                        Controle total do sistema com inteligência artificial avançada
                    </p>
                </div>
                <div class="text-6xl opacity-20">
                    <i class="fas fa-robot"></i>
                </div>
            </div>
        </div>

        <!-- Admin Tools Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Histórico de Logins -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 hover:border-blue-500 transition group">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-600 rounded-lg group-hover:bg-blue-500 transition">
                            <i class="fas fa-history text-2xl"></i>
                        </div>
                        <span class="text-xs bg-blue-900 text-blue-300 px-2 py-1 rounded">SEGURANÇA</span>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Histórico de Logins</h3>
                    <p class="text-gray-400 text-sm mb-4">
                        Monitore todos os acessos ao sistema, detecte tentativas suspeitas e analise padrões de uso.
                    </p>
                    <a href="{{ route('logs.login-history') }}" 
                       class="inline-flex items-center text-blue-400 hover:text-blue-300 transition">
                        <i class="fas fa-arrow-right mr-2"></i>Acessar Histórico
                    </a>
                </div>
            </div>

            <!-- Gerenciador de Migrations -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 hover:border-green-500 transition group">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-green-600 rounded-lg group-hover:bg-green-500 transition">
                            <i class="fas fa-database text-2xl"></i>
                        </div>
                        <span class="text-xs bg-green-900 text-green-300 px-2 py-1 rounded">DATABASE</span>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Migrations Manager</h3>
                    <p class="text-gray-400 text-sm mb-4">
                        Execute migrations, rollbacks e monitore a estrutura do banco de dados em tempo real.
                    </p>
                    <a href="{{ route('logs.migrations') }}" 
                       class="inline-flex items-center text-green-400 hover:text-green-300 transition">
                        <i class="fas fa-arrow-right mr-2"></i>Gerenciar Migrations
                    </a>
                </div>
            </div>

            <!-- Gerenciador de Usuários -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 hover:border-purple-500 transition group">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-600 rounded-lg group-hover:bg-purple-500 transition">
                            <i class="fas fa-users-cog text-2xl"></i>
                        </div>
                        <span class="text-xs bg-purple-900 text-purple-300 px-2 py-1 rounded">USUÁRIOS</span>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Gerenciador de Usuários</h3>
                    <p class="text-gray-400 text-sm mb-4">
                        Edite, delete e gerencie todos os usuários do sistema com controle total.
                    </p>
                    <a href="{{ route('logs.users') }}" 
                       class="inline-flex items-center text-purple-400 hover:text-purple-300 transition">
                        <i class="fas fa-arrow-right mr-2"></i>Gerenciar Usuários
                    </a>
                </div>
            </div>

            <!-- Database Manager -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 hover:border-cyan-500 transition group">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-cyan-600 rounded-lg group-hover:bg-cyan-500 transition">
                            <i class="fas fa-database text-2xl"></i>
                        </div>
                        <span class="text-xs bg-cyan-900 text-cyan-300 px-2 py-1 rounded">DATABASE</span>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Database Manager</h3>
                    <p class="text-gray-400 text-sm mb-4">
                        Execute queries SQL, visualize tabelas e altere dados diretamente no banco.
                    </p>
                    <a href="{{ route('logs.database') }}" 
                       class="inline-flex items-center text-cyan-400 hover:text-cyan-300 transition">
                        <i class="fas fa-arrow-right mr-2"></i>Acessar Database
                    </a>
                </div>
            </div>

            <!-- Ambiente do Sistema -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 hover:border-yellow-500 transition group">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-yellow-600 rounded-lg group-hover:bg-yellow-500 transition">
                            <i class="fas fa-server text-2xl"></i>
                        </div>
                        <span class="text-xs bg-yellow-900 text-yellow-300 px-2 py-1 rounded">SISTEMA</span>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Ambiente do Sistema</h3>
                    <p class="text-gray-400 text-sm mb-4">
                        Monitore performance, configurações e status de todos os serviços.
                    </p>
                    <a href="{{ route('logs.environment') }}" 
                       class="inline-flex items-center text-yellow-400 hover:text-yellow-300 transition">
                        <i class="fas fa-arrow-right mr-2"></i>Ver Ambiente
                    </a>
                </div>
            </div>

            <!-- Logs do Sistema -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 hover:border-orange-500 transition group">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-orange-600 rounded-lg group-hover:bg-orange-500 transition">
                            <i class="fas fa-file-alt text-2xl"></i>
                        </div>
                        <span class="text-xs bg-orange-900 text-orange-300 px-2 py-1 rounded">LOGS</span>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Leitor de Logs</h3>
                    <p class="text-gray-400 text-sm mb-4">
                        Acesse, filtre e analise todos os logs do sistema de forma inteligente.
                    </p>
                    <a href="{{ route('logs.reader') }}" 
                       class="inline-flex items-center text-orange-400 hover:text-orange-300 transition">
                        <i class="fas fa-arrow-right mr-2"></i>Ver Logs
                    </a>
                </div>
            </div>

            <!-- Ferramentas Avançadas -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 hover:border-red-500 transition group">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-red-600 rounded-lg group-hover:bg-red-500 transition">
                            <i class="fas fa-tools text-2xl"></i>
                        </div>
                        <span class="text-xs bg-red-900 text-red-300 px-2 py-1 rounded">EM BREVE</span>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Ferramentas AI</h3>
                    <p class="text-gray-400 text-sm mb-4">
                        Análise inteligente, otimização automática e diagnósticos avançados.
                    </p>
                    <button disabled class="inline-flex items-center text-gray-500 cursor-not-allowed">
                        <i class="fas fa-lock mr-2"></i>Em Desenvolvimento
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="mt-8 bg-gray-800 rounded-lg border border-gray-700">
            <div class="px-6 py-4 border-b border-gray-700">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-chart-line mr-2 text-blue-400"></i>Status Rápido do Sistema
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-400 mb-1">
                            <i class="fas fa-circle text-green-400 mr-1"></i>ONLINE
                        </div>
                        <div class="text-sm text-gray-400">Status do Sistema</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-400 mb-1" id="uptime">--:--:--</div>
                        <div class="text-sm text-gray-400">Uptime</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-400 mb-1" id="memory">---MB</div>
                        <div class="text-sm text-gray-400">Memória PHP</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-400 mb-1">{{ date('H:i:s') }}</div>
                        <div class="text-sm text-gray-400">Horário Servidor</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Assistant Placeholder -->
        <div class="mt-8 bg-gradient-to-r from-purple-900 to-red-900 rounded-lg border border-purple-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold mb-2">
                        <i class="fas fa-robot mr-2"></i>Assistente AI
                    </h3>
                    <p class="text-purple-200">
                        Inteligência artificial para análise automática de logs, detecção de problemas e otimização do sistema.
                    </p>
                </div>
                <div class="text-4xl opacity-20">
                    <i class="fas fa-brain"></i>
                </div>
            </div>
            <div class="mt-4 text-sm text-purple-300">
                <i class="fas fa-info-circle mr-1"></i>
                Funcionalidade em desenvolvimento - Em breve análises inteligentes automáticas!
            </div>
        </div>
    </div>

    <script>
        // Simula uptime
        function updateUptime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('uptime').textContent = `${hours}:${minutes}:${seconds}`;
        }
        
        // Simula uso de memória
        function updateMemory() {
            const memory = Math.floor(Math.random() * 256) + 64;
            document.getElementById('memory').textContent = `${memory}MB`;
        }
        
        // Atualiza stats a cada segundo
        setInterval(() => {
            updateUptime();
            if (Math.random() > 0.9) updateMemory(); // Atualiza memória ocasionalmente
        }, 1000);
        
        // Inicializa
        updateUptime();
        updateMemory();
    </script>
</body>
</html>
