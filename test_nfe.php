<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Últimas NFes:\n";
$nfes = App\Models\Nfe::latest()->take(5)->get(['id', 'status', 'ref', 'chave_nfe']);

foreach ($nfes as $nfe) {
    $chave = $nfe->chave_nfe ? substr($nfe->chave_nfe, 0, 15) . '...' : 'N/A';
    echo "ID: {$nfe->id}, Status: {$nfe->status}, Ref: {$nfe->ref}, Chave: {$chave}\n";
}
