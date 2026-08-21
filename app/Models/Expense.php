<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'gaji_komisi' => 'Gaji/Komisi Barber',
        'produk_stok' => 'Produk & Stok',
        'operasional' => 'Operasional',
        'sewa' => 'Sewa',
        'perawatan_alat' => 'Perawatan Alat',
        'marketing' => 'Marketing',
        'lainnya' => 'Lainnya',
    ];

    public const PAYMENT_METHODS = [
        'cash' => 'Tunai',
        'transfer' => 'Transfer Bank',
        'qris' => 'QRIS',
        'ewallet' => 'E-wallet',
        'other' => 'Lainnya',
    ];

    public const STATUSES = [
        'pending' => 'Menunggu Persetujuan',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
    ];

    protected $fillable = [
        'expense_date',
        'category',
        'description',
        'amount',
        'payment_method',
        'status',
        'receipt_path',
        'rejection_note',
        'created_by',
        'reviewed_by',
        'reviewed_at',
        'payroll_id',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst(str_replace('_', ' ', $this->category));
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}
