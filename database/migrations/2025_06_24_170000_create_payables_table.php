<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payables', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->string('pessoa');
            $table->string('categoria');
            $table->decimal('valor', 12, 2);
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->enum('status', ['pendente', 'pago', 'atrasado'])->default('pendente');
            $table->string('forma_pagamento');
            $table->text('observacoes')->nullable();
            $table->string('comprovante')->nullable();
            $table->foreignId('criado_por')->constrained('employees');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payables');
    }
};
