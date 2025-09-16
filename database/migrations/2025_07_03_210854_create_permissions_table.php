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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome da permissão (ex: Visualizar Produtos, Criar Produtos)
            $table->string('slug')->unique(); // Slug único (ex: view-products, create-products)
            $table->string('module'); // Módulo (ex: products, finance, inventory)
            $table->text('description')->nullable(); // Descrição da permissão
            $table->timestamps();

            // Índices
            $table->index(['module', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
