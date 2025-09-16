<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cpf', 14)->unique();
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->string('role');
            $table->date('admission_date');
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('permission_level', ['administrador', 'operador', 'consulta']);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
