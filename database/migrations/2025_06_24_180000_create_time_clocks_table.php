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
        Schema::create('time_clocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->date('data');
            $table->time('hora_entrada')->nullable();
            $table->time('hora_saida')->nullable();
            $table->time('hora_intervalo_inicio')->nullable();
            $table->time('hora_intervalo_fim')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_clocks');
    }
};
