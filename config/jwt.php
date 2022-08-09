<?php


$now = new DateTimeImmutable();

return [
    'key' => [
        'folder_name' => env('JWT_KEY_FOLDER_NAME', 'app'),
        'path'        => env('JWT_KEY_PATH', 'keys'),
        'public'      => env('JWT_PUBLIC_KEY_FILE_NAME', 'petshop.pem'),
        'private'     => env('JWT_PRIVATE_KEY_FILE_NAME', 'petshop.key'),
    ],

    /**
     * 
     * The JWT specification defines seven reserved claims that are not required, 
     * but are recommended to allow interoperability with third-party applications. These are:
     * iss (issuer): Issuer of the JWT
     * sub (subject): Subject of the JWT (the user)
     * aud (audience): Recipient for which the JWT is intended
     * exp (expiration time): Time after which the JWT expires
     * nbf (not before time): Time before which the JWT must not be accepted for processing
     * iat (issued at time): Time at which the JWT was issued; can be used to determine age of the JWT
     * jti (JWT ID): Unique identifier; can be used to prevent the JWT from being replayed (allows a token to be used only once)
     * 
     */

    'claims' => [
        /**
         * Configures the issuer (iss claim)
         */
        'iss' => env('JWT_ISS_CLAIM', 'https://rohutech.com'),

        /**
         * Configures the audience (aud claim) 
         */
        'aud' => env('JWT_AUD_CLAIM', 'https://rohankamble.com'),

        /**
         * Configures the id (jti claim)
         * a unique id
         */
        'jti' => env('JWT_JTI_CLAIM', 'fa10bc4c-0c99-3504-8052-48897e990287'),

        /**
         * Configures the time that the token was issue (iat claim)
         */
        'iat' => env('JWT_IAT_CLAIM', $now),

        /**
         * Configures the time that the token can be used after (nbf claim)
         */
        'nbf' => env('JWT_NBF_CLAIM', '+1 sec'),

        /**
         * Configures the expiration time of the token (exp claim)
         */
        'exp' => env('JWT_EXP_CLAIM', '+10 minute'),
    ]


];
