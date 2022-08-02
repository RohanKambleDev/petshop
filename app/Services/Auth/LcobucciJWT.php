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
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;

class LcobucciJWT
{
    const KEY_FOLDER_NAME = 'app';
    const KEY_PATH = 'keys';
    const PUBLIC_KEY_FILE_NAME = 'petshop.pem';
    const PRIVATE_KEY_FILE_NAME = 'petshop.key';
    private $uuid = 0;

    private $config = '';

    public function __construct()
    {
        $this->config = $this->setConfiguration();
    }
    private function getPrivateKeyPath()
    {
        return storage_path(self::KEY_FOLDER_NAME . '/' . self::KEY_PATH . '/' . self::PRIVATE_KEY_FILE_NAME);
    }

    private function getPublicKeyPath()
    {
        return storage_path(self::KEY_FOLDER_NAME . '/' . self::KEY_PATH . '/' . self::PUBLIC_KEY_FILE_NAME);
    }
    private function getPrivateKey()
    {
        if (Storage::exists(self::KEY_PATH . '/' . self::PRIVATE_KEY_FILE_NAME)) {
            return Storage::get(self::KEY_PATH . '/' . self::PRIVATE_KEY_FILE_NAME);
        }
    }

    private function getPublicKey()
    {
        if (Storage::exists(self::KEY_PATH . '/' . self::PUBLIC_KEY_FILE_NAME)) {
            return Storage::get(self::KEY_PATH . '/' . self::PUBLIC_KEY_FILE_NAME);
        }
    }
    private function setConfiguration()
    {
        return Configuration::forAsymmetricSigner(
            // You may use RSA or ECDSA and all their variations (256, 384, and 512) and EdDSA over Curve25519
            new Sha256(),
            InMemory::file($this->getPrivateKeyPath()),
            InMemory::file($this->getPublicKeyPath())
            // You may also override the JOSE encoder/decoder if needed by providing extra arguments here
        );
    }
    private function issueToken()
    {
        assert($this->config instanceof Configuration);

        $now   = new DateTimeImmutable();
        // $clock = SystemClock::fromUTC(); // use the clock for issuing and validation
        // $now = $clock->now();
        $token = $this->config->builder()
            // Configures the issuer (iss claim)
            ->issuedBy('https://rohutech.com')
            // Configures the audience (aud claim)
            // ->permittedFor('https://rohankamble.com')
            // Configures the id (jti claim)
            ->identifiedBy($this->uuid)
            // Configures the time that the token was issue (iat claim)
            ->issuedAt($now)
            // Configures the time that the token can be used (nbf claim)
            ->canOnlyBeUsedAfter($now->modify('+1 minute'))
            // Configures the expiration time of the token (exp claim)
            ->expiresAt($now->modify('+10 minute'))
            // Configures a new claim, called "uid"
            // ->withClaim('uid', $this->uuid)
            // Configures a new header, called "foo"
            // ->withHeader('foo', 'bar')
            // Builds a new token
            ->getToken($this->config->signer(), $this->config->signingKey());

        return $token->toString();
    }

    private function parseToken($token)
    {
        assert($this->config instanceof Configuration);

        $parsedToken = $this->config->parser()->parse($token);

        assert($parsedToken instanceof UnencryptedToken);

        return $parsedToken;
    }

    private function validateToken(Token $token)
    {
        $clock = SystemClock::fromUTC(); // use the clock for issuing and validation
        $this->config->setValidationConstraints(
            new IdentifiedBy($this->uuid),
            new IssuedBy('https://rohutech.com'),
            // new PermittedFor('https://rohankamble.com'),
            new SignedWith($this->config->signer(), $this->config->verificationKey()),
            new StrictValidAt($clock)
        );

        $constraints = $this->config->validationConstraints();
        return $this->config->validator()->validate($token, ...$constraints);
    }

    public function getUserApiToken($uuid)
    {
        $this->uuid = $uuid;
        return $this->issueToken();
    }

    public function validateApiToken($token, $uuid)
    {
        if (empty($uuid)) {
            throw new Exception('User not registered');
        }
        $this->uuid = $uuid;
        $parsedToken = $this->parseToken($token);
        return $this->validateToken($parsedToken);
    }

    public function getParsedToken($token)
    {
        return $this->parseToken($token);
    }
}
