<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('company.access');
    }

    public function index()
    {
        $user = Auth::user();
        $employees = Employee::where('company_id', $user->company_id)
                            ->orderBy('name')
                            ->paginate(15);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $redirectTo = session('redirect_to');
        $formData = session('form_data');
        $payableId = session('payable_id');
        
        // Sugerir dados baseados no usuário logado se não há funcionários na empresa
        $user = Auth::user();
        $existingEmployees = Employee::where('company_id', $user->company_id)->count();
        
        $suggestedData = null;
        if ($existingEmployees === 0) {
            // Gerar username único
            $baseUsername = strtolower(str_replace(' ', '.', $user->name));
            $username = $baseUsername;
            $counter = 1;
            while (Employee::where('username', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }
            
            // Verificar se email já existe
            $email = $user->email;
            if (Employee::where('email', $email)->exists()) {
                $email = null; // Não sugerir email se já existe
            }
            
            $suggestedData = [
                'name' => $user->name,
                'email' => $email,
                'role' => 'Administrador',
                'admission_date' => now()->toDateString(),
                'username' => $username,
                'permission_level' => 'administrador'
            ];
        }
        
        return view('employees.create', compact('redirectTo', 'formData', 'payableId', 'suggestedData'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Validação personalizada com mensagens mais claras
        $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:employees,cpf',
            'email' => 'required|email|max:255|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string|max:100',
            'admission_date' => 'required|date',
            'username' => 'required|string|max:100|unique:employees,username',
            'password' => 'required|string|min:6|confirmed',
            'permission_level' => 'required|in:administrador,operador,consulta',
            'active' => 'boolean',
        ], [
            'cpf.unique' => 'Este CPF já está cadastrado para outro funcionário.',
            'email.unique' => 'Este e-mail já está cadastrado para outro funcionário.',
            'username.unique' => 'Este nome de usuário já está em uso. Tente outro.',
        ]);

        $validated = $request->only([
            'name', 'cpf', 'email', 'phone', 'role', 'admission_date', 
            'username', 'password', 'permission_level', 'active'
        ]);
        
        $validated['password'] = bcrypt($validated['password']);
        $validated['company_id'] = $user->company_id;
        
        Employee::create($validated);
        
        // Verificar se deve redirecionar para onde estava tentando ir
        $redirectTo = session('redirect_to');
        $payableId = session('payable_id');
        
        if ($redirectTo && $redirectTo === 'payables.create') {
            return redirect()->route('payables.create')->with('success', 'Funcionário cadastrado com sucesso! Agora você pode criar a conta a pagar.');
        } elseif ($redirectTo && $redirectTo === 'payables.edit' && $payableId) {
            return redirect()->route('payables.edit', $payableId)->with('success', 'Funcionário cadastrado com sucesso! Agora você pode editar a conta a pagar.');
        }
        
        return redirect()->route('employees.index')->with('success', 'Funcionário cadastrado com sucesso!');
    }

    public function show(Employee $employee)
    {
        // Verificar se o funcionário pertence à empresa do usuário
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403, 'Acesso negado.');
        }

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        // Verificar se o funcionário pertence à empresa do usuário
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403, 'Acesso negado.');
        }

        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        // Verificar se o funcionário pertence à empresa do usuário
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403, 'Acesso negado.');
        }

        // Validação personalizada com mensagens mais claras
        $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:employees,cpf,' . $employee->id,
            'email' => 'required|email|max:255|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string|max:100',
            'admission_date' => 'required|date',
            'username' => 'required|string|max:100|unique:employees,username,' . $employee->id,
            'password' => 'nullable|string|min:6|confirmed',
            'permission_level' => 'required|in:administrador,operador,consulta',
            'active' => 'boolean',
        ], [
            'cpf.unique' => 'Este CPF já está cadastrado para outro funcionário.',
            'email.unique' => 'Este e-mail já está cadastrado para outro funcionário.',
            'username.unique' => 'Este nome de usuário já está em uso. Tente outro.',
        ]);

        $validated = $request->only([
            'name', 'cpf', 'email', 'phone', 'role', 'admission_date', 
            'username', 'permission_level', 'active'
        ]);
        
        // Só atualizar a senha se foi fornecida
        if ($request->filled('password')) {
            $validated['password'] = bcrypt($request->password);
        }
        
        $employee->update($validated);
        
        return redirect()->route('employees.index')->with('success', 'Funcionário atualizado com sucesso!');
    }

    public function destroy(Employee $employee)
    {
        // Verificar se o funcionário pertence à empresa do usuário
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403, 'Acesso negado.');
        }

        $employee->delete();
        
        return redirect()->route('employees.index')->with('success', 'Funcionário removido com sucesso!');
    }

    public function toggleStatus(Employee $employee)
    {
        // Verificar se o funcionário pertence à empresa do usuário
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403, 'Acesso negado.');
        }

        $employee->active = !$employee->active;
        $employee->save();

        $status = $employee->active ? 'ativado' : 'desativado';
        return redirect()->route('employees.index')->with('success', "Funcionário {$status} com sucesso!");
    }
}
