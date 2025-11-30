<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'customer_number',
        'customer_custom_id',
        'customer_name',
        'fiscal_data',
        'order_date',
        'delivery_address',
        'notes',
        'status_id',
        'user_id',
        'client_id',
        'route_user_id',
        'product_id',
        'quantity',
        'start_image',
        'end_image',
        'has_incident',
        'incident_notes',
        'missing_items',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
        'has_incident' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function status()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function routeOperator()
    {
        return $this->belongsTo(User::class, 'route_user_id');
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class)->latest();
    }
    
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchaseRequest()
    {
        return $this->hasOne(PurchaseRequest::class);
    }

    public function getDisplayStatusNameAttribute(): string
    {
        $statusName = optional($this->status)->name;

        if ($this->has_incident && $statusName === 'In route') {
            return 'No Delivered';
        }

        return $statusName ?? 'Sin estado';
    }

    public function getDisplayStatusColorAttribute(): string
    {
        $statusColor = optional($this->status)->color;
        $statusName = optional($this->status)->name;

        if ($this->has_incident && $statusName === 'In route') {
            return '#dc3545';
        }

        return $statusColor ?? '#6c757d';
    }
}
