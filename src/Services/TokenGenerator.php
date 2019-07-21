<?php

namespace App\Services;

use Exception;

class TokenGenerator
{
    const CHAR_LOWER   = 1;
    const CHAR_UPPER   = 2;
    const CHAR_NUMERIC = 4;
    const CHAR_SPECIAL = 8;

    /**
     * Generate string consisted of only lowercase, uppercase characters and numbers
     * @param int $length
     * @return string
     * @throws Exception
     */
    public function generateToken(int $length): string
    {
        return $this->generateCustomToken($length, self::CHAR_LOWER | self::CHAR_UPPER | self::CHAR_NUMERIC);
    }

    /**
     * Generate string consisted of only hexadecimal characters [0-9a-f]
     * @param int $length
     * @return string
     * @throws Exception
     */
    public function generateHexadecimalToken(int $length = 10): string
    {
        if ($length > 64 || $length < 1) {
            throw new \InvalidArgumentException('Length must be an integer between 1 and 64');
        }

        return substr(bin2hex(random_bytes((int) ceil($length / 2))), 0, $length);
    }

    /**
     * Generate password consisted of minimum one of each characters: lowercase, uppercase and number
     * @param int $length
     * @return string
     * @throws Exception
     */
    public function generatePassword(int $length): string
    {
        return $this->generateCustomPassword($length, 1, 1, 1, 0);
    }

    /**
     * @param int $length
     * @param $lower
     * @param $upper
     * @param $numeric
     * @param $special
     * @return string
     * @throws Exception
     */
    public function generateCustomPassword(int $length, int $lower, int $upper, int $numeric, int $special): string
    {
        if ($length < $lower + $upper + $numeric + $special) {
            throw new Exception('Length can not be less then sum of characters');
        }

        $characters = '';
        $flags = 0;
        if ($lower) {
            $characters .= $this->generateCustomToken($lower, self::CHAR_LOWER);
            $flags += self::CHAR_LOWER;
        }
        if ($upper) {
            $characters .= $this->generateCustomToken($upper, self::CHAR_UPPER);
            $flags += self::CHAR_UPPER;
        }
        if ($numeric) {
            $characters .= $this->generateCustomToken($numeric, self::CHAR_NUMERIC);
            $flags += self::CHAR_NUMERIC;
        }
        if ($special) {
            $characters .= $this->generateCustomToken($special, self::CHAR_SPECIAL);
            $flags += self::CHAR_SPECIAL;
        }

        if ($length - strlen($characters)) {
            if (!$flags) {
                $flags = self::CHAR_LOWER | self::CHAR_UPPER | self::CHAR_NUMERIC | self::CHAR_SPECIAL;
            }
            $characters .= $this->generateCustomToken($length - strlen($characters), $flags);
        }

        return str_shuffle($characters);
    }

    /**
     * @param int $length
     * @param $flags
     * @return string
     * @throws Exception
     */
    public function generateCustomToken(int $length, $flags): string
    {
        $token = "";
        $characters = $this->getCharacters($flags);
        for ($i = 0; $i < $length; $i++) {
            $token .= $characters[\random_int(0, \strlen($characters) - 1)];
        }
        return $token;
    }

    private function getCharacters($flags): string
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
            $characters .= '+-_=!#$%&?@~';
        }
        return $characters;
    }
}
