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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->string('competencia', 7); // MM/YYYY
            $table->decimal('salario_base', 10, 2);
            $table->decimal('descontos', 10, 2)->default(0);
            $table->decimal('adicionais', 10, 2)->default(0);
            $table->decimal('total_liquido', 10, 2);
            $table->enum('status', ['pendente', 'pago'])->default('pendente');
            $table->date('data_pagamento')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
