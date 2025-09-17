<?php

require_once 'vendor/autoload.php';

use App\Models\Product;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$products = Product::where('company_id', 1)->where('name', 'like', '%SITIO%')->get(['name']);

foreach($products as $p) {
    echo $p->name . "\n";
}
