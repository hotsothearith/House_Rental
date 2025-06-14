<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Tenant extends Authenticatable
{
     use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'tenants';

    protected $fillable = [
    'full_name',
    'email_address',
    'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

  public function bookings()
{
    return $this->hasMany(Booking::class, 'tenant_email', 'email_address');
}


    public function agreements()
    {
        // FIX: Changed 'email_id' back to 'email_address'
        return $this->hasMany(Agreement::class, 'user_email', 'email_address');
    }

    public function payments()
    {
        // FIX: Changed 'email_id' back to 'email_address'
        return $this->hasMany(Payment::class, 'user_email', 'email_address');
    }

    public function feedback()
    {
        // FIX: Changed 'email_id' back to 'email_address'
        return $this->hasMany(Feedback::class, 'user_email', 'email_address');
    }
}
