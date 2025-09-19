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
            $table->foreignId('nfe_id')->nullable()->constrained('nfe')->onDelete('set null');
            $table->boolean('has_nfe')->default(false)->comment('Indica se a venda possui NFe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['nfe_id']);
            $table->dropColumn(['nfe_id', 'has_nfe']);
        });
    }
};
