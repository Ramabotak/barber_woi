<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    public const STATUSES = [
        'draft' => 'Menunggu Pembayaran',
        'paid' => 'Dibayar',
        'cancelled' => 'Dibatalkan',
    ];

    protected $fillable = [
        'barber_id',
        'period_start',
        'period_end',
        'base_salary',
        'commission_amount',
        'bonus_amount',
        'deduction_amount',
        'total_amount',
        'completed_bookings',
        'commission_type',
        'commission_value',
        'status',
        'notes',
        'calculated_by',
        'paid_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'base_salary' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'deduction_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function calculatedBy()
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    public function expense()
    {
        return $this->hasOne(Expense::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}
