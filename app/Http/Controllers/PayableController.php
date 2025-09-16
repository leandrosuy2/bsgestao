<?php

namespace App\Http\Controllers;

use App\Models\Payable;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayableController extends Controller
{
    public function __construct()
    {
        $this->middleware('company.access');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Payable::where('company_id', $user->company_id)->with('criador');

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('pessoa')) {
            $query->where('pessoa', 'like', '%' . $request->pessoa . '%');
        }
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }
        if ($request->filled('date_start')) {
            $query->where('data_vencimento', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->where('data_vencimento', '<=', $request->date_end);
        }

        $payables = $query->orderBy('data_vencimento')->paginate(15);

        // Estatísticas
        $totalPendente = Payable::where('company_id', $user->company_id)->where('status', 'pendente')->sum('valor');
        $totalPago = Payable::where('company_id', $user->company_id)->where('status', 'pago')->sum('valor');
        $totalAtrasado = Payable::where('company_id', $user->company_id)->where('status', 'atrasado')->sum('valor');

        return view('payables.index', compact('payables', 'totalPendente', 'totalPago', 'totalAtrasado', 'request'));
    }

    public function create()
    {
        $user = Auth::user();
        
        $employees = Employee::where('company_id', $user->company_id)->where('active', true)->get();
        
        if ($employees->isEmpty()) {
            return redirect()->route('employees.create')
                ->with('warning', 'É necessário ter pelo menos um funcionário cadastrado para criar contas a pagar. Por favor, cadastre um funcionário primeiro.')
                ->with('redirect_to', 'payables.create');
        }

        return view('payables.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'pessoa' => 'required|string|max:255',
            'categoria' => 'required|string|max:100',
            'valor' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'forma_pagamento' => 'required|string|max:100',
            'observacoes' => 'nullable|string',
            'comprovante' => 'nullable|string|max:255',
        ]);
        
        $employee = $this->findOrCreateEmployeeForPayable($user);
        
        // Se não encontrou funcionário, redirecionar para criar funcionário
        if (!$employee) {
            return redirect()->route('employees.create')
                ->with('warning', 'É necessário ter pelo menos um funcionário cadastrado para criar contas a pagar. Por favor, cadastre um funcionário primeiro.')
                ->with('redirect_to', 'payables.create')
                ->with('form_data', $validated);
        }
        
        $validated['criado_por'] = $employee->id;
        $validated['status'] = 'pendente';
        $validated['company_id'] = $user->company_id;

        Payable::create($validated);

        return redirect()->route('payables.index')->with('success', 'Conta a pagar registrada com sucesso!');
    }

    public function show(Payable $payable)
    {
        return view('payables.show', compact('payable'));
    }

    public function edit(Payable $payable)
    {
        $user = Auth::user();

        // Verificar se o payable pertence à empresa do usuário
        if ($payable->company_id !== $user->company_id && $user->role !== 'admin') {
            abort(403, 'Acesso negado. Você só pode editar contas da sua empresa.');
        }

        $employees = Employee::where('company_id', $user->company_id)->where('active', true)->get();
        
        // Verificar se existem funcionários ativos
        if ($employees->isEmpty()) {
            return redirect()->route('employees.create')
                ->with('warning', 'É necessário ter pelo menos um funcionário cadastrado para editar contas a pagar. Por favor, cadastre um funcionário primeiro.')
                ->with('redirect_to', 'payables.edit')
                ->with('payable_id', $payable->id);
        }
        return view('payables.edit', compact('payable', 'employees'));
    }

    public function update(Request $request, Payable $payable)
    {
        $user = Auth::user();

        // Verificar se o payable pertence à empresa do usuário
        if ($payable->company_id !== $user->company_id && $user->role !== 'admin') {
            abort(403, 'Acesso negado. Você só pode atualizar contas da sua empresa.');
        }

        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'pessoa' => 'required|string|max:255',
            'categoria' => 'required|string|max:100',
            'valor' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'data_pagamento' => 'nullable|date',
            'status' => 'required|in:pendente,pago,atrasado',
            'forma_pagamento' => 'required|string|max:100',
            'observacoes' => 'nullable|string',
            'comprovante' => 'nullable|string|max:255',
        ]);

        $payable->update($validated);

        return redirect()->route('payables.index')->with('success', 'Conta a pagar atualizada com sucesso!');
    }

    public function destroy(Payable $payable)
    {
        $user = Auth::user();

        // Verificar se o payable pertence à empresa do usuário
        if ($payable->company_id !== $user->company_id && $user->role !== 'admin') {
            abort(403, 'Acesso negado. Você só pode remover contas da sua empresa.');
        }

        $payable->delete();
        return redirect()->route('payables.index')->with('success', 'Conta a pagar removida com sucesso!');
    }

    public function marcarComoPago(Payable $payable)
    {
        $user = Auth::user();

        // Verificar se o payable pertence à empresa do usuário
        if ($payable->company_id !== $user->company_id && $user->role !== 'admin') {
            abort(403, 'Acesso negado. Você só pode marcar contas da sua empresa como pagas.');
        }

        $payable->update([
            'status' => 'pago',
            'data_pagamento' => now()->toDateString()
        ]);

        return redirect()->route('payables.index')->with('success', 'Conta marcada como paga!');
    }

    /**
     * Encontrar ou criar um funcionário para a empresa
     */
    private function findOrCreateEmployeeForPayable($user)
    {
        // Primeira tentativa: buscar por email
        $employee = Employee::where('company_id', $user->company_id)
                          ->where('email', $user->email)
                          ->where('active', true)
                          ->first();
        
        if ($employee) return $employee;
        
        // Segunda tentativa: buscar por nome similar
        $employee = Employee::where('company_id', $user->company_id)
                          ->where('name', 'like', '%' . $user->name . '%')
                          ->where('active', true)
                          ->first();
        
        if ($employee) return $employee;
        
        // Terceira tentativa: qualquer funcionário ativo
        $employee = Employee::where('company_id', $user->company_id)
                          ->where('active', true)
                          ->first();
        
        if ($employee) return $employee;
        
        // Se chegou aqui, não há funcionários - retornar null para forçar redirecionamento
        return null;
    }
}
