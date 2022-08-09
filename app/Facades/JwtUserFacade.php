<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class JwtUserFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'JwtUser';
    }
}
