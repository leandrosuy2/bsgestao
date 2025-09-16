<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Boleto extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'txid',
        'qr_code',
        'linha_digitavel',
        'codigo_barras',
        'cooperativa',
        'posto',
        'nosso_numero',
        'cliente_nome',
        'cliente_documento',
        'cliente_endereco',
        'cliente_cidade',
        'cliente_uf',
        'cliente_cep',
        'valor',
        'data_vencimento',
        'seu_numero',
        'instrucoes',
        'status',
        'pdf_path',
        'company_id'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_vencimento' => 'date',
    ];
}
