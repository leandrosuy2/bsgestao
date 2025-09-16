<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock_quantity')->default(0)->after('min_stock');
        });

        // Migrar os dados existentes: o que está em min_stock agora vai para stock_quantity
        DB::statement('UPDATE products SET stock_quantity = min_stock');
        
        // Resetar min_stock para valores padrão mais apropriados (10% do estoque atual ou 5 se for 0)
        DB::statement('UPDATE products SET min_stock = GREATEST(FLOOR(stock_quantity * 0.1), 5)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock_quantity');
        });
    }
};
