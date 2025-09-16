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
        Schema::create('nfe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->string('ref')->unique(); // Referência única para API
            $table->string('status')->default('processando_autorizacao'); // processando_autorizacao, autorizado, erro_autorizacao, cancelado
            $table->string('chave_nfe')->nullable();
            $table->string('numero_nfe')->nullable();
            $table->string('serie_nfe')->default('1');
            $table->string('status_sefaz')->nullable();
            $table->text('mensagem_sefaz')->nullable();
            $table->string('caminho_xml')->nullable();
            $table->string('caminho_danfe')->nullable();
            $table->string('caminho_xml_cancelamento')->nullable();
            
            // Dados do Emitente (preenchidos automaticamente da empresa)
            $table->string('cnpj_emitente');
            $table->string('nome_emitente');
            $table->string('ie_emitente');
            $table->string('logradouro_emitente');
            $table->string('numero_emitente');
            $table->string('bairro_emitente');
            $table->string('municipio_emitente');
            $table->string('uf_emitente');
            $table->string('cep_emitente');
            $table->string('regime_tributario_emitente')->default('1'); // 1-Simples Nacional
            
            // Dados do Destinatário
            $table->string('cnpj_destinatario')->nullable();
            $table->string('cpf_destinatario')->nullable();
            $table->string('nome_destinatario');
            $table->string('email_destinatario')->nullable();
            $table->string('telefone_destinatario')->nullable();
            $table->string('logradouro_destinatario')->nullable();
            $table->string('numero_destinatario')->nullable();
            $table->string('bairro_destinatario')->nullable();
            $table->string('municipio_destinatario')->nullable();
            $table->string('uf_destinatario')->nullable();
            $table->string('cep_destinatario')->nullable();
            $table->string('ie_destinatario')->nullable();
            $table->enum('indicador_ie_destinatario', ['1', '2', '9'])->default('9'); // 1-Contribuinte, 2-Isento, 9-Não contribuinte
            
            // Dados da NFe
            $table->string('natureza_operacao');
            $table->datetime('data_emissao');
            $table->enum('tipo_documento', ['0', '1'])->default('1'); // 0-Entrada, 1-Saída
            $table->enum('local_destino', ['1', '2', '3'])->default('1'); // 1-Interna, 2-Interestadual, 3-Exterior
            $table->enum('finalidade_emissao', ['1', '2', '3', '4'])->default('1'); // 1-Normal, 2-Complementar, 3-Ajuste, 4-Devolução
            $table->enum('consumidor_final', ['0', '1'])->default('1'); // 0-Normal, 1-Consumidor final
            $table->enum('presenca_comprador', ['0', '1', '2', '3', '4', '9'])->default('1'); // 1-Presencial, 2-Internet, etc
            $table->enum('modalidade_frete', ['0', '1', '2', '9'])->default('9'); // 0-Emitente, 1-Destinatário, 2-Terceiros, 9-Sem frete
            
            // Totais (calculados a partir dos itens)
            $table->decimal('valor_produtos', 15, 2)->default(0);
            $table->decimal('valor_frete', 15, 2)->default(0);
            $table->decimal('valor_seguro', 15, 2)->default(0);
            $table->decimal('valor_desconto', 15, 2)->default(0);
            $table->decimal('valor_outras_despesas', 15, 2)->default(0);
            $table->decimal('valor_total', 15, 2);
            $table->decimal('valor_icms', 15, 2)->default(0);
            $table->decimal('valor_ipi', 15, 2)->default(0);
            $table->decimal('valor_pis', 15, 2)->default(0);
            $table->decimal('valor_cofins', 15, 2)->default(0);
            
            // Informações adicionais
            $table->text('informacoes_adicionais')->nullable();
            $table->text('observacoes')->nullable();
            
            $table->timestamps();
            
            $table->index(['company_id', 'status']);
            $table->index(['cnpj_emitente', 'numero_nfe']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfe');
    }
};
