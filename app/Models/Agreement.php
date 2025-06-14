<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_no',
        'house_id',
        'house_owner_id',
        'user_email',
        'remember',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_no');
    }

    public function house()
    {
        return $this->belongsTo(House::class, 'house_id');
    }

    public function houseOwner()
    {
        return $this->belongsTo(HouseOwner::class, 'house_owner_id');
    }


public function tenant()
{
    return $this->belongsTo(Tenant::class, 'user_email', 'email_address');
}
public function administrator()
{
    return $this->belongsTo(Administrator::class, 'admin_id');
}

}