<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'custom_id',
        'name',
        'phone',
        'email',
        'address',
        'fiscal_data',
        'is_active'
    ];
    
    // Relación con órdenes
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
