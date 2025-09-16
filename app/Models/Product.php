<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Product extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'name',
        'internal_code',
        'codigo',
        'ncm',
        'description',
        'category_id',
        'unit',
        'cost_price',
        'sale_price',
        'min_stock',
        'stock_quantity',
        'company_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
