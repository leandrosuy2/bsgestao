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
        Schema::table('delivery_receipt_items', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_receipt_items', 'unit_price')) {
                $table->decimal('unit_price', 10, 2)->default(0.00)->after('received_quantity');
            }
            if (!Schema::hasColumn('delivery_receipt_items', 'total_price')) {
                $table->decimal('total_price', 10, 2)->default(0.00)->after('unit_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_receipt_items', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'total_price']);
        });
    }
};
