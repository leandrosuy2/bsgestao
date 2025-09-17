<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Sale;
use App\Models\Customer;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateSalesReport extends Command
{
    protected $signature = 'sales:report {email} {period=month} {--format=pdf}';
    protected $description = 'Gera relatório de vendas para um usuário específico';

    public function handle()
    {
        $email = $this->argument('email');
        $period = $this->argument('period');
        $format = $this->option('format');

        $this->info("Gerando relatório de vendas para: {$email}");
        $this->info("Período: {$period}");
        $this->info("Formato: {$format}");

        // Buscar usuário
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuário não encontrado com o email: {$email}");
            return 1;
        }

        $this->info("Usuário encontrado: {$user->name}");

        // Definir períodos
        $periods = $this->getPeriods($period);
        
        // Buscar dados de vendas
        $salesData = $this->getSalesData($user, $periods);
        $salesByCustomer = $this->getSalesByCustomer($user, $periods);

        $this->info("Total de vendas: R$ " . number_format($salesData['total'], 2, ',', '.'));
        $this->info("Número de vendas: " . $salesData['count']);
        $this->info("Clientes atendidos: " . $salesByCustomer->count());

        if ($format === 'pdf') {
            $this->generatePdf($user, $periods, $salesData, $salesByCustomer);
        } else {
            $this->displayReport($user, $periods, $salesData, $salesByCustomer);
        }

        return 0;
    }

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

    private function generatePdf($user, $periods, $salesData, $salesByCustomer)
    {
        $period = $this->argument('period');
        
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

        $pdf = Pdf::loadView('sales_reports.pdf.user_report', $data);
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'relatorio_vendas_' . str_replace('@', '_', $user->email) . '_' . $period . '_' . now()->format('Y-m-d') . '.pdf';
        $filepath = storage_path('app/' . $filename);
        
        $pdf->save($filepath);
        
        $this->info("PDF gerado com sucesso: {$filepath}");
    }

    private function displayReport($user, $periods, $salesData, $salesByCustomer)
    {
        $this->info("\n=== RELATÓRIO DE VENDAS ===");
        $this->info("Usuário: {$user->name} ({$user->email})");
        $this->info("Período: {$periods['label']}");
        $this->info("Total de vendas: R$ " . number_format($salesData['total'], 2, ',', '.'));
        $this->info("Número de vendas: " . $salesData['count']);
        $this->info("Ticket médio: R$ " . number_format($salesData['count'] > 0 ? $salesData['total'] / $salesData['count'] : 0, 2, ',', '.'));
        $this->info("Clientes atendidos: " . $salesByCustomer->count());
        
        if ($salesByCustomer->count() > 0) {
            $this->info("\n=== VENDAS POR CLIENTE ===");
            foreach ($salesByCustomer as $customer) {
                $this->info("- {$customer['customer']}: R$ " . number_format($customer['total'], 2, ',', '.') . " ({$customer['count']} vendas)");
            }
        }
    }
}
