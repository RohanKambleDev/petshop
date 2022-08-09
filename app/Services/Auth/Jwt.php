<?php

namespace App\Services\Auth;

use Exception;
use App\Services\Auth\LcobucciJwt;

class Jwt extends LcobucciJwt
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * getUserApiToken
     *
     * @param  mixed $uuid
     * @return void
     */
    public function getUserApiToken($uuid)
    {
        // validate
        $this->checkIfArgsEmpty([$uuid]);

        return $this->issueToken($uuid);
    }

    /**
     * validateApiToken
     *
     * @param  mixed $token
     * @return bool
     */
    public function validateApiToken($token)
    {
        // validate
        $this->checkIfArgsEmpty([$token]);

        $parsedToken = $this->parseToken($token);
        $uuid        = $this->getUserUuid($token);
        return $this->validateToken($parsedToken, $uuid);
    }

    /**
     * getParsedToken
     *
     * @param  mixed $token
     * @return UnencryptedToken
     */
    public function getParsedToken($token)
    {
        // validate
        $this->checkIfArgsEmpty([$token]);

        return $this->parseToken($token);
    }

    /**
     * getUserUuid
     *
     * @param  mixed $token
     * @return string
     */
    public function getUserUuid($token)
    {
        // validate
        $this->checkIfArgsEmpty([$token]);

        $parsedToken = $this->getParsedToken($token);
        return $parsedToken->claims()->get('jti');
    }

    /**
     * checkIfArgsEmpty
     *
     * @param  mixed $args
     * @return void
     */
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
