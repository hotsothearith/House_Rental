<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;


class HouseOwner extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'house_owners';

    protected $fillable = [
        'owner_name',
        'email_address',
        'password',
        'mobile_number',
         'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function houses()
    {
        return $this->hasMany(House::class, 'house_owner_id');
    }

    public function agreements()
    {
        return $this->hasMany(Agreement::class, 'house_owner_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'house_owner_id');
    }
}
