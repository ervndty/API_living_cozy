<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'status',
        'price',
        'customer_first_name',
        'customer_email',
        'item_name',
        'checkout_link'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}


    // protected $primaryKey = 'payment_id';

    // protected $fillable = [
    //     'user_id',
    //     'order_id',
    //     'amount',
    //     'payment_method',
    //     'status'
    // ];

    // // Relasi dengan model User
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // // Relasi dengan model Order
    // public function order()
    // {
    //     return $this->belongsTo(Order::class);
    // }


