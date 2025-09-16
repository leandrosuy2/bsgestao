<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Seller extends Model
{
    use HasFactory;

    protected $appends = ['total_sales', 'total_commission', 'sales_count'];

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'document',
        'commission_rate',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'commission_rate' => 'decimal:2'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // Escopo para empresa atual
    public function scopeForCurrentCompany($query)
    {
        return $query->where('company_id', auth()->user()->company_id);
    }

    // Calcular comissões por período
    public function calculateCommissions($startDate, $endDate)
    {
        return $this->sales()
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('SUM(commission_value) as total_commission, COUNT(*) as total_sales')
            ->first();
    }

    public function getTotalSalesAttribute()
    {
        return $this->sales()
            ->where('status', 'completed')
            ->sum('final_total');
    }

    public function getTotalCommissionAttribute()
    {
        $commission_rate = $this->commission_rate;
        return $this->sales()
            ->where('status', 'completed')
            ->sum(DB::raw("final_total * $commission_rate / 100"));
    }

    public function getSalesCountAttribute()
    {
        return $this->sales()
            ->where('status', 'completed')
            ->count();
    }
}
