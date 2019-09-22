<?php

namespace App\Security;

/**
 * A class for create and control Token
 */
class TokenSecurity
{
    /**
     * Create a token with password_hash and random_bytes
     */
    public function generateToken()
    {
        return str_replace('_','', password_hash(random_bytes(10), PASSWORD_DEFAULT));
    }
}