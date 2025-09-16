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
        Schema::create('vacations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->integer('dias');
            $table->enum('status', ['solicitada', 'aprovada', 'negada', 'gozada'])->default('solicitada');
            $table->date('data_solicitacao');
            $table->date('data_aprovacao')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacations');
    }
};
