<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'trial_start',
        'trial_end',
        'is_active',
        'paid_until',
        'cnpj',
        'phone',
        'address',
        'address_number',
        'neighborhood',
        'city',
        'state',
        'zip',
        'responsible_name',
        'responsible_email',
        'responsible_phone',
        'notes',
    ];

    protected $casts = [
        'trial_start' => 'datetime',
        'trial_end' => 'datetime',
        'paid_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
