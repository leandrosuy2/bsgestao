<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Supplier extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'type', 'cnpj', 'name', 'status',
        'contact_name', 'contact_email', 'contact_phone', 'contact_site',
        'description', 'cep', 'address', 'number', 'complement',
        'neighborhood', 'state', 'city', 'country'
    ];

    /**
     * Relacionamento com empresa
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope para buscar fornecedores ativos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'ativo');
    }
}
