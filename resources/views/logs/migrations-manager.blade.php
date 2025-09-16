<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin AI - Gerenciador de Migrations</title>
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
                <i class="fas fa-database text-2xl text-green-400 mr-3"></i>
                <h1 class="text-xl font-bold">Gerenciador de Migrations</h1>
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
        <!-- Comandos Rápidos -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <button onclick="runMigration('migrate')" 
                    class="bg-green-600 hover:bg-green-700 p-4 rounded-lg transition flex items-center justify-center">
                <i class="fas fa-play mr-2"></i>Executar Migrations
            </button>
            <button onclick="runMigration('migrate:rollback')" 
                    class="bg-yellow-600 hover:bg-yellow-700 p-4 rounded-lg transition flex items-center justify-center">
                <i class="fas fa-undo mr-2"></i>Rollback
            </button>
            <button onclick="runMigration('migrate:refresh')" 
                    class="bg-red-600 hover:bg-red-700 p-4 rounded-lg transition flex items-center justify-center">
                <i class="fas fa-refresh mr-2"></i>Refresh
            </button>
            <button onclick="runMigration('migrate:status')" 
                    class="bg-blue-600 hover:bg-blue-700 p-4 rounded-lg transition flex items-center justify-center">
                <i class="fas fa-list mr-2"></i>Status
            </button>
        </div>

        <!-- Output do Comando -->
        <div id="commandOutput" class="hidden bg-gray-800 rounded-lg border border-gray-700 mb-8">
            <div class="px-6 py-4 border-b border-gray-700">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-terminal mr-2 text-blue-400"></i>Output do Comando
                </h2>
            </div>
            <div class="p-6">
                <div id="outputContent" class="bg-gray-900 p-4 rounded font-mono text-sm max-h-96 overflow-y-auto"></div>
            </div>
        </div>

        <!-- Lista de Migrations -->
        <div class="bg-gray-800 rounded-lg border border-gray-700">
            <div class="px-6 py-4 border-b border-gray-700">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-file-code mr-2 text-green-400"></i>Arquivos de Migration ({{ count($migrations) }})
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Arquivo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Classe
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Tamanho
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                Modificado
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($migrations as $migration)
                            @php
                                $migrationName = str_replace('.php', '', $migration['filename']);
                                $isExecuted = in_array($migrationName, $migrationStatus);
                            @endphp
                            <tr class="hover:bg-gray-700 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($isExecuted)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900 text-green-300">
                                            <i class="fas fa-check mr-1"></i>Executada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-900 text-yellow-300">
                                            <i class="fas fa-clock mr-1"></i>Pendente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <i class="fas fa-file-code text-blue-400 mr-2"></i>
                                        <span class="font-mono text-sm">{{ $migration['filename'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-300 font-mono">{{ $migration['class_name'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ $migration['size'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                    {{ $migration['modified'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Informações Adicionais -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Estatísticas -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-chart-bar mr-2 text-blue-400"></i>Estatísticas
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Total de Migrations:</span>
                        <span class="font-semibold">{{ count($migrations) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Executadas:</span>
                        <span class="font-semibold text-green-400">{{ count($migrationStatus) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Pendentes:</span>
                        <span class="font-semibold text-yellow-400">{{ count($migrations) - count($migrationStatus) }}</span>
                    </div>
                </div>
            </div>

            <!-- Comandos Úteis -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-terminal mr-2 text-green-400"></i>Comandos Úteis
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="bg-gray-700 p-2 rounded font-mono">
                        php artisan migrate
                    </div>
                    <div class="bg-gray-700 p-2 rounded font-mono">
                        php artisan migrate:rollback
                    </div>
                    <div class="bg-gray-700 p-2 rounded font-mono">
                        php artisan migrate:status
                    </div>
                    <div class="bg-gray-700 p-2 rounded font-mono">
                        php artisan migrate:refresh
                    </div>
                </div>
            </div>

            <!-- Avisos -->
            <div class="bg-red-900 bg-opacity-20 border border-red-700 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center text-red-400">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Avisos Importantes
                </h3>
                <div class="space-y-3 text-sm text-red-300">
                    <div class="flex items-start">
                        <i class="fas fa-dot-circle mt-1 mr-2 text-red-400"></i>
                        <span>Sempre faça backup antes de executar migrations</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-dot-circle mt-1 mr-2 text-red-400"></i>
                        <span>Rollbacks podem causar perda de dados</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-dot-circle mt-1 mr-2 text-red-400"></i>
                        <span>Teste em ambiente de desenvolvimento primeiro</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-gray-800 p-6 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-spinner fa-spin text-blue-400 text-2xl mr-4"></i>
                <span class="text-white">Executando comando...</span>
            </div>
        </div>
    </div>

    <script>
        function runMigration(command) {
            const loadingModal = document.getElementById('loadingModal');
            const outputDiv = document.getElementById('commandOutput');
            const outputContent = document.getElementById('outputContent');
            
            // Mostra loading
            loadingModal.classList.remove('hidden');
            loadingModal.classList.add('flex');
            
            // Faz a requisição
            fetch('{{ route("logs.run-migration") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ command: command })
            })
            .then(response => response.json())
            .then(data => {
                // Esconde loading
                loadingModal.classList.add('hidden');
                loadingModal.classList.remove('flex');
                
                // Mostra output
                outputDiv.classList.remove('hidden');
                outputContent.innerHTML = `
                    <div class="mb-2">
                        <span class="text-blue-400">Comando:</span> 
                        <span class="text-white">${data.command}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-${data.success ? 'green' : 'red'}-400">Status:</span> 
                        <span class="text-${data.success ? 'green' : 'red'}-400">${data.success ? 'Sucesso' : 'Erro'}</span>
                    </div>
                    <div class="border-t border-gray-700 pt-2 mt-2">
                        <pre class="text-gray-300 whitespace-pre-wrap">${data.output}</pre>
                    </div>
                `;
                
                // Auto-refresh se for sucesso
                if (data.success && command !== 'migrate:status') {
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadingModal.classList.add('hidden');
                loadingModal.classList.remove('flex');
                
                outputDiv.classList.remove('hidden');
                outputContent.innerHTML = `
                    <div class="text-red-400">
                        Erro ao executar comando: ${error.message}
                    </div>
                `;
            });
        }
        
        function refreshData() {
            location.reload();
        }
    </script>
</body>
</html>
