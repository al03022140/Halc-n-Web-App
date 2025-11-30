<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'requested_by',
        'assigned_to',
        'material_name',
        'quantity_needed',
        'details',
        'status',
        'needed_by',
        'resolved_at',
    ];

    protected $casts = [
        'needed_by' => 'date',
        'resolved_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function updates()
    {
        return $this->hasMany(PurchaseUpdate::class)->latest();
    }
}
