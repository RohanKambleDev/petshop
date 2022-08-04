<?php

/**
 * 
 * The JWT specification defines seven reserved claims that are not required, 
    but are recommended to allow interoperability with third-party applications. These are:
    iss (issuer): Issuer of the JWT
    sub (subject): Subject of the JWT (the user)
    aud (audience): Recipient for which the JWT is intended
    exp (expiration time): Time after which the JWT expires
    nbf (not before time): Time before which the JWT must not be accepted for processing
    iat (issued at time): Time at which the JWT was issued; can be used to determine age of the JWT
    jti (JWT ID): Unique identifier; can be used to prevent the JWT from being replayed (allows a token to be used only once)
 * 
 * 
 */

namespace App\Services\Auth;

use Exception;
use DateTimeZone;
use DateTimeImmutable;
use Lcobucci\JWT\Token;
use Lcobucci\Clock\Clock;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Illuminate\Support\Facades\Storage;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\IdentifiedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use App\Services\Auth\LcobucciJwtConfig;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use App\Services\Auth\LcobucciJwtInterface;

class LcobucciJwt extends LcobucciJwtConfig
{
    private $uuid = 0;
    private $config = '';

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->config = $this->setConfiguration();
    }

    protected function setAllClaims()
    {
        $now = new DateTimeImmutable();
        $this->IAT_CLAIM = $now;
        $this->NBF_CLAIM = $now->modify('+1 sec');
        $this->EXP_CLAIM = $now->modify('+10 minute');
        $this->JTI_CLAIM = $this->uuid;
    }

    /**
     * issueToken
     *
     * @return string
     */
    public function issueToken($uuid)
    {
        $this->config = $this->setConfiguration();
        $this->uuid   = $uuid;
        $this->setAllClaims();

        assert($this->config instanceof Configuration);

        $token = $this->config->builder()
            // Configures the issuer (iss claim)
            ->issuedBy($this->ISS_CLAIM)
            // Configures the audience (aud claim)
            // ->permittedFor($this->AUD_CLAIM)
            // Configures the id (jti claim)
            ->identifiedBy($this->JTI_CLAIM)
            // Configures the time that the token was issue (iat claim)
            ->issuedAt($this->IAT_CLAIM)
            // Configures the time that the token can be used after (nbf claim)
            ->canOnlyBeUsedAfter($this->NBF_CLAIM)
            // Configures the expiration time of the token (exp claim)
            ->expiresAt($this->EXP_CLAIM)
            // Configures a new claim, called "uid"
            // ->withClaim('uid', $this->uuid)
            // Configures a new header, called "foo"
            // ->withHeader('foo', 'bar')
            // Builds a new token
            ->getToken($this->config->signer(), $this->config->signingKey());

        return $token->toString();
    }

    /**
     * parseToken
     *
     * @param  string $token
     * @return void
     */
    public function parseToken($token)
    {
        $this->config = $this->setConfiguration();

        if (empty($token)) {
            throw new Exception('User not registered');
        }
        assert($this->config instanceof Configuration);

        $parsedToken = $this->config->parser()->parse($token);

        assert($parsedToken instanceof UnencryptedToken);

        return $parsedToken;
    }


    /**
     * validateToken
     *
     * @param  Token $token
     * @param  string $uuid
     * @return bool
     */
    public function validateToken(Token $token, $uuid)
    {
        $this->config = $this->setConfiguration();

        $clock = SystemClock::fromUTC(); // use the clock for issuing and validation
        $this->config->setValidationConstraints(
            new IdentifiedBy($uuid),
            new IssuedBy($this->ISS_CLAIM),
            // new PermittedFor($this->AUD_CLAIM),
            new SignedWith($this->config->signer(), $this->config->verificationKey()),
            new StrictValidAt($clock)
        );
        $constraints = $this->config->validationConstraints();
        return $this->config->validator()->validate($token, ...$constraints);
    }
}
