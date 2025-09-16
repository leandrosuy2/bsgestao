<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeClock extends Model
{
    protected $fillable = [
        'employee_id',
        'clock_in',
        'clock_out',
        'date',
        'total_hours',
        'status'
    ];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'date' => 'date'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
