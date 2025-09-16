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
        Schema::create('romaneios', function (Blueprint $table) {
            $table->id();
            $table->string('numero_romaneio')->unique();
            $table->date('data_entrega');
            $table->string('contato_fornecedor')->nullable();
            $table->string('cnpj_fornecedor', 18)->nullable();
            $table->string('razao_social');
            $table->string('uf', 2);
            $table->string('cidade');
            $table->text('observacoes')->nullable();
            $table->enum('status', ['pendente', 'em_andamento', 'concluido', 'cancelado'])->default('pendente');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('romaneios');
    }
};
