<?php

namespace App\Services;

use ReCaptcha\ReCaptcha;

// TODO настроить рекапчу https://www.google.com/recaptcha/admin

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