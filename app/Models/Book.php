<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'author',
        'price',
        'numOfPages',
        'InventoryQuantity',
        'printDate',
        'description',
        'image',
        'rate',
    ];
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
