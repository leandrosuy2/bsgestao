<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Employee;
use App\Models\Receivable;

echo "=== TESTE DE RECEIVABLE ===\n";

// Usuario ID 12 (guabinorte)
$user = User::find(12);
if (!$user) {
    echo "Usuario nao encontrado\n";
    exit;
}

echo "User: {$user->name}, Company: {$user->company_id}\n";

// Buscar employee
$employee = Employee::where('company_id', $user->company_id)
                  ->where('active', true)
                  ->first();
                  
if (!$employee) {
    echo "Employee nao encontrado\n";
    // Listar employees da empresa
    $employees = Employee::where('company_id', $user->company_id)->get();
    echo "Employees na empresa {$user->company_id}:\n";
    foreach ($employees as $emp) {
        echo "- ID: {$emp->id}, Nome: {$emp->name}, Email: {$emp->email}, Ativo: {$emp->active}\n";
    }
    exit;
}

echo "Employee: {$employee->name}, ID: {$employee->id}\n";

// Criar receivable
try {
    $receivable = Receivable::create([
        'descricao' => 'Teste direto',
        'pessoa' => 'Pessoa teste',
        'categoria' => 'vendas',
        'valor' => 2.00,
        'data_vencimento' => '2025-07-24',
        'forma_recebimento' => 'pix',
        'comprovante' => 'teste',
        'observacoes' => 'teste',
        'criado_por' => $employee->id,
        'status' => 'pendente',
        'company_id' => $user->company_id,
    ]);
    echo "✅ SUCCESS: Receivable criado com ID: {$receivable->id}\n";
} catch (Exception $e) {
    echo "❌ ERROR: {$e->getMessage()}\n";
    echo "Stack trace:\n{$e->getTraceAsString()}\n";
}
