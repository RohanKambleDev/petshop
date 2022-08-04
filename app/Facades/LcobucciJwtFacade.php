<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class LcobucciJwtFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Jwt';
    }
}
