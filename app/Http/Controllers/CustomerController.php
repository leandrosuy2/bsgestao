<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::where('company_id', Auth::user()->company_id)
            ->orderBy('name')
            ->paginate(20);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:pessoa_fisica,pessoa_juridica',
            'cpf_cnpj' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('customers')->where(function ($query) {
                    return $query->where('company_id', Auth::user()->company_id);
                })
            ],
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'postal_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'active' => 'boolean'
        ]);

        $customer = Customer::create([
            'company_id' => Auth::user()->company_id,
            'name' => $request->name,
            'type' => $request->type,
            'cpf_cnpj' => $request->cpf_cnpj,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'notes' => $request->notes,
            'active' => $request->active ?? true
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Cliente criado com sucesso!');
    }

    public function show(Customer $customer)
    {
        // Verificar se o cliente pertence à empresa do usuário
        if ($customer->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        // Verificar se o cliente pertence à empresa do usuário
        if ($customer->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        // Verificar se o cliente pertence à empresa do usuário
        if ($customer->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:pessoa_fisica,pessoa_juridica',
            'cpf_cnpj' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('customers')->where(function ($query) {
                    return $query->where('company_id', Auth::user()->company_id);
                })->ignore($customer->id)
            ],
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'number' => 'nullable|string|max:20',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'postal_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'active' => 'boolean'
        ]);

        $customer->update([
            'name' => $request->name,
            'type' => $request->type,
            'cpf_cnpj' => $request->cpf_cnpj,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'number' => $request->number,
            'neighborhood' => $request->neighborhood,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'notes' => $request->notes,
            'active' => $request->active ?? true
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Customer $customer)
    {
        // Verificar se o cliente pertence à empresa do usuário
        if ($customer->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Cliente excluído com sucesso!');
    }

    public function toggleStatus(Customer $customer)
    {
        // Verificar se o cliente pertence à empresa do usuário
        if ($customer->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $customer->update(['active' => !$customer->active]);

        $status = $customer->active ? 'ativado' : 'desativado';
        return redirect()->route('customers.index')
            ->with('success', "Cliente {$status} com sucesso!");
    }
}
