<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin AI - Gerenciador de Usuários</title>
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
                <i class="fas fa-users-cog text-2xl text-purple-400 mr-3"></i>
                <h1 class="text-xl font-bold">Gerenciador de Usuários</h1>
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
        <!-- Estatísticas de Usuários -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 text-center">
                <div class="text-3xl font-bold text-blue-400 mb-2">{{ number_format($userStats['total_users']) }}</div>
                <div class="text-sm text-gray-400">Total de Usuários</div>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 text-center">
                <div class="text-3xl font-bold text-red-400 mb-2">{{ number_format($userStats['admin_users']) }}</div>
                <div class="text-sm text-gray-400">Administradores</div>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 text-center">
                <div class="text-3xl font-bold text-green-400 mb-2">{{ number_format($userStats['active_users']) }}</div>
                <div class="text-sm text-gray-400">Ativos (30 dias)</div>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 text-center">
                <div class="text-3xl font-bold text-yellow-400 mb-2">{{ number_format($userStats['blocked_users']) }}</div>
                <div class="text-sm text-gray-400">Não Verificados</div>
            </div>
        </div>

        <!-- Busca -->
        <div class="bg-gray-800 p-4 rounded-lg border border-gray-700 mb-6">
            <form method="GET" class="flex items-center space-x-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-300 mb-1">
                        <i class="fas fa-search mr-1"></i>Buscar Usuário
                    </label>
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Nome ou email..."
                           class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white placeholder-gray-400">
                </div>
                <div class="flex items-end">
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded transition">
                        <i class="fas fa-search mr-1"></i>Buscar
                    </button>
                </div>
            </form>
        </div>

        <!-- Lista de Usuários -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-700">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-users mr-2 text-purple-400"></i>Usuários do Sistema ({{ $totalUsers }})
                </h2>
            </div>
            
            @if(count($users) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    Usuário
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    Criado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-700 transition" id="user-row-{{ $user->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-white font-bold text-sm">
                                                    {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-white">{{ $user->name ?? 'Sem nome' }}</div>
                                                <div class="text-sm text-gray-400">ID: {{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-300">{{ $user->email }}</div>
                                        @if($user->email_verified_at)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-900 text-green-300">
                                                <i class="fas fa-check mr-1"></i>Verificado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-900 text-yellow-300">
                                                <i class="fas fa-exclamation mr-1"></i>Não Verificado
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ ($user->role ?? 'user') == 'admin' ? 'bg-red-900 text-red-300' : 'bg-blue-900 text-blue-300' }}">
                                            <i class="fas fa-{{ ($user->role ?? 'user') == 'admin' ? 'crown' : 'user' }} mr-1"></i>
                                            {{ ucfirst($user->role ?? 'user') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ date('d/m/Y H:i', strtotime($user->created_at)) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex space-x-2">
                                            <button onclick="editUser({{ $user->id }})" 
                                                    class="bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded text-white transition">
                                                <i class="fas fa-edit mr-1"></i>Editar
                                            </button>
                                            <button onclick="deleteUser({{ $user->id }})" 
                                                    class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-white transition">
                                                <i class="fas fa-trash mr-1"></i>Deletar
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
                    <i class="fas fa-users text-4xl mb-4"></i>
                    <p class="text-lg">Nenhum usuário encontrado</p>
                    @if($search)
                        <p class="text-sm">Tente uma busca diferente</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Edição -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-gray-800 p-6 rounded-lg max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">
                <i class="fas fa-edit text-blue-400 mr-2"></i>
                Editar Usuário
            </h3>
            <form id="editForm">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Nome</label>
                        <input type="text" id="editName" 
                               class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                        <input type="email" id="editEmail" 
                               class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Role</label>
                        <select id="editRole" 
                                class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                            <option value="manager">Manager</option>
                            <option value="estoquista">Estoquista</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Nova Senha (opcional)</label>
                        <input type="password" id="editPassword" placeholder="Deixe em branco para manter"
                               class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white">
                    </div>
                </div>
                <div class="flex space-x-4 mt-6">
                    <button type="button" onclick="hideEditModal()" 
                            class="flex-1 bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded transition">
                        <i class="fas fa-save mr-1"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Confirmação de Deleção -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-gray-800 p-6 rounded-lg max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">
                <i class="fas fa-exclamation-triangle text-red-400 mr-2"></i>
                Confirmar Deleção
            </h3>
            <p class="text-gray-300 mb-6">
                Tem certeza que deseja deletar este usuário? 
                <strong>Esta ação é irreversível.</strong>
            </p>
            <div class="flex space-x-4">
                <button onclick="hideDeleteModal()" 
                        class="flex-1 bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded transition">
                    Cancelar
                </button>
                <button onclick="confirmDelete()" 
                        class="flex-1 bg-red-600 hover:bg-red-700 px-4 py-2 rounded transition">
                    <i class="fas fa-trash mr-1"></i>Deletar
                </button>
            </div>
        </div>
    </div>

    <!-- Notificações -->
    <div id="notification" class="fixed top-4 right-4 hidden z-50">
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 shadow-lg">
            <div id="notificationContent" class="text-white"></div>
        </div>
    </div>

    <script>
        let currentUserId = null;
        
        function editUser(userId) {
            currentUserId = userId;
            const row = document.getElementById(`user-row-${userId}`);
            
            // Pegar dados do usuário da tabela
            const name = row.querySelector('td:nth-child(1) .font-semibold').textContent;
            const email = row.querySelector('td:nth-child(2) .text-gray-300').textContent;
            const role = row.querySelector('td:nth-child(3) span').textContent.toLowerCase().trim();
            
            // Preencher modal
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRole').value = role;
            document.getElementById('editPassword').value = '';
            
            // Mostrar modal
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }
        
        function hideEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
            currentUserId = null;
        }
        
        function deleteUser(userId) {
            currentUserId = userId;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }
        
        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
            currentUserId = null;
        }
        
        function confirmDelete() {
            if (!currentUserId) return;
            
            fetch(`/logs/users/${currentUserId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    document.getElementById(`user-row-${currentUserId}`).remove();
                } else {
                    showNotification(data.message, 'error');
                }
                hideDeleteModal();
            })
            .catch(error => {
                showNotification('Erro ao deletar usuário', 'error');
                hideDeleteModal();
            });
        }
        
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!currentUserId) return;
            
            const formData = {
                name: document.getElementById('editName').value,
                email: document.getElementById('editEmail').value,
                role: document.getElementById('editRole').value
            };
            
            const password = document.getElementById('editPassword').value;
            if (password) {
                formData.password = password;
            }
            
            fetch(`/logs/users/${currentUserId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message, 'error');
                }
                hideEditModal();
            })
            .catch(error => {
                showNotification('Erro ao atualizar usuário', 'error');
                hideEditModal();
            });
        });
        
        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            const content = document.getElementById('notificationContent');
            
            content.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle text-green-400' : 'exclamation-circle text-red-400'} mr-2"></i>
                    ${message}
                </div>
            `;
            
            notification.classList.remove('hidden');
            
            setTimeout(() => {
                notification.classList.add('hidden');
            }, 3000);
        }
        
        function refreshData() {
            location.reload();
        }
    </script>
</body>
</html>
