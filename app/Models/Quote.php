<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Quote extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'user_id',
        'company_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'company_name',
        'company_subtitle',
        'quote_number',
        'total',
        'discount',
        'final_total',
        'status',
        'valid_until',
        'notes',
        'payment_terms',
        'delivery_time',
        'pix_key',
    ];

    protected $casts = [
        'valid_until' => 'date',
        'total' => 'decimal:2',
        'discount' => 'decimal:2',
        'final_total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function getStatusLabelAttribute()
    {
        return [
            'draft' => 'Rascunho',
            'sent' => 'Enviado',
            'accepted' => 'Aceito',
            'rejected' => 'Rejeitado',
            'expired' => 'Expirado',
        ][$this->status] ?? $this->status;
    }
}
