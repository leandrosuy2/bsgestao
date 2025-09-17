<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('company.access');
    }

    /**
     * Exibe o formulário de relatório de vendas
     */
    public function index()
    {
        return view('sales_reports.index');
    }

    /**
     * Gera relatório de vendas por usuário específico
     */
    public function userSalesReport(Request $request)
    {
        $request->validate([
            'user_email' => 'required|email',
            'period' => 'required|in:week,month,year',
            'format' => 'in:html,pdf'
        ]);

        $userEmail = $request->user_email;
        $period = $request->period;
        $format = $request->format ?? 'html';

        // Buscar usuário pelo email
        $user = User::where('email', $userEmail)->first();
        
        if (!$user) {
            return back()->with('error', 'Usuário não encontrado com o email: ' . $userEmail);
        }

        // Definir períodos
        $periods = $this->getPeriods($period);
        
        // Buscar dados de vendas
        $salesData = $this->getSalesData($user, $periods);
        
        // Buscar vendas por cliente
        $salesByCustomer = $this->getSalesByCustomer($user, $periods);

        $data = [
            'user' => $user,
            'period' => $period,
            'periods' => $periods,
            'salesData' => $salesData,
            'salesByCustomer' => $salesByCustomer,
            'totalSales' => $salesData['total'],
            'totalSalesCount' => $salesData['count'],
            'averageTicket' => $salesData['count'] > 0 ? $salesData['total'] / $salesData['count'] : 0,
        ];

        if ($format === 'pdf') {
            return $this->generatePdf($data);
        }

        return view('sales_reports.user_report', $data);
    }

    /**
     * Gera relatório específico para guabinorte1@gmail.com
     */
    public function guabinorteReport(Request $request)
    {
        $request->merge(['user_email' => 'guabinorte1@gmail.com']);
        return $this->userSalesReport($request);
    }

    /**
     * Define os períodos baseado na seleção
     */
    private function getPeriods($period)
    {
        $now = Carbon::now();
        
        switch ($period) {
            case 'week':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek(),
                    'label' => 'Semana Atual (' . $now->copy()->startOfWeek()->format('d/m/Y') . ' - ' . $now->copy()->endOfWeek()->format('d/m/Y') . ')'
                ];
            case 'month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                    'label' => 'Mês Atual (' . $now->copy()->startOfMonth()->format('d/m/Y') . ' - ' . $now->copy()->endOfMonth()->format('d/m/Y') . ')'
                ];
            case 'year':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear(),
                    'label' => 'Ano Atual (' . $now->copy()->startOfYear()->format('d/m/Y') . ' - ' . $now->copy()->endOfYear()->format('d/m/Y') . ')'
                ];
        }
    }

    /**
     * Busca dados de vendas do usuário no período
     */
    private function getSalesData($user, $periods)
    {
        $query = Sale::where('user_id', $user->id)
                    ->where('company_id', $user->company_id)
                    ->where('status', 'completed')
                    ->whereBetween('sold_at', [$periods['start'], $periods['end']]);

        $total = $query->sum('final_total');
        $count = $query->count();

        // Vendas por dia no período
        $salesByDay = Sale::where('user_id', $user->id)
                         ->where('company_id', $user->company_id)
                         ->where('status', 'completed')
                         ->whereBetween('sold_at', [$periods['start'], $periods['end']])
                         ->selectRaw('DATE(sold_at) as date, SUM(final_total) as total, COUNT(*) as count')
                         ->groupBy('date')
                         ->orderBy('date')
                         ->get();

        // Vendas por forma de pagamento
        $salesByPayment = Sale::where('user_id', $user->id)
                             ->where('company_id', $user->company_id)
                             ->where('status', 'completed')
                             ->whereBetween('sold_at', [$periods['start'], $periods['end']])
                             ->selectRaw('payment_mode, SUM(final_total) as total, COUNT(*) as count')
                             ->groupBy('payment_mode')
                             ->get();

        return [
            'total' => $total,
            'count' => $count,
            'byDay' => $salesByDay,
            'byPayment' => $salesByPayment
        ];
    }

    /**
     * Busca vendas agrupadas por cliente
     */
    private function getSalesByCustomer($user, $periods)
    {
        return Sale::where('user_id', $user->id)
                  ->where('company_id', $user->company_id)
                  ->where('status', 'completed')
                  ->whereBetween('sold_at', [$periods['start'], $periods['end']])
                  ->with('customer')
                  ->selectRaw('customer_id, SUM(final_total) as total, COUNT(*) as count')
                  ->groupBy('customer_id')
                  ->orderByDesc('total')
                  ->get()
                  ->map(function ($sale) {
                      return [
                          'customer' => $sale->customer ? $sale->customer->name : 'Cliente não informado',
                          'total' => $sale->total,
                          'count' => $sale->count
                      ];
                  });
    }

    /**
     * Gera PDF do relatório
     */
    private function generatePdf($data)
    {
        $pdf = Pdf::loadView('sales_reports.pdf.user_report', $data);
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'relatorio_vendas_' . $data['user']->email . '_' . $data['period'] . '_' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * API para buscar dados de vendas via AJAX
     */
    public function getSalesDataApi(Request $request)
    {
        $request->validate([
            'user_email' => 'required|email',
            'period' => 'required|in:week,month,year'
        ]);

        $user = User::where('email', $request->user_email)->first();
        
        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }

        $periods = $this->getPeriods($request->period);
        $salesData = $this->getSalesData($user, $periods);
        $salesByCustomer = $this->getSalesByCustomer($user, $periods);

        return response()->json([
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'company' => $user->company->name ?? 'N/A'
            ],
            'period' => $periods['label'],
            'sales' => $salesData,
            'customers' => $salesByCustomer
        ]);
    }
}
