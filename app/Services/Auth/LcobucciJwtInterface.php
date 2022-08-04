<?php

namespace App\Services\Auth;

use PhpParser\Parser\Tokens;

interface LcobucciJwtInterface
{
    public function issueToken(): string;
    public function parseToken(): Tokens;
    public function validateToken(): bool;
}
