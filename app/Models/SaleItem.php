<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'total_price',
        'discount_amount',
        'discount_percentage',
        'discount_type',
        'final_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'final_price' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Aplicar desconto no item
     */
    public function applyDiscount($discountValue, $discountType = 'amount')
    {
        $this->discount_type = $discountType;
        
        if ($discountType === 'percentage') {
            $this->discount_percentage = $discountValue;
            $this->discount_amount = ($this->total_price * $discountValue) / 100;
        } else {
            $this->discount_amount = $discountValue;
            $this->discount_percentage = $this->total_price > 0 ? ($discountValue / $this->total_price) * 100 : 0;
        }
        
        $this->final_price = $this->total_price - $this->discount_amount;
        
        // Garantir que o preço final não seja negativo
        if ($this->final_price < 0) {
            $this->final_price = 0;
            $this->discount_amount = $this->total_price;
        }
        
        return $this;
    }

    /**
     * Remover desconto do item
     */
    public function removeDiscount()
    {
        $this->discount_type = 'none';
        $this->discount_amount = 0;
        $this->discount_percentage = 0;
        $this->final_price = $this->total_price;
        
        return $this;
    }

    /**
     * Accessor para verificar se tem desconto
     */
    public function getHasDiscountAttribute()
    {
        return $this->discount_type !== 'none' && $this->discount_amount > 0;
    }

    /**
     * Accessor para valor do desconto formatado
     */
    public function getFormattedDiscountAttribute()
    {
        if ($this->discount_type === 'percentage') {
            return number_format($this->discount_percentage, 1) . '%';
        } else {
            return 'R$ ' . number_format($this->discount_amount, 2, ',', '.');
        }
    }
}
