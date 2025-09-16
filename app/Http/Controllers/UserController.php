<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $query = User::with(['company']);

        // Se não for admin_sistema, mostrar apenas usuários da mesma empresa
        if (Auth::user()->role !== 'admin_sistema') {
            $query->where('company_id', Auth::user()->company_id);
        }

        $users = $query->paginate(10);
        $companies = Company::where('active', true)->get();

        return view('users.index', compact('users', 'companies'));
    }

    public function create()
    {
        $companies = Company::where('active', true)->get();
        
        // Buscar apenas os roles principais e ativos, limitando a quantidade
        $roles = Role::where('is_active', true)
            ->whereIn('name', ['Admin', 'Manager', 'User', 'Vendedor', 'Estoque', 'Financeiro'])
            ->orWhere('name', 'like', '%admin%')
            ->orWhere('name', 'like', '%user%')
            ->orWhere('name', 'like', '%gerente%')
            ->take(10) // Limitar a 10 roles
            ->get();
            
        return view('users.create', compact('companies', 'roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin_sistema,admin_empresa,usuario',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Verificar permissões
        if (Auth::user()->role === 'admin_empresa' && $request->role === 'admin_sistema') {
            return back()->withErrors(['role' => 'Você não tem permissão para criar usuários admin do sistema.'])->withInput();
        }

        if (Auth::user()->role === 'admin_empresa' && $request->company_id != Auth::user()->company_id) {
            return back()->withErrors(['company_id' => 'Você só pode criar usuários para sua própria empresa.'])->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'company_id' => $request->company_id,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    public function show(User $user)
    {
        // Verificar permissões
        if (Auth::user()->role === 'admin_empresa' && $user->company_id !== Auth::user()->company_id) {
            abort(403, 'Acesso negado.');
        }

        $user->load(['company']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // Verificar permissões
        if (Auth::user()->role === 'admin_empresa' && $user->company_id !== Auth::user()->company_id) {
            abort(403, 'Acesso negado.');
        }

        $companies = Company::where('active', true)->get();
        
        // Buscar apenas os roles principais e ativos, limitando a quantidade
        $roles = Role::where('is_active', true)
            ->whereIn('name', ['Admin', 'Manager', 'User', 'Vendedor', 'Estoque', 'Financeiro'])
            ->orWhere('name', 'like', '%admin%')
            ->orWhere('name', 'like', '%user%')
            ->orWhere('name', 'like', '%gerente%')
            ->take(10) // Limitar a 10 roles
            ->get();
        
        return view('users.edit', compact('user', 'companies', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // Verificar permissões
        if (Auth::user()->role === 'admin_empresa' && $user->company_id !== Auth::user()->company_id) {
            abort(403, 'Acesso negado.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin_sistema,admin_empresa,usuario',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Verificar permissões
        if (Auth::user()->role === 'admin_empresa' && $request->role === 'admin_sistema') {
            return back()->withErrors(['role' => 'Você não tem permissão para alterar usuários para admin do sistema.'])->withInput();
        }

        if (Auth::user()->role === 'admin_empresa' && $request->company_id != Auth::user()->company_id) {
            return back()->withErrors(['company_id' => 'Você só pode alterar usuários para sua própria empresa.'])->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'company_id' => $request->company_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        // Verificar permissões
        if (Auth::user()->role === 'admin_empresa' && $user->company_id !== Auth::user()->company_id) {
            abort(403, 'Acesso negado.');
        }

        // Não permitir excluir o próprio usuário
        if ($user->id === Auth::id()) {
            return back()->withErrors(['delete' => 'Você não pode excluir sua própria conta.']);
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }
}
