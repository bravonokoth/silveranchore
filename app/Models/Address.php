<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'session_id',
    'type',
    'name',        
    'email',
    'phone_number',
    'line1',
    'line2',
    'city',
    'state',
    'postal_code',
    'country',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ordersAsShipping()
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }

    public function ordersAsBilling()
    {
        return $this->hasMany(Order::class, 'billing_address_id');
    }
}
