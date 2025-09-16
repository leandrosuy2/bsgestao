<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receivables', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->string('pessoa');
            $table->string('categoria');
            $table->decimal('valor', 12, 2);
            $table->date('data_vencimento');
            $table->date('data_recebimento')->nullable();
            $table->enum('status', ['pendente', 'recebido', 'atrasado'])->default('pendente');
            $table->string('forma_recebimento');
            $table->text('observacoes')->nullable();
            $table->string('comprovante')->nullable();
            $table->foreignId('criado_por')->constrained('employees');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receivables');
    }
};
