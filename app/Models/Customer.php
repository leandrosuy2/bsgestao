<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Customer extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'type',
        'name',
        'cpf_cnpj',
        'email',
        'phone',
        'address',
        'neighborhood',
        'number',
        'city',
        'state',
        'postal_code',
        'notes',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Relacionamento com empresa
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope para buscar apenas clientes ativos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope para buscar por tipo
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Accessor para formatar CPF/CNPJ
     */
    public function getFormattedCpfCnpjAttribute()
    {
        if (!$this->cpf_cnpj) return '';
        
        $numbers = preg_replace('/[^0-9]/', '', $this->cpf_cnpj);
        
        if (strlen($numbers) === 11) {
            // CPF
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $numbers);
        } elseif (strlen($numbers) === 14) {
            // CNPJ
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $numbers);
        }
        
        return $this->cpf_cnpj;
    }

    /**
     * Accessor para tipo em português
     */
    public function getTypeNameAttribute()
    {
        return $this->type === 'pessoa_fisica' ? 'Pessoa Física' : 'Pessoa Jurídica';
    }
}
