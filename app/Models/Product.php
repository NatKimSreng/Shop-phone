<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Mass assignable fields
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'stock',
        'category_id',
    ];

    protected $casts = [
        'stock' => 'boolean',
        'price' => 'decimal:2',
    ];

    // Relationship: Product belongs to one category
    //     public function products()
    // {
    //     return $this->hasMany(Product::class);
    // }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
