<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class LogReaderController extends Controller
{
    /**
     * Teste de integração Sicredi (autenticação, criar, consultar, pdf, baixa)
     */
    public function testSicrediIntegration(Request $request)
    {
        $userId = $request->input('user_id');
        $action = $request->input('action');
        $nossoNumero = $request->input('nosso_numero');
        $boletoData = $request->input('boleto_data');
        $integration = \App\Models\UserPaymentIntegration::where('user_id', $userId)->first();
        if (!$integration) {
            return back()->with('sicredi_result', 'Integração não encontrada para o usuário.');
        }
        $service = new \App\Services\SicrediService($integration);
        $result = null;
        try {
            switch ($action) {
                case 'auth':
                    $result = $service->authenticate();
                    break;
                case 'criar':
                    $data = $boletoData ? json_decode($boletoData, true) : [];
                    $result = $service->criarBoleto($data);
                    break;
                case 'consultar':
                    $result = $service->consultarBoleto($nossoNumero);
                    break;
                case 'pdf':
                    $pdf = $service->baixarPdf($nossoNumero);
                    if ($pdf) {
                        $filename = 'boleto_' . $nossoNumero . '.pdf';
                        return response($pdf)
                            ->header('Content-Type', 'application/pdf')
                            ->header('Content-Disposition', 'attachment; filename=' . $filename);
                    }
                    $result = 'Erro ao baixar PDF.';
                    break;
                case 'baixa':
                    $result = $service->baixarBoleto($nossoNumero, now()->toDateString());
                    break;
                default:
                    $result = 'Ação inválida.';
            }
        } catch (\Throwable $e) {
            $result = 'Erro: ' . $e->getMessage();
        }
        if (is_array($result) || is_object($result)) {
            $result = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        return back()->with('sicredi_result', $result);
    }
    /**
     * Painel completo de permissões para ADMIN
     */
    public function adminPermissionsPanel(Request $request)
    {
        $users = \App\Models\User::with('roles')->get();
        $roles = \App\Models\Role::with('permissions')->get();
        $permissions = \App\Models\Permission::all();
        return view('admin.permissions_panel', compact('users', 'roles', 'permissions'));
    }

    // Atribuir papel ao usuário
    public function assignRoleToUser(Request $request)
    {
        $user = \App\Models\User::findOrFail($request->user_id);
        $role = \App\Models\Role::findOrFail($request->role_id);
        $user->roles()->syncWithoutDetaching([$role->id]);
        return back()->with('success', 'Papel atribuído ao usuário!');
    }

    // Remover papel do usuário
    public function removeRoleFromUser(Request $request)
    {
        $user = \App\Models\User::findOrFail($request->user_id);
        $role = \App\Models\Role::findOrFail($request->role_id);
        $user->roles()->detach($role->id);
        return back()->with('success', 'Papel removido do usuário!');
    }

    // Atribuir permissão ao papel
    public function assignPermissionToRole(Request $request)
    {
        $role = \App\Models\Role::findOrFail($request->role_id);
        $permission = \App\Models\Permission::findOrFail($request->permission_id);
        $role->permissions()->syncWithoutDetaching([$permission->id]);
        return back()->with('success', 'Permissão atribuída ao papel!');
    }

    // Remover permissão do papel
    public function removePermissionFromRole(Request $request)
    {
        $role = \App\Models\Role::findOrFail($request->role_id);
        $permission = \App\Models\Permission::findOrFail($request->permission_id);
        $role->permissions()->detach($permission->id);
        return back()->with('success', 'Permissão removida do papel!');
    }
    /**
     * Painel exclusivo para SUPERADMIN gerenciar permissões do sistema.
     */
    public function superadminPermissionsPanel(Request $request)
    {
        // Busca todos os papéis, permissões e usuários
        $roles = \App\Models\Role::all();
        $permissions = method_exists(\App\Models\Role::class, 'permissions') ? \App\Models\Role::first()->permissions()->getModel()->all() : [];
        $users = \App\Models\User::all();

        // Aqui pode adicionar lógica para edição, atribuição, etc.
        return view('superadmin.permissions_panel', compact('roles', 'permissions', 'users'));
    }
    /**
     * Exibe o painel de gerenciamento de permissões.
     */
    public function permissionsPanel(Request $request)
    {
        if (!$this->isAuthenticated($request)) {
            return redirect()->route('logs.reader');
        }

        // Busca papéis, permissões e usuários
        $roles = \App\Models\Role::all();
        $permissions = method_exists(\App\Models\Role::class, 'permissions') ? \App\Models\Role::first()->permissions()->getModel()->all() : [];
        $users = \App\Models\User::all();

        return view('logs.permissions_panel', compact('roles', 'permissions', 'users'));
    }
    // Sicredi Integration Panel
    public function sicrediIntegrationsPanel(Request $request)
    {
        $users = \App\Models\User::with('paymentIntegration')->get();
        return view('logs.sicredi_integrations', compact('users'));
    }

    public function toggleSicrediIntegration(Request $request, $userId)
    {
        $enabled = $request->input('enabled', false);
        $integration = \App\Models\UserPaymentIntegration::firstOrNew(['user_id' => $userId]);
        $integration->enabled = $enabled;
        $fields = [
            'x_api_key', 'client_id', 'client_secret', 'cooperativa', 'posto',
            'codigo_beneficiario', 'beneficiario_nome', 'beneficiario_documento',
            'beneficiario_tipo_pessoa', 'beneficiario_cep', 'beneficiario_cidade',
            'beneficiario_uf', 'beneficiario_endereco', 'beneficiario_numero'
        ];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $integration->$field = $request->input($field);
            }
        }
        $integration->save();
        return redirect()->back()->with('success', 'Integração atualizada com sucesso!');
    }

    private $password = 'Jujuba090!';
    
    public function index(Request $request)
    {
        // Permite acesso para admin, manager e superadmin
        $user = $request->user();
        if (!$this->isAuthenticated($request)) {
            return $this->showLoginForm($request);
        }
        if ($user && !($user->isAdmin() || $user->hasRole('manager') || $user->hasRole('superadmin'))) {
            abort(403, 'Acesso negado: você não tem permissão para visualizar os logs.');
        }
        
        $logPath = storage_path('logs');
        $logFiles = [];
        
        if (File::exists($logPath)) {
            $files = File::files($logPath);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'log') {
                    $logFiles[] = [
                        'name' => $file->getFilename(),
                        'path' => $file->getPathname(),
                        'size' => $this->formatBytes($file->getSize()),
                        'modified' => date('d/m/Y H:i:s', $file->getMTime())
                    ];
                }
            }
        }
        
        // Ordena por data de modificação (mais recente primeiro)
        usort($logFiles, function($a, $b) {
            return filemtime($b['path']) - filemtime($a['path']);
        });
        
        return view('logs.reader', compact('logFiles'));
    }
    
    public function authenticate(Request $request)
    {
        $password = $request->input('password');
        
        if ($password === $this->password) {
            session(['log_authenticated' => true]);
            return redirect()->route('logs.reader');
        }
        
        return back()->withErrors(['password' => 'Senha incorreta']);
    }
    
    public function logout()
    {
        session()->forget('log_authenticated');
        return redirect()->route('logs.reader');
    }
    
    public function environment(Request $request)
    {
        if (!$this->isAuthenticated($request)) {
            return redirect()->route('logs.reader');
        }
        
        // Informações do Sistema
        $systemInfo = [
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'operating_system' => php_uname(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'debug_mode' => config('app.debug') ? 'Ativo' : 'Inativo',
            'app_env' => config('app.env'),
            'app_url' => config('app.url'),
        ];
        
        // Informações do Banco de Dados
        try {
            $dbConnection = \DB::connection();
            $dbConfig = config('database.connections.' . config('database.default'));
            $databaseInfo = [
                'connection' => config('database.default'),
                'driver' => $dbConfig['driver'] ?? 'N/A',
                'host' => $dbConfig['host'] ?? 'N/A',
                'port' => $dbConfig['port'] ?? 'N/A',
                'database' => $dbConfig['database'] ?? 'N/A',
                'charset' => $dbConfig['charset'] ?? 'N/A',
                'collation' => $dbConfig['collation'] ?? 'N/A',
                'status' => 'Conectado',
                'version' => \DB::select('SELECT VERSION() as version')[0]->version ?? 'N/A'
            ];
        } catch (\Exception $e) {
            $databaseInfo = [
                'status' => 'Erro de Conexão',
                'error' => $e->getMessage()
            ];
        }
        
        // Estatísticas do Sistema
        $stats = [
            'total_users' => \DB::table('users')->count(),
            'total_companies' => \DB::table('companies')->count(),
            'total_products' => \DB::table('products')->count(),
            'total_sales' => \DB::table('sales')->count(),
            'total_employees' => \DB::table('employees')->count(),
            'total_customers' => \DB::table('customers')->count(),
            'total_suppliers' => \DB::table('suppliers')->count(),
            'total_categories' => \DB::table('categories')->count(),
            'sales_today' => \DB::table('sales')->whereDate('created_at', today())->count(),
            'sales_this_month' => \DB::table('sales')->whereMonth('created_at', now()->month)->count(),
            'revenue_today' => \DB::table('sales')->whereDate('created_at', today())->sum('total'),
            'revenue_this_month' => \DB::table('sales')->whereMonth('created_at', now()->month)->sum('total'),
        ];
        
        // Informações dos Arquivos
        $projectInfo = [
            'project_size' => $this->getDirectorySize(base_path()),
            'storage_size' => $this->getDirectorySize(storage_path()),
            'public_size' => $this->getDirectorySize(public_path()),
            'vendor_size' => $this->getDirectorySize(base_path('vendor')),
            'total_migrations' => count(glob(database_path('migrations/*.php'))),
            'total_models' => count(glob(app_path('Models/*.php'))),
            'total_controllers' => count(glob(app_path('Http/Controllers/*.php'))),
            'total_views' => count(glob(resource_path('views/**/*.blade.php'))),
            'total_routes' => $this->countRoutes(),
        ];
        
        // Configurações Importantes
        $configs = [
            'session_driver' => config('session.driver'),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'mail_driver' => config('mail.default'),
            'filesystem_driver' => config('filesystems.default'),
            'broadcast_driver' => config('broadcasting.default'),
            'log_channel' => config('logging.default'),
        ];
        
        // Status dos Serviços
        $services = [
            'database' => $this->checkDatabaseConnection(),
            'cache' => $this->checkCacheConnection(),
            'session' => $this->checkSessionStatus(),
            'storage' => $this->checkStorageStatus(),
            'logs' => $this->checkLogsStatus(),
        ];
        
        // Últimas Atividades
        $recentActivities = [
            'last_user_login' => \DB::table('users')->latest('updated_at')->first(),
            'last_sale' => \DB::table('sales')->latest('created_at')->first(),
            'last_product_added' => \DB::table('products')->latest('created_at')->first(),
            'last_customer_added' => \DB::table('customers')->latest('created_at')->first(),
        ];
        
        return view('logs.environment', compact(
            'systemInfo', 'databaseInfo', 'stats', 'projectInfo', 
            'configs', 'services', 'recentActivities'
        ));
    }
    
    public function adminPanel(Request $request)
    {
        if (!$this->isAuthenticated($request)) {
            return redirect()->route('logs.reader');
        }
        
        return view('logs.admin-panel');
    }
    
    public function loginHistory(Request $request)
    {
        if (!$this->isAuthenticated($request)) {
            return redirect()->route('logs.reader');
        }
        
        // Busca histórico de logins nos logs
        $loginHistory = $this->extractLoginHistory();
        
        // Busca usuários mais ativos
        $activeUsers = \DB::table('users')
            ->select('id', 'name', 'email', 'updated_at', 'created_at')
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();
            
        // Estatísticas de login
        $loginStats = [
            'total_users' => \DB::table('users')->count(),
            'active_today' => \DB::table('users')->whereDate('updated_at', today())->count(),
            'active_week' => \DB::table('users')->where('updated_at', '>=', now()->subWeek())->count(),
            'active_month' => \DB::table('users')->where('updated_at', '>=', now()->subMonth())->count(),
            'new_users_month' => \DB::table('users')->where('created_at', '>=', now()->subMonth())->count(),
        ];
        
        return view('logs.login-history', compact('loginHistory', 'activeUsers', 'loginStats'));
    }
    
    public function migrationsManager(Request $request)
    {
        if (!$this->isAuthenticated($request)) {
            return redirect()->route('logs.reader');
        }
        
        // Lista todas as migrations
        $migrationFiles = glob(database_path('migrations/*.php'));
        $migrations = [];
        
        foreach ($migrationFiles as $file) {
            $filename = basename($file);
            $migrations[] = [
                'filename' => $filename,
                'path' => $file,
                'size' => $this->formatBytes(filesize($file)),
                'modified' => date('d/m/Y H:i:s', filemtime($file)),
                'class_name' => $this->extractMigrationClassName($file)
            ];
        }
        
        // Ordena por data
        usort($migrations, function($a, $b) {
            return filemtime($b['path']) - filemtime($a['path']);
        });
        
        // Status das migrations
        try {
            $migrationStatus = \DB::table('migrations')->get()->pluck('migration')->toArray();
        } catch (\Exception $e) {
            $migrationStatus = [];
        }
        
        return view('logs.migrations-manager', compact('migrations', 'migrationStatus'));
    }
    
    public function runMigration(Request $request)
    {
        if (!$this->isAuthenticated($request)) {
            return redirect()->route('logs.reader');
        }
        
        $command = $request->input('command', 'migrate');
        $output = '';
        $exitCode = 0;
        
        try {
            switch ($command) {
                case 'migrate':
                    \Artisan::call('migrate', ['--force' => true]);
                    $output = \Artisan::output();
                    break;
                    
                case 'migrate:rollback':
                    \Artisan::call('migrate:rollback', ['--force' => true]);
                    $output = \Artisan::output();
                    break;
                    
                case 'migrate:refresh':
                    \Artisan::call('migrate:refresh', ['--force' => true]);
                    $output = \Artisan::output();
                    break;
                    
                case 'migrate:status':
                    \Artisan::call('migrate:status');
                    $output = \Artisan::output();
                    break;
                    
                default:
                    $output = "Comando não reconhecido";
                    $exitCode = 1;
            }
        } catch (\Exception $e) {
            $output = "Erro: " . $e->getMessage();
            $exitCode = 1;
        }
        
        return response()->json([
            'success' => $exitCode === 0,
            'output' => $output,
            'command' => $command
        ]);
    }
    
    public function userManager(Request $request)
    {
        if (!$this->isAuthenticated($request)) {
            return redirect()->route('logs.reader');
        }
        
        $search = $request->get('search', '');
        $perPage = 20;
        
        $query = \DB::table('users');
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }
        
        $users = $query->orderBy('created_at', 'desc')
                      ->limit($perPage)
                      ->get();
        
        $totalUsers = \DB::table('users')->count();
            
        // Estatísticas
        $userStats = [
            'total_users' => $totalUsers,
            'admin_users' => \DB::table('users')->where('role', 'admin')->count(),
            'active_users' => \DB::table('users')->where('updated_at', '>=', now()->subDays(30))->count(),
            'blocked_users' => \DB::table('users')->whereNull('email_verified_at')->count(),
        ];
        
        return view('logs.user-manager', compact('users', 'userStats', 'search', 'totalUsers'));
    }
    
    public function updateUser(Request $request, $id)
    {
        if (!$this->isAuthenticated($request)) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }
        
        try {
            $data = $request->only(['name', 'email', 'role']);
            
            // Se tem senha nova, hash ela
            if ($request->filled('password')) {
                $data['password'] = bcrypt($request->password);
            }
            
            \DB::table('users')->where('id', $id)->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Usuário atualizado com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar usuário: ' . $e->getMessage()
            ]);
        }
    }
    
    public function deleteUser(Request $request, $id)
    {
        if (!$this->isAuthenticated($request)) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }
        
        try {
            \DB::table('users')->where('id', $id)->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Usuário deletado com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar usuário: ' . $e->getMessage()
            ]);
        }
    }
    
    public function databaseManager(Request $request)
    {
        if (!$this->isAuthenticated($request)) {
            return redirect()->route('logs.reader');
        }
        
        // Lista todas as tabelas
        $tables = \DB::select('SHOW TABLES');
        $tableNames = [];
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            $tableNames[] = [
                'name' => $tableName,
                'rows' => \DB::table($tableName)->count(),
                'size' => $this->getTableSize($tableName)
            ];
        }
        
        // Consultas recentes (simulado - em produção você salvaria isso)
        $recentQueries = session('recent_queries', []);
        
        // Queries favoritas/templates
        $queryTemplates = [
            'Listar Usuários' => 'SELECT id, name, email, created_at FROM users ORDER BY created_at DESC LIMIT 10',
            'Vendas Hoje' => 'SELECT * FROM sales WHERE DATE(created_at) = CURDATE() ORDER BY created_at DESC',
            'Produtos Mais Vendidos' => 'SELECT p.name, SUM(si.quantity) as total_sold FROM products p JOIN sale_items si ON p.id = si.product_id GROUP BY p.id ORDER BY total_sold DESC LIMIT 10',
            'Clientes Ativos' => 'SELECT * FROM customers WHERE updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY updated_at DESC',
            'Estrutura da Tabela Users' => 'DESCRIBE users',
            'Status das Migrations' => 'SELECT * FROM migrations ORDER BY batch DESC LIMIT 20'
        ];
        
        return view('logs.database-manager', compact('tableNames', 'recentQueries', 'queryTemplates'));
    }
    
    public function executeQuery(Request $request)
    {
        if (!$this->isAuthenticated($request)) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }
        
        $query = trim($request->input('query', ''));
        $limit = (int) $request->input('limit', 100);
        
        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Query não pode estar vazia'
            ]);
        }
        
        // Segurança básica - previne algumas operações perigosas
        $dangerousCommands = ['DROP', 'TRUNCATE', 'DELETE FROM users WHERE id < 999'];
        foreach ($dangerousCommands as $dangerous) {
            if (stripos($query, $dangerous) !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comando perigoso detectado e bloqueado por segurança: ' . $dangerous
                ]);
            }
        }
        
        try {
            $startTime = microtime(true);
            
            // Determina o tipo de query
            $queryType = $this->getQueryType($query);
            
            if ($queryType === 'SELECT' || $queryType === 'SHOW' || $queryType === 'DESCRIBE') {
                // Para queries de leitura, aplica limite
                if (stripos($query, 'LIMIT') === false && $queryType === 'SELECT') {
                    $query .= " LIMIT {$limit}";
                }
                $results = \DB::select($query);
                $affectedRows = count($results);
            } else {
                // Para queries de escrita
                $results = \DB::statement($query);
                $affectedRows = \DB::getPdo()->rowCount();
            }
            
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            // Salva na sessão (últimas 10 queries)
            $recentQueries = session('recent_queries', []);
            array_unshift($recentQueries, [
                'query' => $query,
                'timestamp' => now()->format('d/m/Y H:i:s'),
                'execution_time' => $executionTime,
                'affected_rows' => $affectedRows
            ]);
            $recentQueries = array_slice($recentQueries, 0, 10);
            session(['recent_queries' => $recentQueries]);
            
            return response()->json([
                'success' => true,
                'results' => $results,
                'affected_rows' => $affectedRows,
                'execution_time' => $executionTime,
                'query_type' => $queryType,
                'columns' => $this->getQueryColumns($results)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro na execução: ' . $e->getMessage(),
                'error_code' => $e->getCode()
            ]);
        }
    }
    
    public function getTableData(Request $request, $tableName)
    {
        if (!$this->isAuthenticated($request)) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }
        
        try {
            $page = (int) $request->get('page', 1);
            $perPage = (int) $request->get('per_page', 20);
            $offset = ($page - 1) * $perPage;
            
            // Verifica se a tabela existe
            $tables = \DB::select('SHOW TABLES');
            $tableExists = false;
            foreach ($tables as $table) {
                if (array_values((array)$table)[0] === $tableName) {
                    $tableExists = true;
                    break;
                }
            }
            
            if (!$tableExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tabela não encontrada'
                ]);
            }
            
            // Dados da tabela
            $data = \DB::table($tableName)->offset($offset)->limit($perPage)->get();
            $total = \DB::table($tableName)->count();
            
            // Estrutura da tabela
            $structure = \DB::select("DESCRIBE {$tableName}");
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'structure' => $structure,
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($total / $perPage)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar dados: ' . $e->getMessage()
            ]);
        }
    }
    
    public function exportTable(Request $request, $tableName)
    {
        if (!$this->isAuthenticated($request)) {
            return redirect()->route('logs.reader');
        }
        
        try {
            $data = \DB::table($tableName)->get();
            
            $filename = "{$tableName}_" . date('Y-m-d_H-i-s') . '.json';
            $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
            return response($jsonData, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => "attachment; filename={$filename}"
            ]);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao exportar: ' . $e->getMessage());
        }
    }
    
    public function view(Request $request, $filename)
    {
        if (!$this->isAuthenticated($request)) {
            return redirect()->route('logs.reader');
        }
        
        $logPath = storage_path('logs/' . $filename);
        
        if (!File::exists($logPath) || !str_ends_with($filename, '.log')) {
            abort(404, 'Log file not found');
        }
        
        $lines = (int) $request->get('lines', 100);
        $search = $request->get('search', '');
        $level = $request->get('level', '');
        
        $content = File::get($logPath);
        $logLines = explode("\n", $content);
        
        // Filtra por nível se especificado
        if ($level) {
            $logLines = array_filter($logLines, function($line) use ($level) {
                return stripos($line, $level) !== false;
            });
        }
        
        // Filtra por busca se especificado
        if ($search) {
            $logLines = array_filter($logLines, function($line) use ($search) {
                return stripos($line, $search) !== false;
            });
        }
        
        // Pega apenas as últimas N linhas
        $logLines = array_slice(array_reverse($logLines), 0, $lines);
        $logLines = array_reverse($logLines);
        
        return view('logs.view', [
            'filename' => $filename,
            'logLines' => $logLines,
            'totalLines' => count(explode("\n", $content)),
            'displayedLines' => count($logLines),
            'currentLines' => $lines,
            'currentSearch' => $search,
            'currentLevel' => $level
        ]);
    }
    
    public function download(Request $request, $filename)
    {
        if (!$this->isAuthenticated($request)) {
            return redirect()->route('logs.reader');
        }
        
        $logPath = storage_path('logs/' . $filename);
        
        if (!File::exists($logPath) || !str_ends_with($filename, '.log')) {
            abort(404, 'Log file not found');
        }
        
        return Response::download($logPath);
    }
    
    public function clear(Request $request, $filename)
    {
        if (!$this->isAuthenticated($request)) {
            return redirect()->route('logs.reader');
        }
        
        $logPath = storage_path('logs/' . $filename);
        
        if (!File::exists($logPath) || !str_ends_with($filename, '.log')) {
            abort(404, 'Log file not found');
        }
        
        File::put($logPath, '');
        
        return redirect()->route('logs.reader')->with('success', "Log {$filename} foi limpo com sucesso!");
    }
    
    private function isAuthenticated(Request $request)
    {
        return session('log_authenticated') === true || 
               $request->get('password') === $this->password;
    }
    
    private function showLoginForm(Request $request)
    {
        return view('logs.login');
    }
    
    private function formatBytes($size, $precision = 2)
    {
        if ($size == 0) return '0 B';
        $base = log($size, 1024);
        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
        
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
    
    private function getDirectorySize($directory)
    {
        if (!is_dir($directory)) return 0;
        
        $size = 0;
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($files as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        
        return $this->formatBytes($size);
    }
    
    private function countRoutes()
    {
        try {
            $routes = \Route::getRoutes();
            return count($routes);
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function checkDatabaseConnection()
    {
        try {
            \DB::connection()->getPdo();
            return ['status' => 'OK', 'color' => 'green'];
        } catch (\Exception $e) {
            return ['status' => 'ERRO', 'color' => 'red', 'message' => $e->getMessage()];
        }
    }
    
    private function checkCacheConnection()
    {
        try {
            \Cache::put('test_key', 'test_value', 1);
            $value = \Cache::get('test_key');
            return $value === 'test_value' 
                ? ['status' => 'OK', 'color' => 'green'] 
                : ['status' => 'ERRO', 'color' => 'red'];
        } catch (\Exception $e) {
            return ['status' => 'ERRO', 'color' => 'red', 'message' => $e->getMessage()];
        }
    }
    
    private function checkSessionStatus()
    {
        return session()->isStarted() 
            ? ['status' => 'ATIVO', 'color' => 'green'] 
            : ['status' => 'INATIVO', 'color' => 'red'];
    }
    
    private function checkStorageStatus()
    {
        $storagePath = storage_path();
        return is_writable($storagePath) 
            ? ['status' => 'GRAVÁVEL', 'color' => 'green'] 
            : ['status' => 'SEM PERMISSÃO', 'color' => 'red'];
    }
    
    private function checkLogsStatus()
    {
        $logsPath = storage_path('logs');
        if (!is_dir($logsPath)) {
            return ['status' => 'DIRETÓRIO INEXISTENTE', 'color' => 'red'];
        }
        
        $logFiles = glob($logsPath . '/*.log');
        $totalSize = 0;
        foreach ($logFiles as $file) {
            $totalSize += filesize($file);
        }
        
        return [
            'status' => count($logFiles) . ' arquivos (' . $this->formatBytes($totalSize) . ')',
            'color' => 'blue'
        ];
    }
    
    private function extractLoginHistory()
    {
        $loginHistory = [];
        $logPath = storage_path('logs/laravel.log');
        
        if (!file_exists($logPath)) {
            return $loginHistory;
        }
        
        $logContent = file_get_contents($logPath);
        $lines = explode("\n", $logContent);
        
        foreach ($lines as $line) {
            // Procura por padrões de login
            if (stripos($line, 'login') !== false || stripos($line, 'authentication') !== false) {
                $loginHistory[] = [
                    'timestamp' => $this->extractTimestamp($line),
                    'content' => $line,
                    'type' => $this->getLogType($line)
                ];
            }
        }
        
        return array_slice(array_reverse($loginHistory), 0, 50); // Últimos 50
    }
    
    private function extractTimestamp($logLine)
    {
        // Extrai timestamp do formato do Laravel
        preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $logLine, $matches);
        return isset($matches[1]) ? $matches[1] : date('Y-m-d H:i:s');
    }
    
    private function getLogType($logLine)
    {
        if (stripos($logLine, 'error') !== false) return 'error';
        if (stripos($logLine, 'warning') !== false) return 'warning';
        if (stripos($logLine, 'info') !== false) return 'info';
        return 'debug';
    }
    
    private function extractMigrationClassName($filePath)
    {
        $content = file_get_contents($filePath);
        preg_match('/class\s+([a-zA-Z_][a-zA-Z0-9_]*)\s+extends/', $content, $matches);
        return isset($matches[1]) ? $matches[1] : 'Unknown';
    }
    
    private function getTableSize($tableName)
    {
        try {
            $result = \DB::select("
                SELECT 
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'size_mb'
                FROM information_schema.TABLES 
                WHERE table_schema = DATABASE() 
                AND table_name = ?
            ", [$tableName]);
            
            return isset($result[0]->size_mb) ? $result[0]->size_mb . ' MB' : 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
    
    private function getQueryType($query)
    {
        $query = trim(strtoupper($query));
        
        if (strpos($query, 'SELECT') === 0) return 'SELECT';
        if (strpos($query, 'INSERT') === 0) return 'INSERT';
        if (strpos($query, 'UPDATE') === 0) return 'UPDATE';
        if (strpos($query, 'DELETE') === 0) return 'DELETE';
        if (strpos($query, 'SHOW') === 0) return 'SHOW';
        if (strpos($query, 'DESCRIBE') === 0) return 'DESCRIBE';
        if (strpos($query, 'CREATE') === 0) return 'CREATE';
        if (strpos($query, 'ALTER') === 0) return 'ALTER';
        if (strpos($query, 'DROP') === 0) return 'DROP';
        
        return 'OTHER';
    }
    
    private function getQueryColumns($results)
    {
        if (empty($results)) return [];
        
        $firstRow = (array) $results[0];
        return array_keys($firstRow);
    }
}
