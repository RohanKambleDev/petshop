<?php

namespace App\Services\Auth;

use Exception;
use Lcobucci\JWT\Token;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\IdentifiedBy;
use App\Services\Auth\LcobucciJwtConfig;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use App\Services\Auth\LcobucciJwtInterface;

class LcobucciJwt extends LcobucciJwtConfig implements LcobucciJwtInterface
{
    public $uuid = 0;
    public $config = '';

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->config = $this->setConfiguration();
    }

    /**
     * setAllClaims
     *
     * @return void
     */
    protected function setClaims()
    {
        // if JTI is set to use uuid then use uuid
        // else default uuid will be used which is set in jwt config file
        if ($this->JTI_CLAIM == 'uuid') {
            $this->JTI_CLAIM = $this->uuid;
        }
    }

    /**
     * issueToken
     *
     * @return string
     */
    public function issueToken($uuid)
    {
        $this->uuid = $uuid;
        $this->setClaims();

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
     * @return UnencryptedToken
     */
    public function parseToken($token)
    {
        try {
            assert($this->config instanceof Configuration);
            $parsedToken = $this->config->parser()->parse($token);
            assert($parsedToken instanceof UnencryptedToken);
            return $parsedToken;
        } catch (Exception $e) {
            throw new Exception('Invalid Token');
        }
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
        try {
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
        } catch (Exception $e) {
            throw new Exception('Invalid Token');
        }
    }
}
