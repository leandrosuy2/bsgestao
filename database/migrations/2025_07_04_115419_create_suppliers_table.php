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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['empresa', 'pessoa']);
            $table->string('cnpj', 20);
            $table->string('name');
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->string('contact_site')->nullable();
            $table->text('description')->nullable();
            $table->string('cep', 12);
            $table->string('address');
            $table->string('number', 20);
            $table->string('complement')->nullable();
            $table->string('neighborhood');
            $table->string('state', 40);
            $table->string('city', 60);
            $table->string('country', 40);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
