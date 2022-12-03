<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Cafe24ApiService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Cafe24ApiService';
    }
}
