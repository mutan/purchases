<?php

namespace App\Resources;

class ItemCode
{
    const TYPE_ORDER = 'OR';
    const TYPE_PACKAGE = 'P';
    const TYPE_PRODUCT = 'G';
    const TYPE_USER = 'U';
    const TYPE_USER_ADDRESS = 'UA';
    const TYPE_USER_PASSPORT = 'UP';

    const ALLOWED_TYPES = [self::TYPE_ORDER, self::TYPE_PACKAGE, self::TYPE_PRODUCT,
        self::TYPE_USER, self::TYPE_USER_ADDRESS, self::TYPE_USER_PASSPORT];

    /** @var string */
    private $type = '';
    /** @var string */
    private $number = '';

    public function __construct(array $itemCodeData)
    {
        if (is_array($itemCodeData)) {
            if (array_key_exists('type', $itemCodeData)) {
                $this->type = strtoupper($itemCodeData['type']);
            }
            if (array_key_exists('number', $itemCodeData)) {
                $this->number = intval($itemCodeData['number']);
            }
        }
    }

    public function __toString(): string
    {
        return $this->getCode();
    }

    public function getCode(): string
    {
        if ($this->type && $this->number) {
            return $this->type . $this->number;
        }
        return '';
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function isOrder(): bool
    {
        return $this->type == self::TYPE_ORDER;
    }

    public function isPackage(): bool
    {
        return $this->type == self::TYPE_PACKAGE;
    }

    public function isProduct(): bool
    {
        return $this->type == self::TYPE_PRODUCT;
    }

    public function isUser(): bool
    {
        return $this->type == self::TYPE_USER;
    }

    public function isUserAddress(): bool
    {
        return $this->type == self::TYPE_USER_ADDRESS;
    }

    public function isUserPassport(): bool
    {
        return $this->type == self::TYPE_USER_PASSPORT;
    }
}
