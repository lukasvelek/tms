<?php

namespace App\Components;

use App\Core\DB\Database;
use App\Core\Logger\Logger;

/**
 * Common abstract class for all components
 * 
 * @author Lukas Velek
 */
abstract class AComponent {
    private Database $db;
    protected Logger $logger;

    /**
     * Class constructor
     * 
     * @param Database $db Database instance
     * @param Logger $logger Logger instance
     */
    protected function __construct(Database $db, Logger $logger) {
        $this->db = $db;
        $this->logger = $logger;
    }

    /**
     * Returns a QueryBuilder instance
     * 
     * @param string $methodName Calling method name
     * @return QueryBuilder QueryBuilder instance
     */
    protected function qb(string $methodName) {
        $qb = $this->db->createQueryBuilder();
        $qb->setCallingMethod($methodName);

        return $qb;
    }
}

?>