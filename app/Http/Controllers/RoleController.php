<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::where('company_id', Auth::user()->company_id)
            ->with('permissions')
            ->orderBy('name')
            ->get();

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');

        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = Role::create([
            'company_id' => Auth::user()->company_id,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->attach($request->permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Papel criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $this->authorize('view', $role);

        $role->load('permissions');
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $this->authorize('update', $role);

        $permissions = Permission::orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');

        $role->load('permissions');

        return view('roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('update', $role);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Sincronizar permissões
        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('roles.index')
            ->with('success', 'Papel atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        // Verificar se há usuários usando este papel
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Não é possível excluir um papel que está sendo usado por usuários.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Papel excluído com sucesso!');
    }

    /**
     * Ativar/desativar papel
     */
    public function toggleStatus(Role $role)
    {
        $this->authorize('update', $role);

        $role->update([
            'is_active' => !$role->is_active
        ]);

        $status = $role->is_active ? 'ativado' : 'desativado';

        return redirect()->route('roles.index')
            ->with('success', "Papel {$status} com sucesso!");
    }
}
