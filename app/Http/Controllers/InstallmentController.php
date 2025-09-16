<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InstallmentController extends Controller
{
    /**
     * Display a listing of installment sales.
     */
    public function index()
    {
        $sales = Sale::where('user_id', Auth::id())
            ->where('payment_mode', 'installment')
            ->with(['user', 'payments'])
            ->orderBy('installment_due_date', 'asc')
            ->paginate(20);

        // Estatísticas
        $totalInstallments = Sale::where('user_id', Auth::id())
            ->where('payment_mode', 'installment')
            ->count();

        $totalAmount = Sale::where('user_id', Auth::id())
            ->where('payment_mode', 'installment')
            ->sum('final_total');

        $dueSoon = Sale::where('user_id', Auth::id())
            ->dueSoon(7)
            ->count();

        $overdue = Sale::where('user_id', Auth::id())
            ->overdue()
            ->count();

        return view('pdv.installments', compact(
            'sales',
            'totalInstallments',
            'totalAmount',
            'dueSoon',
            'overdue'
        ));
    }

    /**
     * Show the form for editing the specified installment.
     */
    public function edit($id)
    {
        $sale = Sale::where('user_id', Auth::id())
            ->where('id', $id)
            ->where('payment_mode', 'installment')
            ->firstOrFail();

        return view('pdv.edit-installment', compact('sale'));
    }

    /**
     * Update the specified installment.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'installment_due_date' => 'required|date|after:today',
            'installment_notes' => 'nullable|string|max:500',
        ]);

        $sale = Sale::where('user_id', Auth::id())
            ->where('id', $id)
            ->where('payment_mode', 'installment')
            ->firstOrFail();

        $sale->update([
            'installment_due_date' => $request->installment_due_date,
            'installment_notes' => $request->installment_notes,
        ]);

        return redirect()->route('installments.index')
            ->with('success', 'Pagamento a prazo atualizado com sucesso!');
    }

    /**
     * Mark installment as paid.
     */
    public function markAsPaid($id)
    {
        $sale = Sale::where('user_id', Auth::id())
            ->where('id', $id)
            ->where('payment_mode', 'installment')
            ->firstOrFail();

        // Atualizar pagamentos a prazo para dinheiro
        $sale->payments()->where('payment_type', 'prazo')->update([
            'payment_type' => 'dinheiro'
        ]);

        // Atualizar modo de pagamento
        $sale->update([
            'payment_mode' => 'cash'
        ]);

        return redirect()->route('installments.index')
            ->with('success', 'Pagamento marcado como pago!');
    }

    /**
     * Get overdue installments.
     */
    public function overdue()
    {
        $sales = Sale::where('user_id', Auth::id())
            ->overdue()
            ->with(['user', 'payments'])
            ->orderBy('installment_due_date', 'asc')
            ->paginate(20);

        return view('pdv.overdue-installments', compact('sales'));
    }

    /**
     * Get installments due soon.
     */
    public function dueSoon($days = 7)
    {
        $sales = Sale::where('user_id', Auth::id())
            ->dueSoon($days)
            ->with(['user', 'payments'])
            ->orderBy('installment_due_date', 'asc')
            ->paginate(20);

        return view('pdv.due-soon-installments', compact('sales', 'days'));
    }

    /**
     * Get installment statistics.
     */
    public function statistics()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $stats = [
            'total_installments' => Sale::where('user_id', Auth::id())
                ->where('payment_mode', 'installment')
                ->count(),
            
            'total_amount' => Sale::where('user_id', Auth::id())
                ->where('payment_mode', 'installment')
                ->sum('final_total'),
            
            'overdue_count' => Sale::where('user_id', Auth::id())
                ->overdue()
                ->count(),
            
            'overdue_amount' => Sale::where('user_id', Auth::id())
                ->overdue()
                ->sum('final_total'),
            
            'due_today' => Sale::where('user_id', Auth::id())
                ->where('payment_mode', 'installment')
                ->whereDate('installment_due_date', $today)
                ->count(),
            
            'due_this_week' => Sale::where('user_id', Auth::id())
                ->dueSoon(7)
                ->count(),
            
            'due_this_month' => Sale::where('user_id', Auth::id())
                ->where('payment_mode', 'installment')
                ->whereMonth('installment_due_date', $today->month)
                ->whereYear('installment_due_date', $today->year)
                ->count(),
            
            'this_month_amount' => Sale::where('user_id', Auth::id())
                ->where('payment_mode', 'installment')
                ->whereDate('sold_at', '>=', $thisMonth)
                ->sum('final_total'),
            
            'last_month_amount' => Sale::where('user_id', Auth::id())
                ->where('payment_mode', 'installment')
                ->whereDate('sold_at', '>=', $lastMonth)
                ->whereDate('sold_at', '<', $thisMonth)
                ->sum('final_total'),
        ];

        return response()->json($stats);
    }

    /**
     * Export installments to CSV.
     */
    public function export(Request $request)
    {
        $query = Sale::where('user_id', Auth::id())
            ->where('payment_mode', 'installment')
            ->with(['user', 'payments']);

        if ($request->date_from) {
            $query->whereDate('sold_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('sold_at', '<=', $request->date_to);
        }

        if ($request->status === 'overdue') {
            $query->overdue();
        } elseif ($request->status === 'due_soon') {
            $query->dueSoon(7);
        }

        $sales = $query->orderBy('installment_due_date', 'asc')->get();

        $filename = 'vendas_prazo_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($sales) {
            $file = fopen('php://output', 'w');
            
            // Cabeçalho
            fputcsv($file, [
                'ID',
                'Data da Venda',
                'Valor Total',
                'Valor a Prazo',
                'Data Vencimento',
                'Status',
                'Vendedor',
                'Observações'
            ]);

            // Dados
            foreach ($sales as $sale) {
                $status = $sale->isOverdue() ? 'Vencida' : 
                         ($sale->getDaysUntilDue() <= 7 ? 'Vencendo' : 'Normal');

                fputcsv($file, [
                    $sale->id,
                    $sale->sold_at->format('d/m/Y H:i'),
                    number_format($sale->final_total, 2, ',', '.'),
                    number_format($sale->getInstallmentAmount(), 2, ',', '.'),
                    $sale->installment_due_date ? $sale->installment_due_date->format('d/m/Y') : '',
                    $status,
                    $sale->user->name,
                    $sale->installment_notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
