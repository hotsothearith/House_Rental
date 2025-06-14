<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    use HasFactory;

    protected $fillable = [
       'house_owner_id',
        'address',
        'house_city',
        'house_district',
        'house_state',
        'descriptions',
        'price',
        'house_type',
        'rooms',
        'furnitures',
        'variation',
        'image',
    ];

    public function houseOwner()
    {
        return $this->belongsTo(HouseOwner::class, 'house_owner_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'house_id');
    }

    public function agreements()
    {
        return $this->hasMany(Agreement::class, 'house_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'house_id');
    }
}