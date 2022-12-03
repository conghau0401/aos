<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mall extends Model
{
    protected $fillable = [
        'mall_id',
        'mall_name',
        'mall_url',
        'shop_no',
        'pc_skin_no',
        'mobile_skin_no',
        'refresh_token_expires_at',
        'access_token',
        'refresh_token',
        'scopes',
        'default',
        'is_disabled',
        'is_deleted',
    ];
}
