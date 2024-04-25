<?php

namespace App\Helpers;

use App\Core\Datetime;
use App\Entities\User;
use App\Entities\UserEntity;

/**
 * Datetime format helper
 * 
 * @author Lukas Velek
 */
class DatetimeFormatHelper {
    /**
     * Formats datetime by user's default format
     * 
     * @param string $datetime Datetime
     * @param User $user User instance
     * @return string Formatted datetime
     */
    public static function formatDateByUserDefaultFormat(Datetime|string $datetime, UserEntity $user) {
        if($datetime == '-' || $datetime == '' || $datetime === NULL) {
            return $datetime;
        }

        $format = DEFAULT_DATETIME_FORMAT;

        if($user->getDefaultUserDateTimeFormat() !== NULL) {
            $format = $user->getDefaultUserDateTimeFormat();
        }

        return self::formatDateByFormat($datetime, $format);
    }

    /**
     * Formats datetime by given format
     * 
     * @param string $datetime Datetime
     * @param string $format Datetime format
     * @return string Formatted datetime
     */
    public static function formatDateByFormat(Datetime|string $datetime, string $format) {
        if($datetime == '-' || $datetime == '' || $datetime === NULL) {
            return $datetime;
        }

        return date($format, strtotime($datetime));
    }
}

?>