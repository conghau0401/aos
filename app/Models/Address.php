<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'mall_id',
        'store_id',
        'address_name',
        'full_name',
        'address_basic',
        'address_detail',
        'phone',
        'mobile',
        'zip_code',
        'is_default',
    ];
}
