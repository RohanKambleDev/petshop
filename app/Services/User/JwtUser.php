<?php

namespace App\Services\User;

use App\Models\User;
use App\Facades\LcobucciJwtFacade as Jwt;

class JwtUser
{
    private $apiToken = '';
    private $uuid = '';

    public function __construct()
    {
        /**
         * accessing request from app
         * and getting token from request 
         */
        $this->apiToken = app()->request->bearerToken();
        if (empty($this->apiToken)) {
            return null;
        }
        $this->uuid = Jwt::getUserUuid($this->apiToken);
    }

    public function CheckUser()
    {
        $userObj = User::getUserByUuid($this->uuid);
        return $userObj;
    }
}
