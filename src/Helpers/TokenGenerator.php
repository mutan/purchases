<?php

namespace App\Helpers;

class TokenGenerator
{
    public function generateToken($length = 32) : string
    {
        return bin2hex(\random_bytes($length));
    }
}
