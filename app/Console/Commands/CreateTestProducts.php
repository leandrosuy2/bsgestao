<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;

class CreateTestProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:create-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criar produtos de teste com código e NCM';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Buscar ou criar categoria
        $category = Category::firstOrCreate([
            'name' => 'Geral',
            'company_id' => 1
        ]);

        $products = [
            [
                'name' => 'Produto Teste A',
                'internal_code' => 'PROD001',
                'codigo' => 'COD001',
                'ncm' => '49111090',
                'description' => 'Produto para teste NFe',
                'category_id' => $category->id,
                'unit' => 'UN',
                'cost_price' => 10.00,
                'sale_price' => 15.00,
                'company_id' => 1
            ],
            [
                'name' => 'Produto Teste B',
                'internal_code' => 'PROD002',
                'codigo' => 'COD002',
                'ncm' => '84159090',
                'description' => 'Outro produto para teste NFe',
                'category_id' => $category->id,
                'unit' => 'UN',
                'cost_price' => 20.00,
                'sale_price' => 30.00,
                'company_id' => 1
            ],
            [
                'name' => 'Produto Teste C',
                'internal_code' => 'PROD003',
                'codigo' => 'COD003',
                'ncm' => '94036000',
                'description' => 'Terceiro produto para teste NFe',
                'category_id' => $category->id,
                'unit' => 'UN',
                'cost_price' => 5.00,
                'sale_price' => 8.00,
                'company_id' => 1
            ]
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['internal_code' => $product['internal_code']],
                $product
            );
        }

        $this->info('Produtos de teste criados com sucesso!');
        $this->info('- Produto Teste A (COD001, NCM: 49111090)');
        $this->info('- Produto Teste B (COD002, NCM: 84159090)');
        $this->info('- Produto Teste C (COD003, NCM: 94036000)');
    }
}
