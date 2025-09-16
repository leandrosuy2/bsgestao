<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->string('tipo');
            $table->decimal('valor', 10, 2)->default(0);
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('benefits');
    }
};
