<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testando dados de clientes...\n\n";

$customers = App\Models\Customer::where('active', true)
    ->select('id', 'name', 'cpf_cnpj', 'phone', 'address', 'city', 'state', 'postal_code', 'type')
    ->limit(5)
    ->get();

foreach ($customers as $customer) {
    echo "Cliente: {$customer->name}\n";
    echo "CPF/CNPJ: {$customer->cpf_cnpj}\n";
    echo "Telefone: {$customer->phone}\n";
    echo "Endereço: {$customer->address}\n";
    echo "Cidade: {$customer->city}\n";
    echo "Estado: {$customer->state}\n";
    echo "CEP: {$customer->postal_code}\n";
    echo "Tipo: {$customer->type}\n";
    echo "---\n";
}
