<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Reader - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <!-- Header -->
    <header class="bg-gray-800 border-b border-gray-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-file-alt text-2xl text-blue-400 mr-3"></i>
                <h1 class="text-xl font-bold">Log Reader</h1>
                <span class="ml-4 px-3 py-1 bg-green-600 text-xs rounded-full">
                    <i class="fas fa-shield-alt mr-1"></i>Admin Access
                </span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('logs.admin-panel') }}" 
                   class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded text-white transition flex items-center">
                    <i class="fas fa-crown mr-2"></i>Admin AI
                </a>
                <a href="{{ route('logs.environment') }}" 
                   class="bg-purple-600 hover:bg-purple-700 px-4 py-2 rounded text-white transition flex items-center">
                    <i class="fas fa-cogs mr-2"></i>Ambiente Geral
                </a>
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
        @if(session('success'))
            <div class="bg-green-600 text-white p-4 rounded-lg mb-6 flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
            <!-- Stats Cards -->
            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
                <div class="flex items-center">
                    <i class="fas fa-file text-blue-400 text-2xl mr-3"></i>
                    <div>
                        <p class="text-gray-400 text-sm">Total de Logs</p>
                        <p class="text-2xl font-bold">{{ count($logFiles) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
                <div class="flex items-center">
                    <i class="fas fa-hdd text-green-400 text-2xl mr-3"></i>
                    <div>
                        <p class="text-gray-400 text-sm">Espaço Total</p>
                        <p class="text-2xl font-bold">
                            @php
                                $totalSize = 0;
                                foreach($logFiles as $file) {
                                    $totalSize += filesize($file['path']);
                                }
                                echo $totalSize > 0 ? number_format($totalSize / 1024 / 1024, 2) . ' MB' : '0 MB';
                            @endphp
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
                <div class="flex items-center">
                    <i class="fas fa-clock text-yellow-400 text-2xl mr-3"></i>
                    <div>
                        <p class="text-gray-400 text-sm">Último Update</p>
                        <p class="text-lg font-bold">
                            @if(count($logFiles) > 0)
                                {{ $logFiles[0]['modified'] }}
                            @else
                                Nenhum log
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700">
                <div class="flex items-center">
                    <i class="fas fa-server text-purple-400 text-2xl mr-3"></i>
                    <div>
                        <p class="text-gray-400 text-sm">Status</p>
                        <p class="text-lg font-bold text-green-400">
                            <i class="fas fa-circle text-green-400 mr-1"></i>Online
                        </p>
                    </div>
                </div>
            </div>

        <!-- Card Sicredi Integration -->
        <div class="bg-gray-800 p-6 rounded-lg border border-green-700 flex flex-col justify-between">
            <div class="flex items-center mb-2">
                <i class="fas fa-university text-green-400 text-2xl mr-3"></i>
                <div>
                    <p class="text-gray-400 text-sm">Integração Sicredi</p>
                    <p class="text-lg font-bold">Cobrança &amp; Boletos</p>
                </div>
            </div>
            <p class="text-gray-400 text-xs mb-4">Gerencie a ativação Sicredi por usuário diretamente aqui.</p>
            <a href="{{ route('logs.sicredi-integrations') }}" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded text-white text-sm font-semibold flex items-center justify-center">
                <i class="fas fa-plug mr-2"></i>Painel Sicredi
            </a>
        </div>
        <!-- Card Ferramentas AI -->
        <div class="bg-gray-800 p-6 rounded-lg border border-blue-700 flex flex-col justify-between">
            <div class="flex items-center mb-2">
                <i class="fas fa-robot text-blue-400 text-2xl mr-3"></i>
                <div>
                    <p class="text-gray-400 text-sm">Ferramentas AI</p>
                    <p class="text-lg font-bold">Painel de Inteligência</p>
                </div>
            </div>
            <p class="text-gray-400 text-xs mb-4">Acesse recursos avançados de IA e automação.</p>
            <a href="{{ route('logs.admin-panel') }}" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded text-white text-sm font-semibold flex items-center justify-center">
                <i class="fas fa-crown mr-2"></i>Painel Admin AI
            </a>
        </div>
        </div>

        <!-- Log Files Table -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-700">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-list mr-2"></i>Arquivos de Log
                </h2>
            </div>
            
            @if(count($logFiles) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-file-alt mr-1"></i>Arquivo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-weight-hanging mr-1"></i>Tamanho
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-calendar mr-1"></i>Modificado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    <i class="fas fa-cogs mr-1"></i>Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($logFiles as $file)
                                <tr class="hover:bg-gray-700 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="fas fa-file-code text-blue-400 mr-2"></i>
                                            <span class="font-medium">{{ $file['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $file['size'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $file['modified'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('logs.view', $file['name']) }}" 
                                               class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded text-white transition">
                                                <i class="fas fa-eye mr-1"></i>Ver
                                            </a>
                                            <a href="{{ route('logs.download', $file['name']) }}" 
                                               class="bg-green-600 hover:bg-green-700 px-3 py-1 rounded text-white transition">
                                                <i class="fas fa-download mr-1"></i>Download
                                            </a>
                                            <button onclick="clearLog('{{ $file['name'] }}')"
                                                    class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-white transition">
                                                <i class="fas fa-trash mr-1"></i>Limpar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-gray-400">
                    <i class="fas fa-inbox text-4xl mb-4"></i>
                    <p class="text-lg">Nenhum arquivo de log encontrado</p>
                    <p class="text-sm">Os logs aparecerão aqui quando forem gerados pelo sistema</p>
                </div>
            @endif
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
                Tem certeza que deseja limpar o arquivo de log <strong id="logFileName"></strong>?
                Esta ação é irreversível.
            </p>
            <div class="flex space-x-4">
                <button onclick="hideModal()" 
                        class="flex-1 bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded transition">
                    Cancelar
                </button>
                <form id="clearForm" method="POST" class="flex-1">
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
        function clearLog(filename) {
            document.getElementById('logFileName').textContent = filename;
            document.getElementById('clearForm').action = `/logs/clear/${filename}`;
            document.getElementById('confirmModal').classList.remove('hidden');
            document.getElementById('confirmModal').classList.add('flex');
        }
        
        function hideModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            document.getElementById('confirmModal').classList.remove('flex');
        }
        
        // Auto refresh a cada 30 segundos
        setInterval(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
