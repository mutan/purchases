<?php

namespace App\Helpers;

use ReCaptcha\ReCaptcha;

class CaptchaValidator
{
    private $key;
    private $secret;

    public function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    public function validateCaptcha($gRecaptchaResponse)
    {
        $recaptcha = new ReCaptcha($this->secret);
        $resp = $recaptcha->verify($gRecaptchaResponse);
        return $resp->isSuccess();
    }

    public function getKey(): string
    {
        return $this->key;
    }
}