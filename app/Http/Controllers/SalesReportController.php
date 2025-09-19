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
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'in:html,pdf'
        ]);

        $userEmail = $request->user_email;
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $format = $request->format ?? 'html';

        // Buscar usuário pelo email
        $user = User::where('email', $userEmail)->first();
        
        if (!$user) {
            return back()->with('error', 'Usuário não encontrado com o email: ' . $userEmail);
        }

        // Definir períodos
        $periods = [
            'start' => $startDate,
            'end' => $endDate,
            'label' => 'Período: ' . $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y')
        ];
        
        // Buscar dados de vendas
        $salesData = $this->getSalesData($user, $periods);
        
        // Buscar vendas por cliente
        $salesByCustomer = $this->getSalesByCustomer($user, $periods);

        $data = [
            'user' => $user,
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
        // Se não foram fornecidas datas, usar período padrão (últimos 30 dias)
        if (!$request->has('start_date') || !$request->has('end_date')) {
            $request->merge([
                'user_email' => 'guabinorte1@gmail.com',
                'start_date' => Carbon::now()->subDays(30)->format('Y-m-d'),
                'end_date' => Carbon::now()->format('Y-m-d')
            ]);
        } else {
            $request->merge(['user_email' => 'guabinorte1@gmail.com']);
        }
        
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
        // Base query para evitar repetição
        $baseQuery = Sale::where('user_id', $user->id)
                        ->where('company_id', $user->company_id)
                        ->where('status', 'completed')
                        ->whereBetween('sold_at', [$periods['start'], $periods['end']]);

        // Calcular totais
        $total = $baseQuery->sum('final_total');
        $count = $baseQuery->count();

        // Vendas por dia no período
        $salesByDay = (clone $baseQuery)
                         ->selectRaw('DATE(sold_at) as date, SUM(final_total) as total, COUNT(*) as count')
                         ->groupBy('date')
                         ->orderBy('date')
                         ->get()
                         ->map(function($item) {
                             return [
                                 'date' => $item->date,
                                 'total' => (float) $item->total,
                                 'count' => (int) $item->count
                             ];
                         });

        // Vendas por forma de pagamento
        $salesByPayment = (clone $baseQuery)
                             ->selectRaw('payment_mode, SUM(final_total) as total, COUNT(*) as count')
                             ->groupBy('payment_mode')
                             ->get()
                             ->map(function($item) {
                                 return [
                                     'payment_mode' => $item->payment_mode,
                                     'total' => (float) $item->total,
                                     'count' => (int) $item->count
                                 ];
                             });

        return [
            'total' => (float) $total,
            'count' => (int) $count,
            'byDay' => $salesByDay,
            'byPayment' => $salesByPayment
        ];
    }

    /**
     * Busca vendas agrupadas por cliente
     */
    private function getSalesByCustomer($user, $periods)
    {
        $salesByCustomer = Sale::where('user_id', $user->id)
                  ->where('company_id', $user->company_id)
                  ->where('status', 'completed')
                  ->whereBetween('sold_at', [$periods['start'], $periods['end']])
                  ->selectRaw('customer_id, SUM(final_total) as total, COUNT(*) as count')
                  ->groupBy('customer_id')
                  ->orderByDesc('total')
                  ->get();

        // Buscar dados dos clientes separadamente para evitar problemas de relacionamento
        $customerIds = $salesByCustomer->pluck('customer_id')->filter()->unique();
        $customers = Customer::whereIn('id', $customerIds)->get()->keyBy('id');

        return $salesByCustomer->map(function ($sale) use ($customers) {
            $customerName = 'Cliente não informado';
            if ($sale->customer_id && isset($customers[$sale->customer_id])) {
                $customerName = $customers[$sale->customer_id]->name;
            }
            
            return [
                'customer' => $customerName,
                'total' => (float) $sale->total,
                'count' => (int) $sale->count
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
        
        $startDate = $data['periods']['start']->format('Y-m-d');
        $endDate = $data['periods']['end']->format('Y-m-d');
        $filename = 'relatorio_vendas_' . $data['user']->email . '_' . $startDate . '_' . $endDate . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * API para buscar dados de vendas via AJAX
     */
    public function getSalesDataApi(Request $request)
    {
        $request->validate([
            'user_email' => 'required|email',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $user = User::where('email', $request->user_email)->first();
        
        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        
        $periods = [
            'start' => $startDate,
            'end' => $endDate,
            'label' => 'Período: ' . $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y')
        ];
        
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
