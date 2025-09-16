<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Reader - Autenticação</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-lg shadow-2xl w-full max-w-md">
        <div class="text-center mb-8">
            <i class="fas fa-file-alt text-4xl text-blue-400 mb-4"></i>
            <h1 class="text-2xl font-bold text-white">Log Reader</h1>
            <p class="text-gray-400">Acesso Administrativo</p>
        </div>
        
        <form method="POST" action="{{ route('logs.authenticate') }}">
            @csrf
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                    <i class="fas fa-lock mr-2"></i>Senha de Acesso
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    placeholder="Digite a senha..."
                    required
                    autofocus
                >
                @error('password')
                    <p class="text-red-400 text-sm mt-2">
                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                    </p>
                @enderror
            </div>
            
            <button 
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
            >
                <i class="fas fa-sign-in-alt mr-2"></i>
                Acessar Logs
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">
                <i class="fas fa-shield-alt mr-1"></i>
                Acesso restrito ao administrador do sistema
            </p>
        </div>
    </div>
    
    <script>
        // Auto focus no campo de senha
        document.getElementById('password').focus();
        
        // Enter para submeter
        document.getElementById('password').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });
    </script>
</body>
</html>
