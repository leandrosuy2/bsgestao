<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Reader - {{ $filename }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .log-line {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.4;
        }
        .log-error { @apply text-red-400 bg-red-900 bg-opacity-20; }
        .log-warning { @apply text-yellow-400 bg-yellow-900 bg-opacity-20; }
        .log-info { @apply text-blue-400 bg-blue-900 bg-opacity-20; }
        .log-debug { @apply text-gray-400 bg-gray-700 bg-opacity-20; }
    </style>
</head>
<body class="bg-gray-900 text-white">
    <!-- Header -->
    <header class="bg-gray-800 border-b border-gray-700 px-6 py-4 sticky top-0 z-10">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('logs.reader') }}" class="text-blue-400 hover:text-blue-300 mr-4">
                    <i class="fas fa-arrow-left mr-1"></i>Voltar
                </a>
                <i class="fas fa-file-code text-2xl text-blue-400 mr-3"></i>
                <h1 class="text-xl font-bold">{{ $filename }}</h1>
                <span class="ml-4 px-2 py-1 bg-green-600 text-xs rounded">
                    {{ $displayedLines }}/{{ $totalLines }} linhas
                </span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('logs.environment') }}" 
                   class="bg-purple-600 hover:bg-purple-700 px-3 py-1 rounded text-sm transition">
                    <i class="fas fa-cogs mr-1"></i>Ambiente
                </a>
                <button onclick="toggleAutoRefresh()" id="autoRefreshBtn" 
                        class="px-3 py-1 bg-blue-600 hover:bg-blue-700 rounded text-sm transition">
                    <i class="fas fa-sync mr-1"></i>Auto Refresh: ON
                </button>
                <a href="{{ route('logs.logout') }}" class="text-red-400 hover:text-red-300 transition">
                    <i class="fas fa-sign-out-alt mr-1"></i>Sair
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-6 py-6">
        <!-- Filtros -->
        <div class="bg-gray-800 p-4 rounded-lg border border-gray-700 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">
                        <i class="fas fa-list-ol mr-1"></i>Número de Linhas
                    </label>
                    <select name="lines" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white">
                        <option value="50" {{ $currentLines == 50 ? 'selected' : '' }}>50 linhas</option>
                        <option value="100" {{ $currentLines == 100 ? 'selected' : '' }}>100 linhas</option>
                        <option value="200" {{ $currentLines == 200 ? 'selected' : '' }}>200 linhas</option>
                        <option value="500" {{ $currentLines == 500 ? 'selected' : '' }}>500 linhas</option>
                        <option value="1000" {{ $currentLines == 1000 ? 'selected' : '' }}>1000 linhas</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">
                        <i class="fas fa-filter mr-1"></i>Nível
                    </label>
                    <select name="level" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white">
                        <option value="">Todos os níveis</option>
                        <option value="ERROR" {{ $currentLevel == 'ERROR' ? 'selected' : '' }}>ERROR</option>
                        <option value="WARNING" {{ $currentLevel == 'WARNING' ? 'selected' : '' }}>WARNING</option>
                        <option value="INFO" {{ $currentLevel == 'INFO' ? 'selected' : '' }}>INFO</option>
                        <option value="DEBUG" {{ $currentLevel == 'DEBUG' ? 'selected' : '' }}>DEBUG</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">
                        <i class="fas fa-search mr-1"></i>Buscar
                    </label>
                    <input type="text" name="search" value="{{ $currentSearch }}" 
                           placeholder="Buscar no log..."
                           class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white placeholder-gray-400">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded transition">
                        <i class="fas fa-filter mr-1"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Ações Rápidas -->
        <div class="flex flex-wrap gap-2 mb-6">
            <a href="{{ route('logs.download', $filename) }}" 
               class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded text-white transition">
                <i class="fas fa-download mr-1"></i>Download
            </a>
            <button onclick="copyLogs()" 
                    class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded text-white transition">
                <i class="fas fa-copy mr-1"></i>Copiar Logs
            </button>
            <button onclick="clearLog()" 
                    class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded text-white transition">
                <i class="fas fa-trash mr-1"></i>Limpar Log
            </button>
            <button onclick="scrollToBottom()" 
                    class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded text-white transition">
                <i class="fas fa-arrow-down mr-1"></i>Ir para o Final
            </button>
        </div>

        <!-- Conteúdo do Log -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="px-4 py-2 bg-gray-700 border-b border-gray-600 flex items-center justify-between">
                <span class="text-sm font-mono">
                    <i class="fas fa-terminal mr-1"></i>Log Content
                </span>
                <span class="text-xs text-gray-400">
                    Última atualização: {{ date('H:i:s') }}
                </span>
            </div>
            
            <div id="logContent" class="p-4 bg-gray-900 max-h-96 overflow-y-auto">
                @if(count($logLines) > 0)
                    @foreach($logLines as $index => $line)
                        @if(trim($line))
                            <div class="log-line mb-1 p-2 rounded {{ 
                                stripos($line, 'ERROR') !== false ? 'log-error' : 
                                (stripos($line, 'WARNING') !== false ? 'log-warning' : 
                                (stripos($line, 'INFO') !== false ? 'log-info' : 'log-debug'))
                            }}">
                                <span class="text-gray-500 text-xs mr-2">{{ $index + 1 }}:</span>
                                {{ $line }}
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="text-center text-gray-400 py-8">
                        <i class="fas fa-file-alt text-4xl mb-4"></i>
                        <p>Nenhuma linha encontrada com os filtros aplicados</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-gray-800 p-6 rounded-lg max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">
                <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
                Confirmar Limpeza
            </h3>
            <p class="text-gray-300 mb-6">
                Tem certeza que deseja limpar o arquivo <strong>{{ $filename }}</strong>?
                Esta ação é irreversível.
            </p>
            <div class="flex space-x-4">
                <button onclick="hideModal()" 
                        class="flex-1 bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded transition">
                    Cancelar
                </button>
                <form method="POST" action="{{ route('logs.clear', $filename) }}" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full bg-red-600 hover:bg-red-700 px-4 py-2 rounded transition">
                        <i class="fas fa-trash mr-1"></i>Limpar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let autoRefresh = true;
        let refreshInterval;
        
        function toggleAutoRefresh() {
            autoRefresh = !autoRefresh;
            const btn = document.getElementById('autoRefreshBtn');
            
            if (autoRefresh) {
                btn.innerHTML = '<i class="fas fa-sync mr-1"></i>Auto Refresh: ON';
                btn.className = 'px-3 py-1 bg-blue-600 hover:bg-blue-700 rounded text-sm transition';
                startAutoRefresh();
            } else {
                btn.innerHTML = '<i class="fas fa-pause mr-1"></i>Auto Refresh: OFF';
                btn.className = 'px-3 py-1 bg-gray-600 hover:bg-gray-700 rounded text-sm transition';
                clearInterval(refreshInterval);
            }
        }
        
        function startAutoRefresh() {
            refreshInterval = setInterval(() => {
                if (autoRefresh) {
                    location.reload();
                }
            }, 5000);
        }
        
        function scrollToBottom() {
            const logContent = document.getElementById('logContent');
            logContent.scrollTop = logContent.scrollHeight;
        }
        
        function copyLogs() {
            const logLines = document.querySelectorAll('.log-line');
            let text = '';
            logLines.forEach(line => {
                text += line.textContent + '\n';
            });
            
            navigator.clipboard.writeText(text).then(() => {
                alert('Logs copiados para a área de transferência!');
            });
        }
        
        function clearLog() {
            document.getElementById('confirmModal').classList.remove('hidden');
            document.getElementById('confirmModal').classList.add('flex');
        }
        
        function hideModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            document.getElementById('confirmModal').classList.remove('flex');
        }
        
        // Iniciar auto refresh
        startAutoRefresh();
        
        // Scroll automático para o final ao carregar
        window.addEventListener('load', () => {
            setTimeout(scrollToBottom, 100);
        });
    </script>
</body>
</html>
