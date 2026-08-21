<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarberCompensationSetting extends Model
{
    use HasFactory;

    public const COMMISSION_TYPES = [
        'none' => 'Tanpa komisi',
        'per_booking' => 'Nominal per booking selesai',
        'percentage' => 'Persentase pendapatan booking',
    ];

    protected $fillable = [
        'barber_id',
        'fixed_salary',
        'commission_type',
        'commission_value',
    ];

    protected $casts = [
        'fixed_salary' => 'decimal:2',
        'commission_value' => 'decimal:2',
    ];

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }
}
