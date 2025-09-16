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
        Schema::create('romaneio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('romaneio_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantidade_esperada');
            $table->integer('quantidade_recebida')->default(0);
            $table->text('observacoes_item')->nullable();
            $table->boolean('conferido')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('romaneio_items');
    }
};
