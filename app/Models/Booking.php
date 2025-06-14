<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_number',
        'tenant_email',
        'house_id',
        'from_date',
        'to_date',
        'duration',
        'message',
        'status',
    ];

    public function house()
    {
        return $this->belongsTo(House::class, 'house_id');
    }

    public function tenant()
{
    return $this->belongsTo(Tenant::class, 'tenant_email', 'email_address');
}

    public function agreement()
    {
        return $this->hasOne(Agreement::class, 'booking_no');
    }

}
