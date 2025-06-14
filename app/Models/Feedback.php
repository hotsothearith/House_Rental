<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    // Explicitly define the table name if it's not the plural form of the model name
    protected $table = 'feedback';

    protected $fillable = [
        'user_email',
        'comment',
        'rating',
    ];

    /**
     * Get the tenant that owns the feedback.
     * This establishes a belongsTo relationship with the Tenant model,
     * using 'user_email' in this table to match 'email_id' in the tenants table.
     */

public function tenant()
{
    return $this->belongsTo(Tenant::class, 'user_email', 'email_address');
}
public function payment()
{
    return $this->belongsTo(Payment::class, 'payment_id');
}

}