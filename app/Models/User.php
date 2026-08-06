<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

     
    public function barber()
    {
        return $this->hasOne(Barber::class);
    }

  
    public function customerBookings()
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    
    public function reviews()
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

   
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    // Cek role
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isBarber(): bool
    {
        return $this->role === 'barber';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }
}