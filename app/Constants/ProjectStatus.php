<?php

namespace App\Constants;

class ProjectStatus {
    public const IN_REALIZATION = 1;
    public const FINISHED = 2;
    public const OPPORTUNITY = 3;

    public static function toString(int $status) {
        return match($status) {
            self::IN_REALIZATION => 'In realization',
            self::FINISHED => 'Finished',
            self::OPPORTUNITY => 'Business opportunity'
        };
    }
}

?>