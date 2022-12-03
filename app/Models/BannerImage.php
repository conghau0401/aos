<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannerImage extends Model
{
    protected $fillable = [
        'banner_id',
        'url',
        'name',
        'image',
    ];
}
