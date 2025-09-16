<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Manager - Admin AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <!-- Header -->
    <header class="bg-gray-800 border-b border-gray-700 px-6 py-4 sticky top-0 z-10">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('logs.admin-panel') }}" class="text-blue-400 hover:text-blue-300 mr-4">
                    <i class="fas fa-arrow-left mr-1"></i>Voltar
                </a>
                <i class="fas fa-database text-2xl text-cyan-400 mr-3"></i>
                <h1 class="text-xl font-bold">Database Manager</h1>
                <span class="ml-4 px-3 py-1 bg-cyan-600 text-xs rounded-full">
                    <i class="fas fa-code mr-1"></i>SQL Console
                </span>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="text-sm text-gray-400">Conectado</span>
                </div>
                <a href="{{ route('logs.logout') }}" class="text-red-400 hover:text-red-300 transition">
                    <i class="fas fa-sign-out-alt mr-1"></i>Sair
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- SQL Console -->
            <div class="lg:col-span-2">
                <div class="bg-gray-800 rounded-lg border border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                        <h2 class="text-lg font-semibold flex items-center">
                            <i class="fas fa-terminal mr-2 text-cyan-400"></i>SQL Console
                        </h2>
                        <div class="flex items-center space-x-2">
                            <button onclick="clearConsole()" class="px-3 py-1 bg-gray-600 hover:bg-gray-500 rounded text-sm transition">
                                <i class="fas fa-trash mr-1"></i>Limpar
                            </button>
                            <button onclick="formatQuery()" class="px-3 py-1 bg-blue-600 hover:bg-blue-500 rounded text-sm transition">
                                <i class="fas fa-magic mr-1"></i>Formatar
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <form id="queryForm" onsubmit="executeQuery(event)">
                            <div class="mb-4">
                                <textarea 
                                    id="queryInput" 
                                    placeholder="Digite sua query SQL aqui... (ex: SELECT * FROM users LIMIT 10)"
                                    class="w-full h-32 bg-gray-900 border border-gray-600 rounded p-3 text-white font-mono text-sm resize-none focus:border-cyan-500 focus:outline-none"
                                >SELECT * FROM users LIMIT 10</textarea>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <button type="submit" class="bg-cyan-600 hover:bg-cyan-500 px-4 py-2 rounded transition flex items-center">
                                        <i class="fas fa-play mr-2"></i>Executar Query
                                    </button>
                                    <div class="flex items-center space-x-2">
                                        <label class="text-sm text-gray-400">Limite:</label>
                                        <select id="limitSelect" class="bg-gray-700 border border-gray-600 rounded px-2 py-1 text-sm">
                                            <option value="10">10 linhas</option>
                                            <option value="50">50 linhas</option>
                                            <option value="100" selected>100 linhas</option>
                                            <option value="500">500 linhas</option>
                                            <option value="1000">1000 linhas</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="queryStats" class="text-sm text-gray-400"></div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Results Area -->
                <div id="resultsArea" class="mt-6 bg-gray-800 rounded-lg border border-gray-700 hidden">
                    <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                        <h3 class="text-lg font-semibold flex items-center">
                            <i class="fas fa-table mr-2 text-green-400"></i>Resultados
                        </h3>
                        <button onclick="exportResults()" class="px-3 py-1 bg-green-600 hover:bg-green-500 rounded text-sm transition">
                            <i class="fas fa-download mr-1"></i>Exportar JSON
                        </button>
                    </div>
                    <div id="resultsContent" class="p-6 overflow-x-auto">
                        <!-- Results will be inserted here -->
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Tables List -->
                <div class="bg-gray-800 rounded-lg border border-gray-700">
                    <div class="px-4 py-3 border-b border-gray-700">
                        <h3 class="font-semibold flex items-center">
                            <i class="fas fa-list mr-2 text-blue-400"></i>Tabelas ({{ count($tableNames) }})
                        </h3>
                    </div>
                    <div class="max-h-64 overflow-y-auto">
                        @foreach($tableNames as $table)
                        <div class="px-4 py-2 border-b border-gray-700 last:border-b-0 hover:bg-gray-700 transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <button onclick="selectTable('{{ $table['name'] }}')" 
                                            class="text-cyan-400 hover:text-cyan-300 font-mono text-sm">
                                        {{ $table['name'] }}
                                    </button>
                                    <div class="text-xs text-gray-500">
                                        {{ number_format($table['rows']) }} linhas • {{ $table['size'] }}
                                    </div>
                                </div>
                                <div class="flex space-x-1">
                                    <button onclick="browseTable('{{ $table['name'] }}')" 
                                            class="text-blue-400 hover:text-blue-300 text-xs" title="Visualizar dados">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="exportTable('{{ $table['name'] }}')" 
                                            class="text-green-400 hover:text-green-300 text-xs" title="Exportar">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Query Templates -->
                <div class="bg-gray-800 rounded-lg border border-gray-700">
                    <div class="px-4 py-3 border-b border-gray-700">
                        <h3 class="font-semibold flex items-center">
                            <i class="fas fa-bookmark mr-2 text-yellow-400"></i>Templates
                        </h3>
                    </div>
                    <div class="max-h-64 overflow-y-auto">
                        @foreach($queryTemplates as $name => $query)
                        <div class="px-4 py-2 border-b border-gray-700 last:border-b-0 hover:bg-gray-700 transition">
                            <button onclick="loadTemplate(`{{ addslashes($query) }}`)" 
                                    class="text-left w-full">
                                <div class="text-sm text-yellow-400 hover:text-yellow-300">{{ $name }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ Str::limit($query, 50) }}</div>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Query History -->
                <div class="bg-gray-800 rounded-lg border border-gray-700">
                    <div class="px-4 py-3 border-b border-gray-700">
                        <h3 class="font-semibold flex items-center">
                            <i class="fas fa-history mr-2 text-purple-400"></i>Histórico
                        </h3>
                    </div>
                    <div id="queryHistory" class="max-h-64 overflow-y-auto">
                        @if(count($recentQueries) > 0)
                            @foreach($recentQueries as $recent)
                            <div class="px-4 py-2 border-b border-gray-700 last:border-b-0 hover:bg-gray-700 transition">
                                <button onclick="loadTemplate(`{{ addslashes($recent['query']) }}`)" 
                                        class="text-left w-full">
                                    <div class="text-xs text-gray-400">{{ $recent['timestamp'] }}</div>
                                    <div class="text-sm text-purple-400 truncate">{{ Str::limit($recent['query'], 40) }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $recent['execution_time'] }}ms • {{ $recent['affected_rows'] }} linhas
                                    </div>
                                </button>
                            </div>
                            @endforeach
                        @else
                            <div class="px-4 py-3 text-sm text-gray-500 text-center">
                                Nenhuma query executada ainda
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Browser Modal -->
    <div id="tableBrowserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-800 rounded-lg border border-gray-700 w-full max-w-6xl max-h-[90vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold" id="modalTableName">
                        <i class="fas fa-table mr-2 text-blue-400"></i>Dados da Tabela
                    </h3>
                    <button onclick="closeTableBrowser()" class="text-gray-400 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="tableBrowserContent" class="p-6 overflow-auto max-h-[70vh]">
                    <!-- Table data will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentResults = null;

        function executeQuery(event) {
            event.preventDefault();
            
            const query = document.getElementById('queryInput').value.trim();
            const limit = document.getElementById('limitSelect').value;
            
            if (!query) {
                alert('Digite uma query para executar');
                return;
            }

            const button = event.target.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Executando...';
            button.disabled = true;

            fetch('{{ route("logs.execute-query") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ query, limit })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayResults(data);
                    updateQueryHistory();
                } else {
                    displayError(data.message);
                }
            })
            .catch(error => {
                displayError('Erro de conexão: ' + error.message);
            })
            .finally(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            });
        }

        function displayResults(data) {
            const resultsArea = document.getElementById('resultsArea');
            const resultsContent = document.getElementById('resultsContent');
            const queryStats = document.getElementById('queryStats');
            
            currentResults = data.results;
            
            // Update stats
            queryStats.innerHTML = `
                ${data.affected_rows} linhas • ${data.execution_time}ms • ${data.query_type}
            `;
            
            if (data.results.length === 0) {
                resultsContent.innerHTML = '<div class="text-center text-gray-400">Nenhum resultado encontrado</div>';
            } else {
                // Create table
                let html = '<div class="overflow-x-auto"><table class="w-full text-sm">';
                
                // Header
                html += '<thead class="bg-gray-700"><tr>';
                data.columns.forEach(col => {
                    html += `<th class="px-3 py-2 text-left border border-gray-600">${col}</th>`;
                });
                html += '</tr></thead>';
                
                // Body
                html += '<tbody>';
                data.results.forEach((row, index) => {
                    html += `<tr class="${index % 2 === 0 ? 'bg-gray-800' : 'bg-gray-750'}">`;
                    data.columns.forEach(col => {
                        let value = row[col];
                        if (value === null) value = '<span class="text-gray-500 italic">NULL</span>';
                        else if (typeof value === 'string' && value.length > 100) {
                            value = value.substring(0, 100) + '...';
                        }
                        html += `<td class="px-3 py-2 border border-gray-600">${value}</td>`;
                    });
                    html += '</tr>';
                });
                html += '</tbody></table></div>';
                
                resultsContent.innerHTML = html;
            }
            
            resultsArea.classList.remove('hidden');
        }

        function displayError(message) {
            const resultsArea = document.getElementById('resultsArea');
            const resultsContent = document.getElementById('resultsContent');
            
            resultsContent.innerHTML = `
                <div class="bg-red-900 border border-red-600 rounded p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-400 mr-2"></i>
                        <strong class="text-red-300">Erro na Query:</strong>
                    </div>
                    <pre class="mt-2 text-red-200 text-sm whitespace-pre-wrap">${message}</pre>
                </div>
            `;
            
            resultsArea.classList.remove('hidden');
        }

        function selectTable(tableName) {
            document.getElementById('queryInput').value = `SELECT * FROM ${tableName} LIMIT 10`;
        }

        function loadTemplate(query) {
            document.getElementById('queryInput').value = query;
        }

        function clearConsole() {
            document.getElementById('queryInput').value = '';
            document.getElementById('resultsArea').classList.add('hidden');
            document.getElementById('queryStats').innerHTML = '';
        }

        function formatQuery() {
            const input = document.getElementById('queryInput');
            let query = input.value.toUpperCase();
            
            // Basic SQL formatting
            query = query.replace(/SELECT/g, 'SELECT')
                        .replace(/FROM/g, '\nFROM')
                        .replace(/WHERE/g, '\nWHERE')
                        .replace(/ORDER BY/g, '\nORDER BY')
                        .replace(/GROUP BY/g, '\nGROUP BY')
                        .replace(/HAVING/g, '\nHAVING')
                        .replace(/LIMIT/g, '\nLIMIT');
            
            input.value = query;
        }

        function exportResults() {
            if (!currentResults) {
                alert('Nenhum resultado para exportar');
                return;
            }
            
            const dataStr = JSON.stringify(currentResults, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            const url = URL.createObjectURL(dataBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `query_results_${new Date().getTime()}.json`;
            link.click();
        }

        function browseTable(tableName) {
            const modal = document.getElementById('tableBrowserModal');
            const modalName = document.getElementById('modalTableName');
            const content = document.getElementById('tableBrowserContent');
            
            modalName.innerHTML = `<i class="fas fa-table mr-2 text-blue-400"></i>Dados da Tabela: ${tableName}`;
            content.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin mr-2"></i>Carregando...</div>';
            
            modal.classList.remove('hidden');
            
            fetch(`/logs/table/${tableName}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayTableData(data);
                    } else {
                        content.innerHTML = `<div class="text-red-400">Erro: ${data.message}</div>`;
                    }
                })
                .catch(error => {
                    content.innerHTML = `<div class="text-red-400">Erro: ${error.message}</div>`;
                });
        }

        function displayTableData(data) {
            const content = document.getElementById('tableBrowserContent');
            
            let html = `
                <div class="mb-4 flex justify-between items-center">
                    <div class="text-sm text-gray-400">
                        Total: ${data.total} registros • Página: ${data.page}/${data.total_pages}
                    </div>
                    <div class="space-x-2">
                        ${data.page > 1 ? `<button onclick="loadTablePage(${data.page - 1})" class="px-2 py-1 bg-blue-600 rounded text-xs">Anterior</button>` : ''}
                        ${data.page < data.total_pages ? `<button onclick="loadTablePage(${data.page + 1})" class="px-2 py-1 bg-blue-600 rounded text-xs">Próxima</button>` : ''}
                    </div>
                </div>
            `;
            
            if (data.data.length > 0) {
                html += '<div class="overflow-x-auto"><table class="w-full text-sm">';
                
                // Get columns from first row
                const columns = Object.keys(data.data[0]);
                
                // Header
                html += '<thead class="bg-gray-700"><tr>';
                columns.forEach(col => {
                    html += `<th class="px-3 py-2 text-left border border-gray-600">${col}</th>`;
                });
                html += '</tr></thead>';
                
                // Body
                html += '<tbody>';
                data.data.forEach((row, index) => {
                    html += `<tr class="${index % 2 === 0 ? 'bg-gray-800' : 'bg-gray-750'}">`;
                    columns.forEach(col => {
                        let value = row[col];
                        if (value === null) value = '<span class="text-gray-500 italic">NULL</span>';
                        else if (typeof value === 'string' && value.length > 50) {
                            value = value.substring(0, 50) + '...';
                        }
                        html += `<td class="px-3 py-2 border border-gray-600">${value}</td>`;
                    });
                    html += '</tr>';
                });
                html += '</tbody></table></div>';
            } else {
                html += '<div class="text-center text-gray-400">Nenhum dado encontrado</div>';
            }
            
            content.innerHTML = html;
        }

        function closeTableBrowser() {
            document.getElementById('tableBrowserModal').classList.add('hidden');
        }

        function exportTable(tableName) {
            window.open(`/logs/export/${tableName}`, '_blank');
        }

        function loadTablePage(page) {
            // Esta função seria implementada para carregar uma página específica
            // Por simplicidade, vamos apenas recarregar a primeira página
            const modalName = document.getElementById('modalTableName');
            const tableName = modalName.textContent.split(': ')[1];
            if (tableName) {
                browseTable(tableName);
            }
        }

        function updateQueryHistory() {
            // Reload page to update history (in a real app, you'd use AJAX)
            // For now, we'll just show a success message
            const queryStats = document.getElementById('queryStats');
            queryStats.innerHTML += ' • <span class="text-green-400">Query salva no histórico</span>';
        }

        // Auto-resize textarea
        document.getElementById('queryInput').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.max(this.scrollHeight, 128) + 'px';
        });
    </script>
</body>
</html>
