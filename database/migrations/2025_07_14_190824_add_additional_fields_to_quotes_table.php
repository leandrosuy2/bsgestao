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
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('company_id');
            $table->string('company_subtitle')->nullable()->after('company_name');
            $table->string('quote_number')->nullable()->after('company_subtitle');
            $table->text('payment_terms')->nullable()->after('notes');
            $table->string('delivery_time')->nullable()->after('payment_terms');
            $table->string('pix_key')->nullable()->after('delivery_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'company_subtitle', 
                'quote_number',
                'payment_terms',
                'delivery_time',
                'pix_key'
            ]);
        });
    }
};
