<?php

namespace App\Http\Controllers;

use App\Models\Receivable;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceivableController extends Controller
{
    public function __construct()
    {
        $this->middleware('company.access');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Receivable::where('company_id', $user->company_id)->with('criador');

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

        $receivables = $query->orderBy('data_vencimento')->paginate(15);

        // Estatísticas
        $totalPendente = Receivable::where('company_id', $user->company_id)->where('status', 'pendente')->sum('valor');
        $totalRecebido = Receivable::where('company_id', $user->company_id)->where('status', 'recebido')->sum('valor');
        $totalAtrasado = Receivable::where('company_id', $user->company_id)->where('status', 'atrasado')->sum('valor');

        return view('receivables.index', compact('receivables', 'totalPendente', 'totalRecebido', 'totalAtrasado', 'request'));
    }

    public function create()
    {
        $user = Auth::user();
        $employees = Employee::where('company_id', $user->company_id)->where('active', true)->get();
        return view('receivables.create', compact('employees'));
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
            'forma_recebimento' => 'required|string|max:100',
            'observacoes' => 'nullable|string',
            'comprovante' => 'nullable|string|max:255',
        ]);

        // Tentar buscar o employee associado pelo email
        $employee = $user->getEmployeeByEmail();
        
        // Se não encontrar pelo email, buscar o primeiro employee ativo da empresa
        if (!$employee) {
            $employee = Employee::where('company_id', $user->company_id)
                             ->where('active', true)
                             ->first();
        }
        
        if (!$employee) {
            return back()->withErrors([
                'criado_por' => 'Não foi possível encontrar um funcionário ativo para esta empresa. É necessário cadastrar pelo menos um funcionário ativo.'
            ])->withInput();
        }
        
        $validated['criado_por'] = $employee->id;
        $validated['status'] = 'pendente';
        $validated['company_id'] = $user->company_id;
        
        try {
            Receivable::create($validated);
            return redirect()->route('receivables.index')->with('success', 'Conta a receber registrada com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao criar receivable', [
                'user_id' => $user->id,
                'employee_id' => $employee->id,
                'validated_data' => $validated,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Erro ao salvar: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Receivable $receivable)
    {
        return view('receivables.show', compact('receivable'));
    }

    public function edit(Receivable $receivable)
    {
        $user = Auth::user();

        // Verificar se o receivable pertence à empresa do usuário
        if ($receivable->company_id !== $user->company_id && $user->role !== 'admin') {
            abort(403, 'Acesso negado. Você só pode editar contas da sua empresa.');
        }

        $employees = Employee::where('company_id', $user->company_id)->where('active', true)->get();
        return view('receivables.edit', compact('receivable', 'employees'));
    }

    public function update(Request $request, Receivable $receivable)
    {
        $user = Auth::user();

        // Verificar se o receivable pertence à empresa do usuário
        if ($receivable->company_id !== $user->company_id && $user->role !== 'admin') {
            abort(403, 'Acesso negado. Você só pode atualizar contas da sua empresa.');
        }

        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'pessoa' => 'required|string|max:255',
            'categoria' => 'required|string|max:100',
            'valor' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'data_recebimento' => 'nullable|date',
            'status' => 'required|in:pendente,recebido,atrasado',
            'forma_recebimento' => 'required|string|max:100',
            'observacoes' => 'nullable|string',
            'comprovante' => 'nullable|string|max:255',
        ]);

        $receivable->update($validated);

        return redirect()->route('receivables.index')->with('success', 'Conta a receber atualizada com sucesso!');
    }

    public function destroy(Receivable $receivable)
    {
        $user = Auth::user();

        // Verificar se o receivable pertence à empresa do usuário
        if ($receivable->company_id !== $user->company_id && $user->role !== 'admin') {
            abort(403, 'Acesso negado. Você só pode remover contas da sua empresa.');
        }

        $receivable->delete();
        return redirect()->route('receivables.index')->with('success', 'Conta a receber removida com sucesso!');
    }

    public function marcarComoRecebido(Receivable $receivable)
    {
        $user = Auth::user();

        // Verificar se o receivable pertence à empresa do usuário
        if ($receivable->company_id !== $user->company_id && $user->role !== 'admin') {
            abort(403, 'Acesso negado. Você só pode marcar contas da sua empresa como recebidas.');
        }

        $receivable->update([
            'status' => 'recebido',
            'data_recebimento' => now()->toDateString()
        ]);

        return redirect()->route('receivables.index')->with('success', 'Conta marcada como recebida!');
    }
}
