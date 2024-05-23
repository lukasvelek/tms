<?php

namespace App\Repositories;

use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Entities\TicketEntity;

class TicketRepository extends ARepository {
    public function __construct(Database $db, Logger $logger) {
        parent::__construct($db, $logger);
    }

    public function getAllUnassignedTickets() {
        $qb = $this->qb(__METHOD__);

        $qb ->select(['*'])
            ->from('tickets')
            ->where('id_resolver IS NULL')
            ->execute();

        $tickets = [];
        while($row = $qb->fetchAssoc()) {
            $tickets[] = TicketEntity::createTicketEntityFromDbRow($row);
        }

        return $tickets;
    }
}

?>