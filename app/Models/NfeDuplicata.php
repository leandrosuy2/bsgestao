<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NfeDuplicata extends Model
{
    protected $table = 'nfe_duplicatas';

    protected $fillable = [
        'nfe_id',
        'numero',
        'data_vencimento', 
        'valor'
    ];

    protected $casts = [
        'data_vencimento' => 'date',
        'valor' => 'decimal:2'
    ];

    public function nfe(): BelongsTo
    {
        return $this->belongsTo(Nfe::class);
    }
}
