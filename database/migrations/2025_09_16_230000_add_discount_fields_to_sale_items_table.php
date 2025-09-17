<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            // Adicionar campos de desconto por produto
            if (!Schema::hasColumn('sale_items', 'discount_amount')) {
                $table->decimal('discount_amount', 12, 2)->default(0.00)->after('total_price');
            }
            
            if (!Schema::hasColumn('sale_items', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(0.00)->after('discount_amount');
            }
            
            if (!Schema::hasColumn('sale_items', 'discount_type')) {
                $table->enum('discount_type', ['none', 'amount', 'percentage'])->default('none')->after('discount_percentage');
            }
            
            if (!Schema::hasColumn('sale_items', 'final_price')) {
                $table->decimal('final_price', 12, 2)->default(0.00)->after('discount_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            if (Schema::hasColumn('sale_items', 'final_price')) {
                $table->dropColumn('final_price');
            }
            if (Schema::hasColumn('sale_items', 'discount_type')) {
                $table->dropColumn('discount_type');
            }
            if (Schema::hasColumn('sale_items', 'discount_percentage')) {
                $table->dropColumn('discount_percentage');
            }
            if (Schema::hasColumn('sale_items', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
        });
    }
};
