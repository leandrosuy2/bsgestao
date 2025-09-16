<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->string('tipo');
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->integer('dias');
            $table->enum('status', ['solicitada', 'aprovada', 'negada', 'gozada'])->default('solicitada');
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
