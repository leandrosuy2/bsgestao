<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Category extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'name',
        'code',
        'description',
        'company_id',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
