<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$sale = App\Models\Sale::with('items')->find(57);
echo "Product name: " . $sale->items->first()->product_name . "\n";
echo "Product ID: " . $sale->items->first()->product_id . "\n";
echo "Quantity: " . $sale->items->first()->quantity . "\n";
