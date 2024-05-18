<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'description', 'price', 'quantity_available', 'category_id', 'producer_id',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function producer()
    {
        return $this->belongsTo(Producer::class);
    }
}