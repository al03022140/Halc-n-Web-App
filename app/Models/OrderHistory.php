<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'from_status_id',
        'to_status_id',
        'notes',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fromStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'from_status_id');
    }

    public function toStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'to_status_id');
    }
}
