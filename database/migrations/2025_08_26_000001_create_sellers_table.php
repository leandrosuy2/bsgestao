<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('document')->nullable(); // CPF/CNPJ
            $table->decimal('commission_rate', 5, 2)->default(0); // Taxa de comissão em %
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Adicionar campo seller_id na tabela sales
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('seller_id')->nullable()->constrained('sellers')->nullOnDelete();
            $table->decimal('commission_value', 10, 2)->nullable(); // Valor da comissão calculada
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['seller_id']);
            $table->dropColumn(['seller_id', 'commission_value']);
        });
        
        Schema::dropIfExists('sellers');
    }
};
