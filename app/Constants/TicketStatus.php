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
        switch($status) {
            case self::NEW:
                return 'New';

            case self::IN_PROGRESS:
                return 'In progress';

            case self::WAITING_FOR_CUSTOMER:
                return 'Waiting for customer';

            case self::CANCELLED:
                return 'Cancelled';

            case self::CLOSED:
                return 'Closed';

            case self::WAITING_FOR_APPROVAL:
                return 'Waiting for approval';

            case self::APPROVED:
                return 'Approved';

            case self::BACKLOG:
                return 'Backlog';

            default:
                return 'Unknown';
        }
    }
}

?>