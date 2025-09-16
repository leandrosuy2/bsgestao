<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserPaymentIntegrationsTable extends Migration
{
    public function up()
    {
        Schema::table('user_payment_integrations', function (Blueprint $table) {
            if (!Schema::hasColumn('user_payment_integrations', 'client_id')) $table->string('client_id')->nullable();
            if (!Schema::hasColumn('user_payment_integrations', 'client_secret')) $table->string('client_secret')->nullable();
            if (!Schema::hasColumn('user_payment_integrations', 'x_api_key')) $table->string('x_api_key')->nullable();
            if (!Schema::hasColumn('user_payment_integrations', 'cooperativa')) $table->string('cooperativa')->nullable();
            if (!Schema::hasColumn('user_payment_integrations', 'posto')) $table->string('posto')->nullable();
            if (!Schema::hasColumn('user_payment_integrations', 'codigo_beneficiario')) $table->string('codigo_beneficiario')->nullable();
            if (!Schema::hasColumn('user_payment_integrations', 'beneficiario_nome')) $table->string('beneficiario_nome')->nullable();
            if (!Schema::hasColumn('user_payment_integrations', 'beneficiario_documento')) $table->string('beneficiario_documento')->nullable();
            if (!Schema::hasColumn('user_payment_integrations', 'beneficiario_tipo_pessoa')) $table->string('beneficiario_tipo_pessoa')->nullable();
            if (!Schema::hasColumn('user_payment_integrations', 'beneficiario_cep')) $table->string('beneficiario_cep')->nullable();
            if (!Schema::hasColumn('user_payment_integrations', 'beneficiario_cidade')) $table->string('beneficiario_cidade')->nullable();
            if (!Schema::hasColumn('user_payment_integrations', 'beneficiario_uf')) $table->string('beneficiario_uf')->nullable();
            if (!Schema::hasColumn('user_payment_integrations', 'beneficiario_endereco')) $table->string('beneficiario_endereco')->nullable();
            if (!Schema::hasColumn('user_payment_integrations', 'beneficiario_numero')) $table->string('beneficiario_numero')->nullable();
        });
    }
    
    public function down()
    {
        // Não remove colunas para evitar perda de dados
    }
}
