<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Sale extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'cash_register_id',
        'user_id',
        'company_id',
        'customer_id',
        'seller_id',
        'nfe_id',
        'has_nfe',
        'total',
        'discount',
        'discount_type',
        'final_total',
        'status',
        'payment_mode',
        'installment_due_date',
        'installment_notes',
        'sold_at',
        'cancelled_at',
        'cancellation_reason',
        'cancelled_by',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'discount' => 'decimal:2',
        'final_total' => 'decimal:2',
        'installment_due_date' => 'date',
        'sold_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function nfe()
    {
        return $this->belongsTo(Nfe::class);
    }

    // Verificar se é pagamento a prazo
    public function isInstallment()
    {
        return $this->payment_mode === 'installment';
    }

    // Verificar se o pagamento a prazo está vencido
    public function isOverdue()
    {
        if (!$this->isInstallment()) {
            return false;
        }

        return $this->installment_due_date && $this->installment_due_date->isPast();
    }

    // Obter valor total a prazo
    public function getInstallmentAmount()
    {
        return $this->payments()->where('payment_type', 'prazo')->sum('amount');
    }

    // Obter dias até vencimento
    public function getDaysUntilDue()
    {
        if (!$this->isInstallment() || !$this->installment_due_date) {
            return null;
        }

        return now()->diffInDays($this->installment_due_date, false);
    }

    // Scopes
    public function scopeInstallment($query)
    {
        return $query->where('payment_mode', 'installment');
    }

    public function scopeCash($query)
    {
        return $query->where('payment_mode', 'cash');
    }

    public function scopeOverdue($query)
    {
        return $query->where('payment_mode', 'installment')
                    ->where('installment_due_date', '<', now()->toDateString());
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('payment_mode', 'installment')
                    ->whereBetween('installment_due_date', [
                        now()->toDateString(),
                        now()->addDays($days)->toDateString()
                    ]);
    }
}
