<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Payable extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'descricao',
        'pessoa',
        'categoria',
        'valor',
        'data_vencimento',
        'data_pagamento',
        'status',
        'forma_pagamento',
        'observacoes',
        'comprovante',
        'criado_por',
        'company_id',
    ];

    public function criador()
    {
        return $this->belongsTo(Employee::class, 'criado_por');
    }
}
