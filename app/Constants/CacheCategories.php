<?php

namespace App\Constants;

class CacheCategories {
    public const FLASH_MESSAGES = 'flashMessages';
    public const RIBBONS = 'ribbons';
    public const PAGES = 'pages';
    public const USERS = 'users';
    public const CLIENTS = 'clients';

    public static array $all = [
        self::FLASH_MESSAGES,
        self::RIBBONS,
        self::PAGES,
        self::USERS,
        self::CLIENTS
    ];
}

?>