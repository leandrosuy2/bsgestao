<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::orderBy('created_at', 'desc')->get();
        return view('admin.companies.index', compact('companies'));
    }

    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'cnpj' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'address_number' => 'nullable|string|max:20',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip' => 'nullable|string|max:20',
            'responsible_name' => 'nullable|string|max:100',
            'responsible_email' => 'nullable|email|max:255',
            'responsible_phone' => 'nullable|string|max:30',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
            'paid_until' => 'nullable|date',
        ]);

        // Converter paid_until para null se estiver vazio
        if (empty($data['paid_until'])) {
            $data['paid_until'] = null;
        }

        $company->update($data);
        return redirect()->route('admin.companies.index')->with('success', 'Empresa atualizada com sucesso!');
    }

    public function toggleActive(Company $company)
    {
        $company->is_active = !$company->is_active;
        $company->save();
        return back()->with('success', 'Status atualizado!');
    }

    public function liberarPagamento(Company $company)
    {
        $company->paid_until = now()->addMonth();
        $company->save();
        return back()->with('success', 'Pagamento liberado por 30 dias!');
    }

    public function renovarTrial(Company $company)
    {
        $company->trial_end = now()->addDays(5);
        $company->save();
        return back()->with('success', 'Trial renovado por 5 dias!');
    }

    public function create()
    {
        return view('admin.companies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:companies,email',
            'cnpj' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'address_number' => 'nullable|string|max:20',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip' => 'nullable|string|max:20',
            'responsible_name' => 'nullable|string|max:100',
            'responsible_email' => 'nullable|email|max:255',
            'responsible_phone' => 'nullable|string|max:30',
            'notes' => 'nullable|string',
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|max:255|unique:users,email',
            'user_password' => 'required|string|min:6|confirmed',
        ]);
        $companyData = $data;
        unset($companyData['user_name'], $companyData['user_email'], $companyData['user_password'], $companyData['user_password_confirmation']);
        $companyData['trial_start'] = now();
        $companyData['trial_end'] = now()->addDays(5);
        $companyData['is_active'] = true;
        $companyData['paid_until'] = null;
        $company = Company::create($companyData);
        Log::info('Empresa criada', ['company_id' => $company->id, 'empresa' => $company->name]);
        // Garante que os papéis e permissões da empresa sejam criados
        Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\DefaultRolesSeeder',
            '--force' => true,
        ]);
        Log::info('Seeder de roles rodado para empresa', ['company_id' => $company->id]);
        // Cria usuário master da empresa
        $user = User::create([
            'name' => $data['user_name'],
            'email' => $data['user_email'],
            'password' => bcrypt($data['user_password']),
            'company_id' => $company->id,
        ]);
        Log::info('Usuário master criado', ['user_id' => $user->id, 'email' => $user->email]);
        // Atribui o papel Administrativo da empresa ao usuário
        $adminRole = \App\Models\Role::where('company_id', $company->id)
            ->where('name', 'Administrativo')
            ->first();
        if ($adminRole) {
            $user->roles()->attach($adminRole->id);
            Log::info('Papel Administrativo atribuído ao usuário', ['user_id' => $user->id, 'role_id' => $adminRole->id]);
        } else {
            Log::warning('Papel Administrativo NÃO encontrado para empresa', ['company_id' => $company->id]);
        }
        return redirect()->route('admin.companies.index')->with('success', 'Empresa criada com sucesso! Usuário master: ' . $user->email);
    }

    public function publicCreate()
    {
        return view('companies.public.create');
    }

    public function publicStore(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:companies,email',
                'cnpj' => 'nullable|string|max:30',
                'phone' => 'nullable|string|max:30',
                'address' => 'nullable|string|max:255',
                'address_number' => 'nullable|string|max:20',
                'neighborhood' => 'nullable|string|max:100',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:50',
                'zip' => 'nullable|string|max:20',
                'responsible_name' => 'nullable|string|max:100',
                'responsible_email' => 'nullable|email|max:255',
                'responsible_phone' => 'nullable|string|max:30',
                'notes' => 'nullable|string',
                'user_name' => 'required|string|max:255',
                'user_email' => 'required|email|max:255|unique:users,email',
                'user_password' => 'required|string|min:6|confirmed',
            ]);

            $companyData = $data;
            unset($companyData['user_name'], $companyData['user_email'], $companyData['user_password'], $companyData['user_password_confirmation']);
            $companyData['trial_start'] = now();
            $companyData['trial_end'] = now()->addDays(5);
            $companyData['is_active'] = true;
            $companyData['paid_until'] = null;

            $company = Company::create($companyData);
            Log::info('Empresa criada', ['company_id' => $company->id, 'empresa' => $company->name]);

            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\DefaultRolesSeeder',
                '--force' => true,
            ]);
            Log::info('Seeder de roles rodado para empresa', ['company_id' => $company->id]);

            $user = User::create([
                'name' => $data['user_name'],
                'email' => $data['user_email'],
                'password' => bcrypt($data['user_password']),
                'company_id' => $company->id,
            ]);
            Log::info('Usuário master criado', ['user_id' => $user->id, 'email' => $user->email]);

            $adminRole = \App\Models\Role::where('company_id', $company->id)
                ->where('name', 'Administrativo')
                ->first();
            if ($adminRole) {
                $user->roles()->attach($adminRole->id);
                Log::info('Papel Administrativo atribuído ao usuário', ['user_id' => $user->id, 'role_id' => $adminRole->id]);
            } else {
                Log::warning('Papel Administrativo NÃO encontrado para empresa', ['company_id' => $company->id]);
            }

            return redirect()->route('login')->with('success', 'Empresa cadastrada com sucesso! Faça login com seu e-mail e senha para acessar o sistema.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $customMessages = [];
            foreach ($e->errors() as $field => $messages) {
                $customMessages[$field] = implode(' ', $messages);
            }
            Log::error('Erro de validação ao cadastrar empresa', ['errors' => $customMessages]);
            return redirect()->back()->withInput()->withErrors($customMessages);
        } catch (\Exception $e) {
            Log::error('Erro inesperado ao cadastrar empresa', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->withErrors(['error' => 'Ocorreu um erro inesperado ao cadastrar a empresa. Por favor, tente novamente.']);
        }
    }
}
