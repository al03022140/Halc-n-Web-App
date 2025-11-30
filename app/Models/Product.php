<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'name',
        'description',
        'price',
        'sku',
        'stock',
        'reorder_level',
        'is_active'
    ];
    
    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
        'reorder_level' => 'integer',
        'is_active' => 'boolean',
    ];
    
    // Relación con órdenes
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
