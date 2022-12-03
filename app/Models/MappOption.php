<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MappOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'map_id',
        'meta_key',
        'meta_value',
    ];
}
