<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'mall_id',
        'mall_order_id',
        'user_id',
        'store_id',
        'payment_amount',
        'shipping_method',
    ];
}
