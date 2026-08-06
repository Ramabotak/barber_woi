<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'barber_id',
        'date',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    
    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Scope jadwal tersedia
    public function scopeAvailable($query)
    {
        return $query->where('status', 'tersedia');
    }

    // Cek apakah slot masih tersedia
    public function isAvailable(): bool
    {
        return $this->status === 'tersedia';
    }
}