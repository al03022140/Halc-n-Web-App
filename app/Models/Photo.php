<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'path', 'type', 'notes']; // tipo: 'entrega' o 'en ruta'

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
