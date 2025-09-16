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
        Schema::table('sales', function (Blueprint $table) {
            $table->enum('payment_mode', ['cash', 'installment'])->default('cash')->after('status');
            $table->date('installment_due_date')->nullable()->after('payment_mode');
            $table->text('installment_notes')->nullable()->after('installment_due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['payment_mode', 'installment_due_date', 'installment_notes']);
        });
    }
};
