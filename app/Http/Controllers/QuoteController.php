<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class QuoteController extends Controller
{
    public function index()
    {
        $quotes = Quote::where('user_id', Auth::id())
            ->with(['user', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('quotes.index', compact('quotes'));
    }

    public function create()
    {
        $products = Product::where('company_id', Auth::user()->company_id)->get();
        $lastQuoteId = Quote::where('company_id', Auth::user()->company_id)->max('id') ?? 0;
        
        // Buscar dados da empresa do usuário logado
        $userCompany = Auth::user()->company;
        
        return view('quotes.create', compact('products', 'lastQuoteId', 'userCompany'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'nullable|email',
                'customer_phone' => 'nullable|string|max:20',
                'company_name' => 'nullable|string|max:255',
                'company_subtitle' => 'nullable|string|max:255',
                'quote_number' => 'nullable|string|max:50',
                'payment_terms' => 'nullable|string',
                'delivery_time' => 'nullable|string|max:255',
                'pix_key' => 'nullable|string|max:255',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.qtd' => 'required|integer|min:1',
                'items.*.unitario' => 'required|numeric|min:0.01',
                'discount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'valid_until' => 'nullable|date|after_or_equal:today',
            ]);

            $quote = null;
            DB::transaction(function () use ($request, &$quote) {
                $total = 0;
                foreach ($request->items as $item) {
                    $total += $item['unitario'] * $item['qtd'];
                }

                $discount = $request->discount ?? 0;
                $finalTotal = $total - $discount;

                $quote = Quote::create([
                    'user_id' => Auth::id(),
                    'company_id' => Auth::user()->company_id,
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'company_name' => $request->company_name,
                    'company_subtitle' => $request->company_subtitle,
                    'quote_number' => $request->quote_number,
                    'payment_terms' => $request->payment_terms,
                    'delivery_time' => $request->delivery_time,
                    'pix_key' => $request->pix_key,
                    'total' => $total,
                    'discount' => $discount,
                    'final_total' => $finalTotal,
                    'status' => 'draft',
                    'valid_until' => $request->valid_until,
                    'notes' => $request->notes,
                ]);

                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    
                    QuoteItem::create([
                        'quote_id' => $quote->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $item['qtd'],
                        'unit_price' => $item['unitario'],
                        'total_price' => $item['unitario'] * $item['qtd'],
                    ]);
                }
            });

            return redirect()->route('quotes.show', $quote)->with('success', 'Orçamento criado com sucesso!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Erro ao criar orçamento: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Erro ao criar orçamento: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Quote $quote)
    {
        $quote->load(['items.product', 'user']);
        return view('quotes.show', compact('quote'));
    }

    public function convertToSale(Quote $quote)
    {
        $register = CashRegister::where('user_id', Auth::id())
            ->where('status', 'open')
            ->latest()
            ->first();

        if (!$register) {
            return response()->json(['error' => 'Abra um caixa antes de converter o orçamento.'], 400);
        }

        $sale = null;
        DB::transaction(function () use ($quote, $register, &$sale) {
            $sale = Sale::create([
                'cash_register_id' => $register->id,
                'user_id' => Auth::id(),
                'total' => $quote->total,
                'discount' => $quote->discount,
                'final_total' => $quote->final_total,
                'status' => 'completed',
                'sold_at' => now(),
            ]);

            foreach ($quote->items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                ]);
            }

            $register->movements()->create([
                'user_id' => Auth::id(),
                'type' => 'sale',
                'amount' => $quote->final_total,
                'description' => 'Venda de Orçamento #' . $quote->id . ' -> Venda #' . $sale->id,
            ]);

            $quote->update(['status' => 'accepted']);
        });

        return response()->json(['success' => true, 'sale_id' => $sale->id]);
    }

    public function generatePdf(Quote $quote)
    {
        $quote->load(['items.product', 'user']);

        // Determina qual template usar baseado na presença dos novos campos
        $templateView = $quote->company_name ? 'quotes.pdf' : 'quotes.pdf-old';

        $pdf = Pdf::loadView($templateView, compact('quote'))
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'dpi' => 120,
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => false,
                'isPhpEnabled' => false,
                'chroot' => public_path(),
                'enable_php' => false,
                'enable_css_float' => true,
                'enable_html5_parser' => true,
                'debugCss' => false,
                'debugKeepTemp' => false
            ]);

        $filename = 'orcamento_' . str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $quote->quote_number ?? $quote->id) . '.pdf';
        
        return $pdf->stream($filename);
    }
}
