<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryReceiptItem extends Model
{
    protected $fillable = [
        'delivery_receipt_id',
        'product_id',
        'product_name',
        'product_code',
        'expected_quantity',
        'received_quantity',
        'quantity', // para compatibilidade com código existente
        'unit_price',
        'total_price',
        'checked',
        'notes'
    ];

    protected $casts = [
        'expected_quantity' => 'decimal:2',
        'received_quantity' => 'decimal:2',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'checked' => 'boolean',
    ];

    /**
     * Relacionamento com romaneio
     */
    public function deliveryReceipt()
    {
        return $this->belongsTo(DeliveryReceipt::class);
    }

    /**
     * Relacionamento com produto
     */
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }

    /**
     * Scope para itens conferidos
     */
    public function scopeChecked($query, $checked = true)
    {
        return $query->where('checked', $checked);
    }
}
