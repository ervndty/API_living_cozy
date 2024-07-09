<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'product_name',
        'description',
        'price',
        'stock',
        'category_id',
        'image_url',
    ];

    // Relationship to the Category model
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
