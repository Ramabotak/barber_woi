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

    // Semua ulasan yang masuk lewat booking milik barber ini.
    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Booking::class);
    }

    // Rating rata-rata barber, dibulatkan 1 desimal. Null kalau belum ada ulasan.
    public function averageRating(): ?float
    {
        $avg = $this->reviews()->avg('rating');

        return $avg !== null ? round($avg, 1) : null;
    }

    public function reviewsCount(): int
    {
        return $this->reviews()->count();
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