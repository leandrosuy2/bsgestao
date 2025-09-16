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
        Schema::table('nfe', function (Blueprint $table) {
            // Campos para devolução
            $table->timestamp('data_devolucao')->nullable()->after('data_emissao');
            $table->string('protocolo_devolucao')->nullable()->after('data_devolucao');
            $table->text('justificativa_devolucao')->nullable()->after('protocolo_devolucao');
            $table->string('status_devolucao')->nullable()->after('justificativa_devolucao');
            $table->text('mensagem_devolucao_sefaz')->nullable()->after('status_devolucao');
            $table->string('caminho_xml_devolucao')->nullable()->after('mensagem_devolucao_sefaz');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nfe', function (Blueprint $table) {
            $table->dropColumn([
                'data_devolucao',
                'protocolo_devolucao', 
                'justificativa_devolucao',
                'status_devolucao',
                'mensagem_devolucao_sefaz',
                'caminho_xml_devolucao'
            ]);
        });
    }
};
