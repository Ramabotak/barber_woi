<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barber extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'experience',
        'photo',
        'status',
    ];

   
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

  
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Scope untuk barber aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    // Cek ketersediaan
    public function isAvailable(): bool
    {
        return $this->status === 'aktif';
    }
}