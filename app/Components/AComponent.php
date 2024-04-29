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

    protected function get(string $varName) {
        if(isset($_GET[$varName])) {
            return htmlspecialchars($_GET[$varName]);
        } else {
            return null;
        }
    }

    protected function post(string $varName) {
        if(isset($_POST[$varName])) {
            return htmlspecialchars($_POST[$varName]);
        } else {
            return null;
        }
    }
}

?>