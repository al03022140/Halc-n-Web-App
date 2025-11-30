<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'user_id',
        'status',
        'notes',
        'attachment_path',
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
