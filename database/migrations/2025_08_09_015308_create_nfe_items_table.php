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
        Schema::create('nfe_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nfe_id')->constrained('nfe')->onDelete('cascade');
            $table->integer('numero_item');
            $table->string('codigo_produto');
            $table->text('descricao');
            $table->string('codigo_ncm');
            $table->string('cfop');
            $table->string('unidade_comercial')->default('UN');
            $table->decimal('quantidade_comercial', 15, 4);
            $table->decimal('valor_unitario_comercial', 15, 6);
            $table->decimal('valor_bruto_produtos', 15, 2);
            $table->decimal('valor_desconto', 15, 2)->default(0);
            $table->decimal('valor_frete', 15, 2)->default(0);
            $table->decimal('valor_seguro', 15, 2)->default(0);
            $table->decimal('valor_outras_despesas', 15, 2)->default(0);
            $table->decimal('valor_total_item', 15, 2);
            
            // Tributos ICMS
            $table->string('icms_origem')->default('0'); // 0-Nacional, 1-Estrangeira, etc
            $table->string('icms_situacao_tributaria'); // 00, 10, 20, 30, 40, 41, 50, 51, 60, 70, 90, etc
            $table->decimal('icms_base_calculo', 15, 2)->default(0);
            $table->decimal('icms_aliquota', 5, 2)->default(0);
            $table->decimal('icms_valor', 15, 2)->default(0);
            
            // Tributos IPI
            $table->string('ipi_situacao_tributaria')->default('53'); // 00-99 conforme tabela
            $table->decimal('ipi_base_calculo', 15, 2)->default(0);
            $table->decimal('ipi_aliquota', 5, 2)->default(0);
            $table->decimal('ipi_valor', 15, 2)->default(0);
            
            // Tributos PIS
            $table->string('pis_situacao_tributaria')->default('07'); // 01-99 conforme tabela
            $table->decimal('pis_base_calculo', 15, 2)->default(0);
            $table->decimal('pis_aliquota', 5, 4)->default(0);
            $table->decimal('pis_valor', 15, 2)->default(0);
            
            // Tributos COFINS
            $table->string('cofins_situacao_tributaria')->default('07'); // 01-99 conforme tabela
            $table->decimal('cofins_base_calculo', 15, 2)->default(0);
            $table->decimal('cofins_aliquota', 5, 4)->default(0);
            $table->decimal('cofins_valor', 15, 2)->default(0);
            
            $table->timestamps();
            
            $table->index(['nfe_id', 'numero_item']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfe_items');
    }
};
