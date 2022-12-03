<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get all of the images for the banner.
     */
    public function images()
    {
        return $this->hasMany(BannerImage::class);
    }
}
