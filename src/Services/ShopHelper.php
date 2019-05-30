<?php

namespace App\Services;

class ShopHelper
{
    const SHOP_AMAZON = 'https://www.amazon.com';
    const SHOP_CHANNEL_FIREBALL = 'https://www.channelfireball.com';
    const SHOP_COOL_STUFF_INC = 'https://www.coolstuffinc.com';
    const SHOP_EBAY = 'https://www.ebay.com';
    const SHOP_MINIATURE_MARKET = 'https://www.miniaturemarket.com';
    const SHOP_ORIGINAL_MAGIC_ART = 'https://www.originalmagicart.store';
    const SHOP_STAR_CITY_GAMES = 'http://www.starcitygames.com';
    const SHOP_TROLL_AND_TOAD = 'https://www.trollandtoad.com';

    const SHOP_LIST_FOR_AUTOCOMPLETE = [
        self::SHOP_AMAZON,
        self::SHOP_CHANNEL_FIREBALL,
        self::SHOP_COOL_STUFF_INC,
        self::SHOP_EBAY,
        self::SHOP_MINIATURE_MARKET,
        self::SHOP_ORIGINAL_MAGIC_ART,
        self::SHOP_STAR_CITY_GAMES,
        self::SHOP_TROLL_AND_TOAD,
    ];

    const SHOP_LOGOS_DIR = 'img' . DIRECTORY_SEPARATOR . 'shop-logos' . DIRECTORY_SEPARATOR;

    const SHOP_LIST_LOGOS = [
        self::SHOP_AMAZON => '',
        self::SHOP_CHANNEL_FIREBALL => '',
        self::SHOP_COOL_STUFF_INC => 'coolstuffinc-logo.png',
        self::SHOP_EBAY => '',
        self::SHOP_MINIATURE_MARKET => '',
        self::SHOP_ORIGINAL_MAGIC_ART => '',
        self::SHOP_STAR_CITY_GAMES => '',
        self::SHOP_TROLL_AND_TOAD => 'trollandtoad-logo.png',
    ];

    public static function getLogo($shop)
    {
        return array_key_exists($shop, self::SHOP_LIST_LOGOS)
            ? self::SHOP_LOGOS_DIR . self::SHOP_LIST_LOGOS[$shop]
            : '';
    }
}