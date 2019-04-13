<?php

namespace Mutan\HelperBundle;

class TokenGenerator
{
    const CHAR_LOWER   = 1;
    const CHAR_UPPER   = 2;
    const CHAR_NUMERIC = 4;
    const CHAR_SPECIAL = 8;

    /**
     * If $length is less then 32, it's set to 32
     * @param int $length
     * @return string
     * @throws \Exception
     */
    public function getHexadecimalToken(int $length = 32): string
    {
        $length = \intval($length/2);
        if ($length < 16) {
            $length = 16;
        }
        return \bin2hex(\random_bytes($length));
    }

    /**
     * @param int $length
     * @return string
     * @throws \Exception
     */
    public function getToken(int $length): string
    {
        return $this->getCustomToken($length, self::CHAR_LOWER | self::CHAR_UPPER | self::CHAR_NUMERIC);
    }

    public function getCustomToken(int $length, $flags): string
    {
        $token = "";
        $characters = $this->getCharacters($flags);
        for ($i = 0; $i < $length; $i++) {
            $token .= $characters[\random_int(0, \strlen($characters) - 1)];
        }
        return $token;
    }

    private function getCharacters($flags)
    {
        $characters = '';
        if ($flags & self::CHAR_LOWER) {
            $characters .= 'abcdefghijklmnopqrstuvwxyz';
        }
        if ($flags & self::CHAR_UPPER) {
            $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        if ($flags & self::CHAR_NUMERIC) {
            $characters .= '0123456789';
        }
        if ($flags & self::CHAR_SPECIAL) {
            $characters .= '_+-=!#$%&?@~';
        }
        return $characters;
    }
}
