<?php

namespace App\Services\Auth;

use Carbon\Carbon;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class LcobucciJwtConfig
{
    protected $KEY_FOLDER_NAME = 'app';
    protected $KEY_PATH = 'keys';
    protected $PUBLIC_KEY_FILE_NAME = 'petshop.pem';
    protected $PRIVATE_KEY_FILE_NAME = 'petshop.key';

    // Configures the issuer (iss claim)
    protected $ISS_CLAIM = 'https://rohutech.com';
    // Configures the audience (aud claim)
    protected $AUD_CLAIM = 'https://rohankamble.com';
    // Configures the id (jti claim)
    protected $JTI_CLAIM = '';
    // Configures the time that the token was issue (iat claim)
    protected $IAT_CLAIM = '';
    // Configures the time that the token can be used after (nbf claim)
    protected $NBF_CLAIM = '';
    // Configures the expiration time of the token (exp claim)
    protected $EXP_CLAIM = '';

    public function __construct()
    {
    }

    protected function getPrivateKeyPath()
    {
        return storage_path($this->KEY_FOLDER_NAME . '/' . $this->KEY_PATH . '/' . $this->PRIVATE_KEY_FILE_NAME);
    }

    protected function getPublicKeyPath()
    {
        return storage_path($this->KEY_FOLDER_NAME . '/' . $this->KEY_PATH . '/' . $this->PUBLIC_KEY_FILE_NAME);
    }

    protected function setConfiguration()
    {
        return Configuration::forAsymmetricSigner(
            // You may use RSA or ECDSA and all their variations (256, 384, and 512) and EdDSA over Curve25519
            new Sha256(),
            InMemory::file($this->getPrivateKeyPath()),
            InMemory::file($this->getPublicKeyPath())
            // You may also override the JOSE encoder/decoder if needed by providing extra arguments here
        );
    }
}
