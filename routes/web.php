


<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PayableController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TimeClockController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\VacationController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\BenefitController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Api\CnpjController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\CashMovementController;
use App\Http\Controllers\PDVController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeliveryReceiptController;
use App\Http\Controllers\LogReaderController;
use App\Http\Controllers\NfeController;

// Sicredi - Rotas RESTful padronizadas
Route::prefix('sicredi')->group(function () {
    Route::get('/boletos', [\App\Http\Controllers\BoletoSicrediController::class, 'listar']); // Listar boletos
    Route::post('/boletos', [\App\Http\Controllers\BoletoSicrediController::class, 'criar']); // Criar boleto
    Route::get('/boletos/consultar', [\App\Http\Controllers\BoletoSicrediController::class, 'consultar']); // Consultar boleto
    Route::get('/boletos/pdf', [\App\Http\Controllers\BoletoSicrediController::class, 'baixarPdf']); // Baixar PDF
});
// Página exclusiva de testes Sicredi
Route::middleware(['auth'])->prefix('sicredi-teste')->group(function () {
    Route::get('/', [\App\Http\Controllers\SicrediTesteController::class, 'index'])->name('sicredi.teste');
    Route::post('/criar', [\App\Http\Controllers\SicrediTesteController::class, 'criar'])->name('sicredi.teste.criar');
    Route::post('/consultar', [\App\Http\Controllers\SicrediTesteController::class, 'consultar'])->name('sicredi.teste.consultar');
    Route::post('/listar', [\App\Http\Controllers\SicrediTesteController::class, 'listar'])->name('sicredi.teste.listar');
    Route::post('/pdf', [\App\Http\Controllers\SicrediTesteController::class, 'pdf'])->name('sicredi.teste.pdf');
});
// Painel completo de permissões para ADMIN
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('permissions', [LogReaderController::class, 'adminPermissionsPanel'])->name('permissions.panel');
    Route::post('permissions/assign-role', [LogReaderController::class, 'assignRoleToUser'])->name('permissions.assignRole');
    Route::post('permissions/remove-role', [LogReaderController::class, 'removeRoleFromUser'])->name('permissions.removeRole');
    Route::post('permissions/assign-permission', [LogReaderController::class, 'assignPermissionToRole'])->name('permissions.assignPermission');
    Route::post('permissions/remove-permission', [LogReaderController::class, 'removePermissionFromRole'])->name('permissions.removePermission');
});

// Rota de teste NFe sem middleware de autenticação
Route::post('/api/nfe/criar-teste', [NfeController::class, 'criarTeste'])->name('api.nfe.criar-teste');

// NFe (Nota Fiscal Eletrônica)
Route::middleware(['auth'])->group(function () {
    Route::get('/nfe/painel', [NfeController::class, 'painel'])->name('nfe.painel');
    Route::resource('nfe', NfeController::class);
    Route::post('/nfe/{nfe}/emitir', [NfeController::class, 'emitir'])->name('nfe.emitir');
    Route::get('/nfe/{nfe}/consultar', [NfeController::class, 'consultar'])->name('nfe.consultar');
    Route::get('/nfe/{nfe}/danfe', [NfeController::class, 'danfe'])->name('nfe.danfe');
    Route::get('/nfe/{nfe}/xml', [NfeController::class, 'xml'])->name('nfe.xml');
    Route::post('/nfe/{nfe}/cancelar', [NfeController::class, 'cancelar'])->name('nfe.cancelar');
    Route::match(['get', 'post'], '/nfe/{nfe}/cancelar-24h', [NfeController::class, 'cancelarNfe24h'])->name('nfe.cancelar24h');
    Route::post('/nfe/{nfe}/devolucao', [NfeController::class, 'devolverNfe'])->name('nfe.devolucao');
    Route::get('/api/produtos-nfe', [NfeController::class, 'buscarProdutos'])->name('nfe.produtos');
    Route::get('/api/produtos-buscar', [NfeController::class, 'buscarProdutosAjax'])->name('nfe.produtos.buscar');
    Route::get('/nfe-teste/criar', [NfeController::class, 'criarTeste'])->name('nfe.teste.criar');
    Route::post('/nfe/criar-teste', [NfeController::class, 'criarTeste'])->name('nfe.criar-teste')->withoutMiddleware('web');
    
    // Rotas para consulta de numeração NFe
    Route::get('/api/nfe/ultimo-numero', [NfeController::class, 'consultarUltimoNumero'])->name('nfe.ultimo-numero');
    Route::post('/api/nfe/sincronizar-numeracao', [NfeController::class, 'sincronizarNumeracao'])->name('nfe.sincronizar-numeracao');
    
    // API para buscar clientes na emissão de NFe
    Route::get('/api/customers', function () {
        $customers = \App\Models\Customer::where('company_id', Auth::user()->company_id)
            ->where('active', true)
            ->select('id', 'name', 'cpf_cnpj', 'phone', 'address', 'city', 'state', 'postal_code', 'type')
            ->orderBy('name')
            ->get();
            
        // Debug: Log the customer data structure
        \Log::info('API Customers data:', $customers->toArray());
        
        return $customers;
    })->name('api.customers');
});

// Venda por Boleto (formulário simples)
Route::middleware(['auth'])->group(function () {
    Route::view('/venda-boleto', 'venda-boleto')->name('venda-boleto.form');
    Route::get('/venda-boleto', [\App\Http\Controllers\VendaBoletoController::class, 'index'])->name('venda-boleto');
    Route::post('/venda-boleto', [\App\Http\Controllers\VendaBoletoController::class, 'gerar'])->name('venda-boleto.gerar');
    Route::get('/pesquisar-boletos', [\App\Http\Controllers\VendaBoletoController::class, 'pesquisarBoletos'])->name('pesquisar-boletos');
    Route::post('/venda-boleto/listar-api', [\App\Http\Controllers\VendaBoletoController::class, 'listarBoletosApi'])->name('venda-boleto.listar-api');

    // Autocomplete de clientes para venda por boleto
    Route::get('/buscar-boletos', [App\Http\Controllers\BoletoSicrediController::class, 'buscarBoletos'])->name('buscar-boletos');
    Route::get('/baixar-boleto-pdf', [App\Http\Controllers\BoletoSicrediController::class, 'baixarPdf'])->name('baixar-boleto-pdf');
    Route::get('/api/clientes-autocomplete', function (Request $request) {
        $q = $request->input('q');
        $clientes = \App\Models\Customer::where('company_id', Auth::user()->company_id)
            ->where(function($query) use ($q) {
                $query->where('name', 'like', "%$q%")
                      ->orWhere('cpf_cnpj', 'like', "%$q%")
                      ->orWhere('email', 'like', "%$q%")
                      ->orWhere('phone', 'like', "%$q%")
                      ;
            })
            ->limit(10)
            ->get(['id','name','cpf_cnpj','address','city','state','postal_code']);
        return response()->json($clientes);
    })->name('clientes.autocomplete');

    // Boletos Sicredi (painel completo)
    Route::get('/boletos/sicredi', [\App\Http\Controllers\BoletoSicrediController::class, 'index'])->name('boletos.sicredi.index');
    Route::post('/boletos/sicredi/create', [\App\Http\Controllers\BoletoSicrediController::class, 'create'])->name('boletos.sicredi.create');
    Route::get('/boletos/sicredi/consultar', [\App\Http\Controllers\BoletoSicrediController::class, 'consultar'])->name('boletos.sicredi.consultar');
});
// Boletos Sicredi
Route::middleware(['auth'])->group(function () {
    Route::get('/boletos/sicredi', [\App\Http\Controllers\BoletoSicrediController::class, 'index'])->name('boletos.sicredi.index');
    Route::post('/boletos/sicredi/create', [\App\Http\Controllers\BoletoSicrediController::class, 'create'])->name('boletos.sicredi.create');
    Route::get('/boletos/sicredi/consultar', [\App\Http\Controllers\BoletoSicrediController::class, 'consultar'])->name('boletos.sicredi.consultar');
});
// ====== LOG READER ROUTES (INDEPENDENT) ======
Route::get('/logs', [LogReaderController::class, 'index'])->name('logs.reader');
Route::post('/logs/authenticate', [LogReaderController::class, 'authenticate'])->name('logs.authenticate');
Route::get('/logs/logout', [LogReaderController::class, 'logout'])->name('logs.logout');
Route::get('/logs/environment', [LogReaderController::class, 'environment'])->name('logs.environment');
Route::get('/logs/admin-panel', [LogReaderController::class, 'adminPanel'])->name('logs.admin-panel');
// Painel de permissões exclusivo para SUPERADMIN
Route::middleware(['auth'])->group(function () {
    Route::get('/superadmin/permissions', [LogReaderController::class, 'superadminPermissionsPanel'])->name('superadmin.permissions.panel');
});
// Painel de gerenciamento de permissões (acesso via painel admin de logs)
Route::get('/logs/admin-panel/permissions', [LogReaderController::class, 'permissionsPanel'])->name('logs.admin-panel.permissions');
Route::get('/logs/login-history', [LogReaderController::class, 'loginHistory'])->name('logs.login-history');
Route::get('/logs/migrations', [LogReaderController::class, 'migrationsManager'])->name('logs.migrations');
Route::post('/logs/run-migration', [LogReaderController::class, 'runMigration'])->name('logs.run-migration');
Route::get('/logs/users', [LogReaderController::class, 'userManager'])->name('logs.users');
Route::put('/logs/users/{id}', [LogReaderController::class, 'updateUser'])->name('logs.update-user');
Route::delete('/logs/users/{id}', [LogReaderController::class, 'deleteUser'])->name('logs.delete-user');
Route::get('/logs/database', [LogReaderController::class, 'databaseManager'])->name('logs.database');
Route::post('/logs/execute-query', [LogReaderController::class, 'executeQuery'])->name('logs.execute-query');
Route::get('/logs/table/{tableName}', [LogReaderController::class, 'getTableData'])->name('logs.table-data');
Route::get('/logs/export/{tableName}', [LogReaderController::class, 'exportTable'])->name('logs.export-table');
Route::get('/logs/view/{filename}', [LogReaderController::class, 'view'])->name('logs.view');
Route::get('/logs/download/{filename}', [LogReaderController::class, 'download'])->name('logs.download');
Route::delete('/logs/clear/{filename}', [LogReaderController::class, 'clear'])->name('logs.clear');
// ============================================

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect('/');
    }
    return back()->with('error', 'E-mail ou senha inválidos.');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

Route::middleware(['auth', 'company.access'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('stock_movements', StockMovementController::class);
    Route::resource('employees', EmployeeController::class);
    Route::patch('/employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');

    // Rotas do módulo financeiro
    Route::resource('payables', PayableController::class);
    Route::resource('receivables', ReceivableController::class);

    // Rotas específicas para marcar como pago/recebido
    Route::patch('payables/{payable}/marcar-pago', [PayableController::class, 'marcarComoPago'])->name('payables.marcar-pago');
    Route::patch('receivables/{receivable}/marcar-recebido', [ReceivableController::class, 'marcarComoRecebido'])->name('receivables.marcar-recebido');

    // Relatórios financeiros
    Route::prefix('financial-reports')->group(function () {
        Route::get('/', [FinancialReportController::class, 'index'])->name('financial-reports.index');
        Route::get('dashboard', [FinancialReportController::class, 'dashboard'])->name('financial-reports.dashboard');
        Route::get('fluxo-caixa', [FinancialReportController::class, 'fluxoCaixa'])->name('financial-reports.fluxo-caixa');
        Route::get('categorias', [FinancialReportController::class, 'categorias'])->name('financial-reports.categorias');
        Route::get('pessoas', [FinancialReportController::class, 'pessoas'])->name('financial-reports.pessoas');
    });

    Route::prefix('reports')->group(function () {
        Route::get('estoque-atual', [ReportController::class, 'estoqueAtual'])->name('reports.estoque_atual');
        Route::get('historico-movimentacoes', [ReportController::class, 'historicoMovimentacoes'])->name('reports.historico_movimentacoes');
        Route::get('alerta-estoque', [ReportController::class, 'alertaEstoque'])->name('reports.alerta_estoque');
        Route::get('produtos-mais-movimentados', [ReportController::class, 'produtosMaisMovimentados'])->name('reports.produtos_mais_movimentados');
    });

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    // Rotas para fornecedores
    Route::resource('suppliers', SupplierController::class);
    Route::patch('suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');

    // Rotas para clientes
    Route::resource('customers', CustomerController::class);
    Route::patch('customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');

    // Rotas para vendedores
    Route::resource('sellers', \App\Http\Controllers\SellersController::class);
    Route::patch('sellers/{seller}/toggle-status', [\App\Http\Controllers\SellersController::class, 'toggleStatus'])->name('sellers.toggle-status');
    Route::get('sellers/commissions', [\App\Http\Controllers\SellersController::class, 'commissions'])->name('sellers.commissions');

    // Rotas para romaneios (embaixo de movimentações de estoque)
    Route::resource('delivery_receipts', DeliveryReceiptController::class)->names([
        'index' => 'delivery_receipts.index',
        'create' => 'delivery_receipts.create',
        'store' => 'delivery_receipts.store',
        'show' => 'delivery_receipts.show',
        'edit' => 'delivery_receipts.edit',
        'update' => 'delivery_receipts.update',
        'destroy' => 'delivery_receipts.destroy'
    ]);
    Route::patch('delivery_receipts/{delivery_receipt}/status', [DeliveryReceiptController::class, 'updateStatus'])->name('delivery_receipts.update-status');
    Route::patch('delivery_receipts/{delivery_receipt}/items/{item}/check', [DeliveryReceiptController::class, 'updateItemCheck'])->name('delivery_receipts.update-item-check');
    Route::get('delivery_receipts/{delivery_receipt}/pdf', [DeliveryReceiptController::class, 'generatePdf'])->name('delivery_receipts.pdf');
    Route::get('api/cnpj-search', [DeliveryReceiptController::class, 'searchCnpj'])->name('api.cnpj-search');
    Route::get('api/products-search', [DeliveryReceiptController::class, 'searchProducts'])->name('api.products-search');
    Route::get('api/suppliers-search', [DeliveryReceiptController::class, 'searchSuppliers'])->name('api.suppliers-search');
    
    // Página de teste do romaneio
    Route::get('romaneio-teste', function () {
        return view('romaneio_teste');
    })->name('romaneio.teste');
    
    // Rota para criar produtos de teste
    Route::get('/criar-produtos-teste', function () {
        // Verificar se já existem produtos
        if (\App\Models\Product::count() > 0) {
            return "Já existem produtos no sistema. Total: " . \App\Models\Product::count();
        }

        // Criar categoria padrão se não existir
        $category = \App\Models\Category::firstOrCreate([
            'name' => 'Geral',
            'company_id' => 1
        ], [
            'code' => 'GER',
            'description' => 'Categoria geral'
        ]);

        // Produtos de exemplo
        $products = [
            [
                'name' => 'Arroz Branco 5kg',
                'internal_code' => 'ARR001',
                'description' => 'Arroz branco tipo 1, pacote 5kg',
                'unit' => 'PC',
                'cost_price' => 15.50,
                'sale_price' => 22.90,
                'min_stock' => 10,
            ],
            [
                'name' => 'Feijão Preto 1kg',
                'internal_code' => 'FEI001',
                'description' => 'Feijão preto tipo 1, pacote 1kg',
                'unit' => 'PC',
                'cost_price' => 8.20,
                'sale_price' => 12.50,
                'min_stock' => 20,
            ],
            [
                'name' => 'Açúcar Cristal 1kg',
                'internal_code' => 'ACU001',
                'description' => 'Açúcar cristal refinado, pacote 1kg',
                'unit' => 'PC',
                'cost_price' => 4.50,
                'sale_price' => 6.80,
                'min_stock' => 15,
            ],
            [
                'name' => 'Óleo de Soja 900ml',
                'internal_code' => 'OLE001',
                'description' => 'Óleo de soja refinado, garrafa 900ml',
                'unit' => 'PC',
                'cost_price' => 6.20,
                'sale_price' => 9.50,
                'min_stock' => 12,
            ],
            [
                'name' => 'Sal Refinado 1kg',
                'internal_code' => 'SAL001',
                'description' => 'Sal refinado iodado, pacote 1kg',
                'unit' => 'PC',
                'cost_price' => 2.10,
                'sale_price' => 3.20,
                'min_stock' => 25,
            ],
        ];

        foreach ($products as $productData) {
            \App\Models\Product::create([
                'name' => $productData['name'],
                'internal_code' => $productData['internal_code'],
                'description' => $productData['description'],
                'category_id' => $category->id,
                'unit' => $productData['unit'],
                'cost_price' => $productData['cost_price'],
                'sale_price' => $productData['sale_price'],
                'min_stock' => $productData['min_stock'],
                'company_id' => 1,
            ]);
        }

        return "Produtos criados com sucesso! Total: " . \App\Models\Product::count();
    });

    // Rotas do módulo RH / Departamento Pessoal
    Route::resource('timeclocks', TimeClockController::class);
    Route::resource('payrolls', PayrollController::class);
    Route::resource('vacations', VacationController::class);
    Route::resource('leaves', LeaveController::class);
    Route::resource('benefits', BenefitController::class);
    Route::resource('payslips', PayslipController::class);

    Route::get('/payment/notice', [PaymentController::class, 'notice'])->name('payment.notice');

    // Rotas para gerenciamento de usuários e papéis
    Route::resource('roles', RoleController::class);
    Route::patch('roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');

    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Sicredi Integration Panel (agora dentro do grupo de autenticação)
    Route::get('/logs/sicredi-integrations', [LogReaderController::class, 'sicrediIntegrationsPanel'])->name('logs.sicredi-integrations');
    Route::post('/logs/sicredi-integrations/toggle/{userId}', [LogReaderController::class, 'toggleSicrediIntegration'])->name('logs.sicredi-integrations.toggle');
    Route::post('/logs/sicredi-integrations/test', [LogReaderController::class, 'testSicrediIntegration'])->name('logs.sicredi-integrations.test');
});

// Rotas públicas para cadastro de empresa
Route::get('/cadastro-empresa', function () {
    Log::info('Acessou a rota /cadastro-empresa');
    return app(\App\Http\Controllers\Admin\CompanyController::class)->publicCreate(request());
});
Route::post('/cadastro-empresa', [CompanyController::class, 'publicStore'])->name('companies.public.store');

Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
    Route::put('companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
    Route::post('companies/{company}/toggle-active', [CompanyController::class, 'toggleActive'])->name('companies.toggleActive');
    Route::post('companies/{company}/liberar-pagamento', [CompanyController::class, 'liberarPagamento'])->name('companies.liberarPagamento');
    Route::post('companies/{company}/renovar-trial', [CompanyController::class, 'renovarTrial'])->name('companies.renovarTrial');
    Route::get('companies/create', [CompanyController::class, 'create'])->name('companies.create');
    Route::post('companies', [CompanyController::class, 'store'])->name('companies.store');
});

// Rota da API para buscar dados do CNPJ
Route::get('/api/cnpj/{cnpj}', [CnpjController::class, 'search']);

// Rotas do Caixa
Route::prefix('caixa')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\CashRegisterController::class, 'index'])->name('caixa.index');
    Route::post('/abrir', [\App\Http\Controllers\CashRegisterController::class, 'open'])->name('caixa.open');
    Route::get('/{id}', [\App\Http\Controllers\CashRegisterController::class, 'show'])->name('caixa.show');
    Route::post('/{id}/fechar', [\App\Http\Controllers\CashRegisterController::class, 'close'])->name('caixa.close');
    Route::get('/{id}/relatorio', [\App\Http\Controllers\CashRegisterController::class, 'report'])->name('caixa.report');
    // Movimentações
    Route::get('/{id}/movimentacoes', [\App\Http\Controllers\CashMovementController::class, 'index'])->name('caixa.movements');
    Route::post('/{id}/movimentacoes', [\App\Http\Controllers\CashMovementController::class, 'store'])->name('caixa.movements.store');
});

// Rotas do PDV
Route::prefix('pdv')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\PDVController::class, 'index'])->name('pdv.index');
    Route::get('/full', [\App\Http\Controllers\PDVController::class, 'index'])->name('pdv.full');
    Route::post('/iniciar', [\App\Http\Controllers\PDVController::class, 'startSale'])->name('pdv.start');
    Route::post('/item', [\App\Http\Controllers\PDVController::class, 'addItem'])->name('pdv.addItem');
    Route::delete('/item/{itemId}', [\App\Http\Controllers\PDVController::class, 'removeItem'])->name('pdv.removeItem');
    Route::post('/desconto', [\App\Http\Controllers\PDVController::class, 'applyDiscount'])->name('pdv.discount');
    Route::post('/pagamento', [\App\Http\Controllers\PDVController::class, 'addPayment'])->name('pdv.addPayment');
    Route::post('/finalizar', [\App\Http\Controllers\PDVController::class, 'finalize'])->name('pdv.finalize');
    Route::get('/venda/{id}/comprovante', [\App\Http\Controllers\PDVController::class, 'receipt'])->name('pdv.receipt');
    Route::get('/historico', [\App\Http\Controllers\PDVController::class, 'history'])->name('pdv.history');
    Route::post('/consulta-preco', [\App\Http\Controllers\PDVController::class, 'priceLookup'])->name('pdv.priceLookup');
});

// Sicredi - Gerador Teste Boletos
Route::post('/pdv/testar-boleto', [\App\Http\Controllers\BoletoSicrediController::class, 'testarBoleto'])->name('pdv.testar-boleto');

Route::post('/pdv/finalizar-nf', [\App\Http\Controllers\PDVController::class, 'finalizeWithInvoice'])->middleware(['auth'])->name('pdv.finalizeWithInvoice');
Route::post('/pdv/finalizar-sem-nf', [\App\Http\Controllers\PDVController::class, 'finalizeWithoutInvoice'])->middleware(['auth'])->name('pdv.finalizeWithoutInvoice');
Route::post('/pdv/finalizar', [\App\Http\Controllers\PDVController::class, 'finalize'])->middleware(['auth'])->name('pdv.finalize');
Route::get('/pdv/cupom/{id}', [\App\Http\Controllers\PDVController::class, 'cupom'])->middleware(['auth'])->name('pdv.cupom');
Route::get('/pdv/romaneio/{id}', [\App\Http\Controllers\PDVController::class, 'romaneio'])->middleware(['auth'])->name('pdv.romaneio');
Route::post('/pdv/cancelar', [\App\Http\Controllers\PDVController::class, 'cancelSale'])->middleware(['auth'])->name('pdv.cancelSale');

// Rotas de Orçamentos
Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.index');
Route::get('/quotes/create', [QuoteController::class, 'create'])->name('quotes.create');
Route::post('/quotes', [QuoteController::class, 'store'])->name('quotes.store');
Route::get('/quotes/{quote}', [QuoteController::class, 'show'])->name('quotes.show');
Route::post('/quotes/{quote}/convert', [QuoteController::class, 'convertToSale'])->name('quotes.convert');
Route::get('/quotes/{quote}/pdf', [QuoteController::class, 'generatePdf'])->name('quotes.pdf');

// Rota temporária para recalcular saldo do caixa
Route::get('/debug/recalcular-caixa/{id}', [PDVController::class, 'recalculateCashRegister'])->name('debug.recalcular');

// Rota para debug de NFe
Route::get('/debug/nfe/{id}', function($id) {
    $nfe = \App\Models\Nfe::findOrFail($id);
    return response()->json([
        'id' => $nfe->id,
        'status' => $nfe->status,
        'ref' => $nfe->ref,
        'chave_nfe' => $nfe->chave_nfe,
        'numero_nfe' => $nfe->numero_nfe,
        'serie_nfe' => $nfe->serie_nfe,
        'data_emissao' => $nfe->data_emissao?->format('Y-m-d H:i:s'),
        'limite_24h' => $nfe->data_emissao?->addHours(24)->format('Y-m-d H:i:s'),
        'agora' => now()->format('Y-m-d H:i:s'),
        'pode_cancelar_24h' => $nfe->data_emissao && now()->lessThanOrEqualTo($nfe->data_emissao->addHours(24)),
        'data_devolucao' => $nfe->data_devolucao?->format('Y-m-d H:i:s'),
        'protocolo_devolucao' => $nfe->protocolo_devolucao,
        'status_devolucao' => $nfe->status_devolucao
    ], 200, [], JSON_PRETTY_PRINT);
})->middleware(['auth'])->name('debug.nfe');

// Rota temporária para testar busca de fornecedores
Route::get('/debug/test-suppliers', function(Request $request) {
    $search = $request->get('search', '');
    $cleanSearch = preg_replace('/[^0-9]/', '', $search);
    
    $suppliers = App\Models\Supplier::where('company_id', 8) // Usando company_id fixo para teste
        ->where(function($query) use ($search, $cleanSearch) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('cnpj', 'like', '%' . $search . '%')
                  ->orWhere('cnpj', 'like', '%' . $cleanSearch . '%');
        })
        ->where('status', 'ativo')
        ->orderBy('name')
        ->limit(10)
        ->get();
    
    return response()->json([
        'search' => $search,
        'cleanSearch' => $cleanSearch,
        'count' => $suppliers->count(),
        'suppliers' => $suppliers
    ]);
});

// Rotas específicas para romaneios (AJAX)
Route::post('delivery-receipts/{deliveryReceipt}/items/{item}/toggle', [App\Http\Controllers\DeliveryReceiptController::class, 'toggleItem'])
    ->name('delivery_receipts.toggle_item');
Route::post('delivery-receipts/{deliveryReceipt}/finalize', [App\Http\Controllers\DeliveryReceiptController::class, 'finalize'])
    ->name('delivery_receipts.finalize');
