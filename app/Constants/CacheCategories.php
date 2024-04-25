<?php

namespace App\Constants;

class CacheCategories {
    public const FLASH_MESSAGES = 'flashMessages';
    public const RIBBONS = 'ribbons';
    public const PAGES = 'pages';

    public static array $all = [
        self::FLASH_MESSAGES,
        self::RIBBONS,
        self::PAGES
    ];
}

?>