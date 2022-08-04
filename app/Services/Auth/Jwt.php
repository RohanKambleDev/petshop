<?php

namespace App\Services\Auth;

use Exception;
use App\Services\Auth\LcobucciJwt;

class Jwt extends LcobucciJwt
{
    public function __construct()
    {
    }
    /**
     * getUserApiToken
     *
     * @param  mixed $uuid
     * @return void
     */
    public function getUserApiToken($uuid)
    {
        $this->checkIfArgsEmpty([$uuid]);

        return $this->issueToken($uuid);
    }

    /**
     * validateApiToken
     *
     * @param  mixed $token
     * @param  mixed $uuid
     * @return Lcobucci\JWT\Token $parsedToken
     */
    public function validateApiToken($token)
    {
        $this->checkIfArgsEmpty([$token]);

        $parsedToken = $this->parseToken($token);
        $uuid        = $this->getUserUuid($token);
        return $this->validateToken($parsedToken, $uuid);
    }

    /**
     * getParsedToken
     *
     * @param  mixed $token
     * @return void
     */
    public function getParsedToken($token)
    {
        $this->checkIfArgsEmpty([$token]);

        return $this->parseToken($token);
    }

    /**
     * getUserUuid
     *
     * @param  mixed $token
     * @return void
     */
    public function getUserUuid($token)
    {
        $this->checkIfArgsEmpty([$token]);

        $parsedToken = $this->getParsedToken($token);
        return $parsedToken->claims()->get('jti');
    }

    public function checkIfArgsEmpty($args = [])
    {
        if (is_array($args)) {
            foreach ($args as $arg) {
                if (empty($arg)) {
                    throw new Exception();
                }
            }
        }
    }
}
