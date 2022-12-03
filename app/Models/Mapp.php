<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapp extends Model
{
    use HasFactory;

    protected $table = 'mapps';

    protected $fillable = [
        'type',
        'cafe24_id',
        'doosoun_id',
        'mall_id',
        'shop_no',
    ];
}
