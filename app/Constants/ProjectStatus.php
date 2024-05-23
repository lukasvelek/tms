<?php

namespace App\Constants;

class ProjectStatus {
    public const OPPORTUNITY = 1;
    public const IN_REALIZATION = 2;
    public const FINISHED = 3;

    public static function toString(int $status) {
        return match($status) {
            self::OPPORTUNITY => 'Business opportunity',
            self::IN_REALIZATION => 'In realization',
            self::FINISHED => 'Finished'
        };
    }

    public static function getAll() {
        return [
            self::OPPORTUNITY => self::toString(self::OPPORTUNITY),
            self::IN_REALIZATION => self::toString(self::IN_REALIZATION),
            self::FINISHED => self::toString(self::FINISHED)
        ];
    }
}

?>