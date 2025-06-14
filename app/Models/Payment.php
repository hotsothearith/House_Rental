<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'house_id',
        'house_owner_id',
        'user_email',
        'details',
        'date_payment',
        'messages', // Added messages field for consistency with ERD comment
    ];

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
    // Feedback relationship is conceptual as per ERD, not direct FK
    public function feedback()
{
    return $this->hasMany(Feedback::class, 'payment_id');
}

}