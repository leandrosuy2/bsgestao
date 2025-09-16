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
        Schema::table('delivery_receipts', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_receipts', 'payment_status')) {
                $table->enum('payment_status', ['paid', 'pending', 'installment', 'overdue'])
                      ->default('paid')->after('status');
            }
            if (!Schema::hasColumn('delivery_receipts', 'sale_id')) {
                $table->unsignedBigInteger('sale_id')->nullable()->after('user_id');
                $table->foreign('sale_id')->references('id')->on('sales')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_receipts', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_receipts', 'sale_id')) {
                $table->dropForeign(['sale_id']);
                $table->dropColumn('sale_id');
            }
            if (Schema::hasColumn('delivery_receipts', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
        });
    }
};
