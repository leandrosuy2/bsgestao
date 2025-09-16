<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPaymentIntegration extends Model
{
    protected $fillable = [
        'user_id',
        'enabled',
        'api_key',
        'client_id',
        'client_secret',
        'x_api_key',
        'cooperativa',
        'posto',
        'codigo_beneficiario',
        'beneficiario_nome',
        'beneficiario_documento',
        'beneficiario_tipo_pessoa',
        'beneficiario_cep',
        'beneficiario_cidade',
        'beneficiario_uf',
        'beneficiario_endereco',
        'beneficiario_numero',
        'config',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'config' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
