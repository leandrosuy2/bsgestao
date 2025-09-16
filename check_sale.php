<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$sale = App\Models\Sale::find(57);
echo "Sale ID: " . $sale->id . "\n";
echo "Company ID: " . $sale->company_id . "\n";
echo "User ID: " . $sale->user_id . "\n";
echo "Customer ID: " . $sale->customer_id . "\n";
