<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('user_payment_integrations', function (Blueprint $table) {
            $table->string('client_id')->nullable()->change();
            $table->string('client_secret')->nullable()->change();
            $table->string('x_api_key')->nullable()->change();
            $table->string('cooperativa')->nullable()->change();
            $table->string('posto')->nullable()->change();
            $table->string('codigo_beneficiario')->nullable()->change();
            $table->string('beneficiario_nome')->nullable()->change();
            $table->string('beneficiario_documento')->nullable()->change();
            $table->string('beneficiario_tipo_pessoa')->nullable()->change();
            $table->string('beneficiario_cep')->nullable()->change();
            $table->string('beneficiario_cidade')->nullable()->change();
            $table->string('beneficiario_uf')->nullable()->change();
            $table->string('beneficiario_endereco')->nullable()->change();
            $table->string('beneficiario_numero')->nullable()->change();
        });
    }
    public function down() {
        // Não reverte para evitar perda de dados
    }
};
