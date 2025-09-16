<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NfeItem extends Model
{
    use HasFactory;

    protected $table = 'nfe_items';

    protected $fillable = [
        'nfe_id',
        'numero_item',
        'codigo_produto',
        'descricao',
        'codigo_ncm',
        'cfop',
        'unidade_comercial',
        'quantidade_comercial',
        'valor_unitario_comercial',
        'valor_bruto_produtos',
        'valor_desconto',
        'valor_frete',
        'valor_seguro',
        'valor_outras_despesas',
        'valor_total_item',
        
        // ICMS
        'icms_origem',
        'icms_situacao_tributaria',
        'icms_aliquota',
        'icms_valor',
        'icms_base_calculo',
        
        // IPI
        'ipi_situacao_tributaria',
        'ipi_aliquota',
        'ipi_valor',
        'ipi_base_calculo',
        
        // PIS
        'pis_situacao_tributaria',
        'pis_aliquota',
        'pis_valor',
        'pis_base_calculo',
        
        // COFINS
        'cofins_situacao_tributaria',
        'cofins_aliquota',
        'cofins_valor',
        'cofins_base_calculo',
        
        'informacoes_adicionais'
    ];

    protected $casts = [
        'quantidade_comercial' => 'decimal:4',
        'valor_unitario_comercial' => 'decimal:4',
        'valor_bruto_produtos' => 'decimal:2',
        'valor_desconto' => 'decimal:2',
        'valor_frete' => 'decimal:2',
        'valor_seguro' => 'decimal:2',
        'valor_outras_despesas' => 'decimal:2',
        'valor_total_item' => 'decimal:2',
        
        'icms_aliquota' => 'decimal:4',
        'icms_valor' => 'decimal:2',
        'icms_base_calculo' => 'decimal:2',
        
        'ipi_aliquota' => 'decimal:4',
        'ipi_valor' => 'decimal:2',
        'ipi_base_calculo' => 'decimal:2',
        
        'pis_aliquota' => 'decimal:4',
        'pis_valor' => 'decimal:2',
        'pis_base_calculo' => 'decimal:2',
        
        'cofins_aliquota' => 'decimal:4',
        'cofins_valor' => 'decimal:2',
        'cofins_base_calculo' => 'decimal:2',
    ];

    public function nfe()
    {
        return $this->belongsTo(Nfe::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'codigo_produto', 'code');
    }

    /**
     * Calcular valor total do item
     */
    public function calcularValorTotal()
    {
        $this->valor_bruto = $this->quantidade_comercial * $this->valor_unitario_comercial;
        $this->valor_total_item = $this->valor_bruto - $this->valor_desconto;
        
        return $this;
    }

    /**
     * Calcular ICMS
     */
    public function calcularIcms()
    {
        if ($this->icms_aliquota > 0) {
            $this->icms_base_calculo = $this->valor_total_item;
            $this->icms_valor = $this->icms_base_calculo * ($this->icms_aliquota / 100);
        }
        
        return $this;
    }

    /**
     * Calcular IPI
     */
    public function calcularIpi()
    {
        if ($this->ipi_aliquota > 0) {
            $this->ipi_base_calculo = $this->valor_total_item;
            $this->ipi_valor = $this->ipi_base_calculo * ($this->ipi_aliquota / 100);
        }
        
        return $this;
    }

    /**
     * Calcular PIS
     */
    public function calcularPis()
    {
        if ($this->pis_aliquota > 0) {
            $this->pis_base_calculo = $this->valor_total_item;
            $this->pis_valor = $this->pis_base_calculo * ($this->pis_aliquota / 100);
        }
        
        return $this;
    }

    /**
     * Calcular COFINS
     */
    public function calcularCofins()
    {
        if ($this->cofins_aliquota > 0) {
            $this->cofins_base_calculo = $this->valor_total_item;
            $this->cofins_valor = $this->cofins_base_calculo * ($this->cofins_aliquota / 100);
        }
        
        return $this;
    }

    /**
     * Calcular todos os impostos
     */
    public function calcularImpostos()
    {
        $this->calcularValorTotal()
             ->calcularIcms()
             ->calcularIpi()
             ->calcularPis()
             ->calcularCofins();
             
        return $this;
    }
}
