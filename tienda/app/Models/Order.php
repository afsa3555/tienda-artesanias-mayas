<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'customer_name', 'customer_email',
        'total', 'status', 'payment_reference',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
