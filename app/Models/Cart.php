<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\ShippingMethodEnum;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'store_id',
        'user_id',
        'ds_product_id',
        'ds_option_id',
        'shipping_method',
        'quantity',
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'shipping_method' => ShippingMethodEnum::class,
    ];
}
