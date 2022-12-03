<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class AOSAPI extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'AOSAPI';
    }
}
