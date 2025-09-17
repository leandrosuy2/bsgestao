<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class DeliveryReceipt extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'user_id',
        'sale_id',
        'customer_id',
        'customer_name',
        'customer_cpf_cnpj',
        'customer_phone',
        'customer_email',
        'delivery_address',
        'delivery_city',
        'delivery_state',
        'delivery_zipcode',
        'receipt_number',
        'supplier_name',
        'supplier_cnpj',
        'supplier_contact',
        'delivery_date',
        'status',
        'payment_status',
        'total_items',
        'checked_items',
        'progress_percentage',
        'notes',
        'finalized_by',
        'finalized_at'
    ];

    protected $casts = [
        'delivery_date' => 'datetime',
        'finalized_at' => 'datetime',
    ];

    /**
     * Relacionamento com empresa
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relacionamento com usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com itens do romaneio
     */
    public function items()
    {
        return $this->hasMany(DeliveryReceiptItem::class);
    }

    /**
     * Relacionamento com a venda
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Relacionamento com o cliente
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Scope para status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para período
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('delivery_date', [$startDate, $endDate]);
    }

    /**
     * Accessor para status em português
     */
    public function getStatusNameAttribute()
    {
        $statuses = [
            'pending' => 'Pendente',
            'completed' => 'Concluído',
            'cancelled' => 'Cancelado'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Accessor para total de itens
     */
    public function getTotalItemsAttribute()
    {
        return $this->items()->count();
    }

    /**
     * Accessor para itens conferidos
     */
    public function getCheckedItemsAttribute()
    {
        return $this->items()->where('checked', true)->count();
    }

    /**
     * Accessor para porcentagem de conclusão
     */
    public function getCompletionPercentageAttribute()
    {
        $total = $this->total_items;
        if ($total === 0) return 0;
        
        return round(($this->checked_items / $total) * 100, 1);
    }

    /**
     * Update delivery receipt progress
     */
    public function updateProgress()
    {
        $totalItems = $this->items()->count();
        $checkedItems = $this->items()->where('checked', true)->count();
        
        $this->total_items = $totalItems;
        $this->checked_items = $checkedItems;
        $this->progress_percentage = $totalItems > 0 ? round(($checkedItems / $totalItems) * 100) : 0;
        
        // Atualizar status baseado no progresso
        if ($this->progress_percentage > 0 && $this->status === 'pending') {
            $this->status = 'in_progress';
        }
        
        $this->save();
    }
}
