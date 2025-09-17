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
            // Adicionar campos de cliente para facilitar a impressão
            if (!Schema::hasColumn('delivery_receipts', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('sale_id');
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('delivery_receipts', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('customer_id');
            }
            
            if (!Schema::hasColumn('delivery_receipts', 'customer_cpf_cnpj')) {
                $table->string('customer_cpf_cnpj', 20)->nullable()->after('customer_name');
            }
            
            if (!Schema::hasColumn('delivery_receipts', 'customer_phone')) {
                $table->string('customer_phone', 20)->nullable()->after('customer_cpf_cnpj');
            }
            
            if (!Schema::hasColumn('delivery_receipts', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('customer_phone');
            }
            
            if (!Schema::hasColumn('delivery_receipts', 'delivery_address')) {
                $table->text('delivery_address')->nullable()->after('customer_email');
            }
            
            if (!Schema::hasColumn('delivery_receipts', 'delivery_city')) {
                $table->string('delivery_city', 100)->nullable()->after('delivery_address');
            }
            
            if (!Schema::hasColumn('delivery_receipts', 'delivery_state')) {
                $table->string('delivery_state', 2)->nullable()->after('delivery_city');
            }
            
            if (!Schema::hasColumn('delivery_receipts', 'delivery_zipcode')) {
                $table->string('delivery_zipcode', 10)->nullable()->after('delivery_state');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_receipts', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_receipts', 'delivery_zipcode')) {
                $table->dropColumn('delivery_zipcode');
            }
            if (Schema::hasColumn('delivery_receipts', 'delivery_state')) {
                $table->dropColumn('delivery_state');
            }
            if (Schema::hasColumn('delivery_receipts', 'delivery_city')) {
                $table->dropColumn('delivery_city');
            }
            if (Schema::hasColumn('delivery_receipts', 'delivery_address')) {
                $table->dropColumn('delivery_address');
            }
            if (Schema::hasColumn('delivery_receipts', 'customer_email')) {
                $table->dropColumn('customer_email');
            }
            if (Schema::hasColumn('delivery_receipts', 'customer_phone')) {
                $table->dropColumn('customer_phone');
            }
            if (Schema::hasColumn('delivery_receipts', 'customer_cpf_cnpj')) {
                $table->dropColumn('customer_cpf_cnpj');
            }
            if (Schema::hasColumn('delivery_receipts', 'customer_name')) {
                $table->dropColumn('customer_name');
            }
            if (Schema::hasColumn('delivery_receipts', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }
        });
    }
};
