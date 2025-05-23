<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'comment',
        'photo_path',
    ];

 

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

}
