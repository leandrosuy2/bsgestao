<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\StockMovement;
use App\Models\Payable;
use App\Models\Receivable;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('company.access');
    }

    public function index()
    {
        $user = Auth::user();

        // Se for admin, mostrar estatísticas de todas as empresas
        if ($user->id == 1) {
            $totalProducts = Product::count();
            $totalCategories = Category::count();
            $totalEmployees = Employee::count();
        } else {
            // Estatísticas gerais
            $totalProducts = Product::forCurrentCompany()->count();
            $totalCategories = Category::where('company_id', $user->company_id)->count();
            $totalEmployees = Employee::where('company_id', $user->company_id)->count();
        }

        // Estatísticas de estoque
        if ($user->id == 1) {
            $productsWithLowStock = Product::whereColumn('stock_quantity', '<=', 'min_stock')->count();
            $totalStockValue = Product::sum(DB::raw('cost_price * stock_quantity'));

            // Estatísticas financeiras
            $totalPayable = Payable::where('status', 'pendente')->sum('valor');
            $totalReceivable = Receivable::where('status', 'pendente')->sum('valor');
            $totalPaid = Payable::where('status', 'pago')->sum('valor');
            $totalReceived = Receivable::where('status', 'recebido')->sum('valor');
        } else {
            $productsWithLowStock = Product::where('company_id', $user->company_id)
                                          ->whereColumn('stock_quantity', '<=', 'min_stock')->count();
                        // Cálculo explícito do valor do estoque apenas da empresa do usuário
            $totalStockValue = Product::where('company_id', $user->company_id)
                                     ->sum(DB::raw('cost_price * stock_quantity'));

            // Estatísticas financeiras
            $totalPayable = Payable::where('company_id', $user->company_id)
                                  ->where('status', 'pendente')->sum('valor');
            $totalReceivable = Receivable::where('company_id', $user->company_id)
                                        ->where('status', 'pendente')->sum('valor');
            $totalPaid = Payable::where('company_id', $user->company_id)
                               ->where('status', 'pago')->sum('valor');
            $totalReceived = Receivable::where('company_id', $user->company_id)
                                      ->where('status', 'recebido')->sum('valor');
        }

        // Para admin, mostrar dashboard simplificado
        if ($user->id == 1) {
            $recentMovements = collect();
            $topProducts = collect();
            $movementsByMonth = collect([['month' => now()->month, 'year' => now()->year, 'total' => 0]]);
            $payablesByStatus = collect([['status' => 'Nenhum', 'total' => 0, 'valor_total' => 0]]);
            $receivablesByStatus = collect([['status' => 'Nenhum', 'total' => 0, 'valor_total' => 0]]);
            $cashFlow = ['days' => [], 'payables' => [], 'receivables' => []];
            $lowStockProducts = collect();
            $upcomingPayables = collect();
            $upcomingReceivables = collect();
            $topCategories = collect();
        } else {
            // Movimentações recentes
            $recentMovements = StockMovement::whereHas('product', function($query) use ($user) {
                                    $query->where('company_id', $user->company_id);
                                })
                                ->with('product')
                                ->orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();

            // Produtos mais movimentados (últimos 30 dias)
            $topProducts = StockMovement::select('product_id', DB::raw('SUM(quantity) as total_movimentado'))
                ->whereHas('product', function($query) use ($user) {
                    $query->where('company_id', $user->company_id);
                })
                ->with('product')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('product_id')
                ->orderBy('total_movimentado', 'desc')
                ->limit(5)
                ->get();

            // Gráfico de movimentações por mês (últimos 6 meses)
            $movementsByMonth = StockMovement::select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('COUNT(*) as total')
                )
                ->whereHas('product', function($query) use ($user) {
                    $query->where('company_id', $user->company_id);
                })
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            // Gráfico de contas a pagar por status
            $payablesByStatus = Payable::where('company_id', $user->company_id)
                ->select('status', DB::raw('COUNT(*) as total'), DB::raw('SUM(valor) as valor_total'))
                ->groupBy('status')
                ->get();

            // Gráfico de contas a receber por status
            $receivablesByStatus = Receivable::where('company_id', $user->company_id)
                ->select('status', DB::raw('COUNT(*) as total'), DB::raw('SUM(valor) as valor_total'))
                ->groupBy('status')
                ->get();

            // Fluxo de caixa (últimos 30 dias)
            $cashFlow = $this->getCashFlow();
            if (empty($cashFlow['days'])) $cashFlow['days'] = [];
            if (empty($cashFlow['payables'])) $cashFlow['payables'] = [];
            if (empty($cashFlow['receivables'])) $cashFlow['receivables'] = [];

            if ($movementsByMonth->isEmpty()) {
                $movementsByMonth = collect([['month' => now()->month, 'year' => now()->year, 'total' => 0]]);
            }
            if ($payablesByStatus->isEmpty()) {
                $payablesByStatus = collect([['status' => 'Nenhum', 'total' => 0, 'valor_total' => 0]]);
            }
            if ($receivablesByStatus->isEmpty()) {
                $receivablesByStatus = collect([['status' => 'Nenhum', 'total' => 0, 'valor_total' => 0]]);
            }

            // Produtos com estoque baixo (comparando stock_quantity com min_stock)
            $lowStockProducts = Product::where('company_id', $user->company_id)
                ->whereColumn('stock_quantity', '<=', 'min_stock')
                ->get();

            $productsWithLowStock = $lowStockProducts->count();

            // Contas vencendo nos próximos 7 dias
            $upcomingPayables = Payable::where('company_id', $user->company_id)
                ->where('status', 'pendente')
                ->whereBetween('data_vencimento', [Carbon::now(), Carbon::now()->addDays(7)])
                ->orderBy('data_vencimento')
                ->limit(5)
                ->get();

            $upcomingReceivables = Receivable::where('company_id', $user->company_id)
                ->where('status', 'pendente')
                ->whereBetween('data_vencimento', [Carbon::now(), Carbon::now()->addDays(7)])
                ->limit(5)
                ->get();

            // Categorias mais utilizadas
            $topCategories = Category::where('company_id', $user->company_id)
                ->withCount(['products' => function($query) use ($user) {
                    $query->where('company_id', $user->company_id);
                }])
                ->orderBy('products_count', 'desc')
                ->limit(5)
                ->get();
        }

        // Se for admin, mostrar dashboard admin1istrativo
        if ($user->id == 1) {
            // Estatísticas para admin1
            $totalCompanies = \App\Models\Company::count();
            $activeCompanies = \App\Models\Company::where('is_active', true)->count();
            $trialCompanies = \App\Models\Company::where('trial_end', '>', now())->count();
            $expiredTrials = \App\Models\Company::where('trial_end', '<', now())->count();
            $blockedCompanies = \App\Models\Company::where('is_active', false)->count();
            $paidCompanies = \App\Models\Company::whereNotNull('paid_until')->count();
            $totalUsers = \App\Models\User::where('role', '!=', 'admin1')->count();
            $activeUsers = \App\Models\User::where('role', '!=', 'admin1')->whereNotNull('company_id')->count();

            $companies = \App\Models\Company::withCount('users')->orderBy('created_at', 'desc')->get();

            return view('dashboard.admin', compact(
                'totalCompanies',
                'activeCompanies',
                'trialCompanies',
                'expiredTrials',
                'blockedCompanies',
                'paidCompanies',
                'totalUsers',
                'activeUsers',
                'companies'
            ));
        }

        return view('dashboard.index', compact(
            'totalProducts',
            'totalCategories',
            'totalEmployees',
            'productsWithLowStock',
            'totalStockValue',
            'totalPayable',
            'totalReceivable',
            'totalPaid',
            'totalReceived',
            'recentMovements',
            'topProducts',
            'movementsByMonth',
            'payablesByStatus',
            'receivablesByStatus',
            'cashFlow',
            'lowStockProducts',
            'upcomingPayables',
            'upcomingReceivables',
            'topCategories'
        ));
    }

    private function getCashFlow()
    {
        $user = Auth::user();
        $days = [];
        $payables = [];
        $receivables = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('d/m');

            if ($user->id == 1) {
                $payables[] = Payable::where('status', 'pago')
                    ->whereDate('data_pagamento', $date)
                    ->sum('valor');

                $receivables[] = Receivable::where('status', 'recebido')
                    ->whereDate('data_recebimento', $date)
                    ->sum('valor');
            } else {
                $payables[] = Payable::where('company_id', $user->company_id)
                    ->where('status', 'pago')
                    ->whereDate('data_pagamento', $date)
                    ->sum('valor');

                $receivables[] = Receivable::where('company_id', $user->company_id)
                    ->where('status', 'recebido')
                    ->whereDate('data_recebimento', $date)
                    ->sum('valor');
            }
        }

        return [
            'days' => $days,
            'payables' => $payables,
            'receivables' => $receivables
        ];
    }
}
