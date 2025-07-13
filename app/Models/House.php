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
        'image_url',
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
    public function getImageUrlAttribute()
{
        return $this->image ? asset('storage/' . $this->image) : null;
}
    public function tenants()
{
    return $this->belongsToMany(
        \App\Models\Tenant::class,
        'bookings',
        'house_id',
        'tenant_id'
    )->withTimestamps();
}
public function feedbacks()
{
    return $this->hasMany(Feedback::class);
}
}