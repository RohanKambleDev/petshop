<?php

namespace App\Services\Auth;

use Lcobucci\JWT\Token;

interface LcobucciJwtInterface
{
    public function issueToken(string $uuid);
    public function parseToken(Token $token);
    public function validateToken(Token $token, $uuid);
}
