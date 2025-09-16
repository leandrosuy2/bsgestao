<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Nfe extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'nfe';

    protected $fillable = [
        'company_id',
        'ref',
        'status',
        'chave_nfe',
        'numero_nfe',
        'serie_nfe',
        'status_sefaz',
        'mensagem_sefaz',
        'caminho_xml',
        'caminho_danfe',
        'caminho_xml_cancelamento',
        
        // Dados do Emitente
        'cnpj_emitente',
        'nome_emitente',
        'ie_emitente',
        'logradouro_emitente',
        'numero_emitente',
        'bairro_emitente',
        'municipio_emitente',
        'uf_emitente',
        'cep_emitente',
        'regime_tributario_emitente',
        
        // Dados do Destinatário
        'cnpj_destinatario',
        'cpf_destinatario',
        'nome_destinatario',
        'email_destinatario',
        'telefone_destinatario',
        'logradouro_destinatario',
        'numero_destinatario',
        'bairro_destinatario',
        'municipio_destinatario',
        'uf_destinatario',
        'cep_destinatario',
        'ie_destinatario',
        'indicador_ie_destinatario',
        
        // Dados da NFe
        'natureza_operacao',
        'data_emissao',
        'tipo_documento',
        'local_destino',
        'finalidade_emissao',
        'consumidor_final',
        'presenca_comprador',
        'modalidade_frete',
        
        // Campos de devolução
        'data_devolucao',
        'protocolo_devolucao',
        'justificativa_devolucao',
        'status_devolucao',
        'mensagem_devolucao_sefaz',
        'caminho_xml_devolucao',
        
        // Totais
        'valor_produtos',
        'valor_frete',
        'valor_seguro',
        'valor_desconto',
        'valor_outras_despesas',
        'valor_total',
        'valor_icms',
        'valor_ipi',
        'valor_pis',
        'valor_cofins',
        
        // Informações adicionais
        'informacoes_adicionais',
        'observacoes',
    ];

    protected $casts = [
        'data_emissao' => 'datetime',
        'data_devolucao' => 'datetime',
        'valor_produtos' => 'decimal:2',
        'valor_frete' => 'decimal:2',
        'valor_seguro' => 'decimal:2',
        'valor_desconto' => 'decimal:2',
        'valor_outras_despesas' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'valor_icms' => 'decimal:2',
        'valor_ipi' => 'decimal:2',
        'valor_pis' => 'decimal:2',
        'valor_cofins' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function items()
    {
        return $this->hasMany(NfeItem::class);
    }

    public function duplicatas()
    {
        return $this->hasMany(NfeDuplicata::class);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'processando_autorizacao' => 'bg-yellow-100 text-yellow-800',
            'autorizado' => 'bg-green-100 text-green-800',
            'erro_autorizacao' => 'bg-red-100 text-red-800',
            'cancelado' => 'bg-gray-100 text-gray-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getStatusNameAttribute()
    {
        $names = [
            'processando_autorizacao' => 'Processando',
            'autorizado' => 'Autorizada',
            'erro_autorizacao' => 'Erro',
            'cancelado' => 'Cancelada',
        ];

        return $names[$this->status] ?? 'Desconhecido';
    }

    public function getTipoDocumentoNameAttribute()
    {
        return $this->tipo_documento == '0' ? 'Entrada' : 'Saída';
    }

    public function getConsumidorFinalNameAttribute()
    {
        return $this->consumidor_final == '1' ? 'Sim' : 'Não';
    }

    public function recalcularTotais()
    {
        $this->valor_produtos = $this->items->sum('valor_total_item');
        $this->valor_icms = $this->items->sum('icms_valor');
        $this->valor_ipi = $this->items->sum('ipi_valor');
        $this->valor_pis = $this->items->sum('pis_valor');
        $this->valor_cofins = $this->items->sum('cofins_valor');
        
        $this->valor_total = $this->valor_produtos + 
                           $this->valor_frete + 
                           $this->valor_seguro + 
                           $this->valor_outras_despesas - 
                           $this->valor_desconto;
        
        return $this;
    }
}
