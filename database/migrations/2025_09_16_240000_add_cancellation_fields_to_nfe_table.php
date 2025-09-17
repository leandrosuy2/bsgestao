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
            // Campos de cancelamento
            if (!Schema::hasColumn('nfe', 'justificativa_cancelamento')) {
                $table->text('justificativa_cancelamento')->nullable()->after('caminho_xml_cancelamento');
            }
            if (!Schema::hasColumn('nfe', 'protocolo_cancelamento')) {
                $table->string('protocolo_cancelamento')->nullable()->after('justificativa_cancelamento');
            }
            if (!Schema::hasColumn('nfe', 'data_cancelamento')) {
                $table->datetime('data_cancelamento')->nullable()->after('protocolo_cancelamento');
            }
            if (!Schema::hasColumn('nfe', 'mensagem_cancelamento_sefaz')) {
                $table->text('mensagem_cancelamento_sefaz')->nullable()->after('data_cancelamento');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nfe', function (Blueprint $table) {
            if (Schema::hasColumn('nfe', 'mensagem_cancelamento_sefaz')) {
                $table->dropColumn('mensagem_cancelamento_sefaz');
            }
            if (Schema::hasColumn('nfe', 'data_cancelamento')) {
                $table->dropColumn('data_cancelamento');
            }
            if (Schema::hasColumn('nfe', 'protocolo_cancelamento')) {
                $table->dropColumn('protocolo_cancelamento');
            }
            if (Schema::hasColumn('nfe', 'justificativa_cancelamento')) {
                $table->dropColumn('justificativa_cancelamento');
            }
        });
    }
};
