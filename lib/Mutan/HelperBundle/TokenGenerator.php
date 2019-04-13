<?php

namespace Mutan\HelperBundle;

class TokenGenerator
{
    const ALPHABET_TYPE_FULL = 'full';
    const ALPHABET_TYPE_NUMERIC = 'numeric';
    const ALPHABET_TYPE_NUMERIC_NO_ZERO = 'nozero';


    private $length;

    public function __construct(int $length = 32)
    {
        $this->length = $length;
    }

    /**
     * @param $length
     * @return string
     * @throws \Exception
     */
    public function getSecureToken($length): string
    {
        return bin2hex(\random_bytes($length));
    }

    /**
     * @param $length
     * @return string
     * @throws \Exception
     */
    public function getToken($length)
    {
        $token = "";
        $alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $alphabet.= "abcdefghijklmnopqrstuvwxyz";
        $alphabet.= "0123456789";

        $alphabet = $this->getAlphabet();
        $max = strlen($alphabet);

        for ($i=0; $i < $length; $i++) {
            $token .= $alphabet[random_int(0, $max-1)];
        }

        return $token;
    }

    private function getAlphabet()
    {

        return ;
    }
}
