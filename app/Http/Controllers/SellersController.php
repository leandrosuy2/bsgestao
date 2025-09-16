<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;

class SellersController extends Controller
{
    public function index()
    {
        $sellers = Seller::forCurrentCompany()
            ->orderBy('name')
            ->paginate(20);
            
        return view('sellers.index', compact('sellers'));
    }

    public function create()
    {
        return view('sellers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'document' => 'nullable|string|max:20',
            'commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        $validated['company_id'] = auth()->user()->company_id;
        
        Seller::create($validated);

        return redirect()
            ->route('sellers.index')
            ->with('success', 'Vendedor cadastrado com sucesso!');
    }

    public function show(Seller $seller)
    {
        if ($seller->company_id !== auth()->user()->company_id) {
            abort(403);
        }
        return view('sellers.show', compact('seller'));
    }

    public function edit(Seller $seller)
    {
        if ($seller->company_id !== auth()->user()->company_id) {
            abort(403);
        }
        return view('sellers.edit', compact('seller'));
    }

    public function update(Request $request, Seller $seller)
    {
        if ($seller->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'document' => 'nullable|string|max:20',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'active' => 'boolean'
        ]);

        $seller->update($validated);

        return redirect()
            ->route('sellers.index')
            ->with('success', 'Vendedor atualizado com sucesso!');
    }

    public function destroy(Seller $seller)
    {
        if ($seller->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $seller->delete();

        return redirect()
            ->route('sellers.index')
            ->with('success', 'Vendedor removido com sucesso!');
    }

    public function commissions(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $sellers = Seller::forCurrentCompany()
            ->where('active', true)
            ->get()
            ->map(function($seller) use ($startDate, $endDate) {
                $commissions = $seller->calculateCommissions($startDate, $endDate);
                return [
                    'seller' => $seller,
                    'total_commission' => $commissions->total_commission ?? 0,
                    'total_sales' => $commissions->total_sales ?? 0
                ];
            });

        return view('sellers.commissions', compact('sellers', 'startDate', 'endDate'));
    }
}
