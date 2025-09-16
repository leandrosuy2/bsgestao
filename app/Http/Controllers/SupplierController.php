<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::where('company_id', Auth::user()->company_id)
            ->orderByDesc('id')
            ->paginate(15);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:empresa,pessoa',
            'cnpj' => 'required|string|max:20',
            'name' => 'required|string',
            'status' => 'required|in:ativo,inativo',
            'contact_name' => 'required|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string',
            'contact_site' => 'nullable|string',
            'description' => 'nullable|string',
            'cep' => 'required|string|max:12',
            'address' => 'required|string',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string',
            'neighborhood' => 'required|string',
            'state' => 'required|string|max:40',
            'city' => 'required|string|max:60',
            'country' => 'required|string|max:40',
        ]);
        
        $validated['company_id'] = Auth::user()->company_id;
        Supplier::create($validated);
        
        return redirect()->route('suppliers.index')->with('success', 'Fornecedor cadastrado com sucesso!');
    }

    public function show(Supplier $supplier)
    {
        // Verificar se o fornecedor pertence à empresa do usuário
        if ($supplier->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        // Verificar se o fornecedor pertence à empresa do usuário
        if ($supplier->company_id !== Auth::user()->company_id) {
            abort(403);
        }
        
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        // Verificar se o fornecedor pertence à empresa do usuário
        if ($supplier->company_id !== Auth::user()->company_id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'type' => 'required|in:empresa,pessoa',
            'cnpj' => 'required|string|max:20',
            'name' => 'required|string',
            'status' => 'required|in:ativo,inativo',
            'contact_name' => 'required|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string',
            'contact_site' => 'nullable|string',
            'description' => 'nullable|string',
            'cep' => 'required|string|max:12',
            'address' => 'required|string',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string',
            'neighborhood' => 'required|string',
            'state' => 'required|string|max:40',
            'city' => 'required|string|max:60',
            'country' => 'required|string|max:40',
        ]);
        
        $supplier->update($validated);
        return redirect()->route('suppliers.index')->with('success', 'Fornecedor atualizado com sucesso!');
    }

    public function destroy(Supplier $supplier)
    {
        // Verificar se o fornecedor pertence à empresa do usuário
        if ($supplier->company_id !== Auth::user()->company_id) {
            abort(403);
        }
        
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Fornecedor excluído com sucesso!');
    }

    public function toggleStatus(Supplier $supplier)
    {
        // Verificar se o fornecedor pertence à empresa do usuário
        if ($supplier->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $newStatus = $supplier->status === 'ativo' ? 'inativo' : 'ativo';
        $supplier->update(['status' => $newStatus]);

        return redirect()->route('suppliers.index')
            ->with('success', "Fornecedor {$newStatus} com sucesso!");
    }
}
