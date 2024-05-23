<?php

namespace App\Constants;

class TicketStatus {
    public const NEW = 1;
    public const IN_PROGRESS = 2;
    public const WAITING_FOR_CUSTOMER = 3;
    public const CANCELLED = 4;
    public const CLOSED = 5;
    public const WAITING_FOR_APPROVAL = 6;
    public const APPROVED = 7;
    public const BACKLOG = 8;

    public static function toString(int $status) {
        return match($status) {
            self::NEW => 'New',
            self::IN_PROGRESS => 'In progress',
            self::WAITING_FOR_CUSTOMER => 'Waiting for customer',
            self::CANCELLED => 'Cancelled',
            self::CLOSED => 'Closed',
            self::WAITING_FOR_APPROVAL => 'Waiting for approval',
            self::APPROVED => 'Approved',
            self::BACKLOG => 'Backlog',
            default => '-'
        };
    }
}

?>