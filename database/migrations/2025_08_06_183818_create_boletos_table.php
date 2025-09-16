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
        Schema::create('boletos', function (Blueprint $table) {
            $table->id();
            $table->string('txid')->unique();
            $table->text('qr_code');
            $table->string('linha_digitavel');
            $table->string('codigo_barras');
            $table->string('cooperativa');
            $table->string('posto');
            $table->string('nosso_numero');
            $table->string('cliente_nome');
            $table->string('cliente_documento');
            $table->string('cliente_endereco');
            $table->string('cliente_cidade');
            $table->string('cliente_uf', 2);
            $table->string('cliente_cep', 8);
            $table->decimal('valor', 10, 2);
            $table->date('data_vencimento');
            $table->string('seu_numero')->nullable();
            $table->text('instrucoes')->nullable();
            $table->string('status')->default('gerado');
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boletos');
    }
};
