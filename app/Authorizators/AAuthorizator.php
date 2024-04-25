<?php

namespace App\Authorizators;

use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Entities\User;

/**
 * Abstract class that is common for all authorizators
 * 
 * @author Lukas Velek
 */
abstract class AAuthorizator {
    protected const VIEW = 'can_see';
    protected const EDIT = 'can_edit';
    protected const DELETE = 'can_delete';

    private Database $db;
    protected Logger $logger;

    /**
     * Common constructor for all authorizators
     * 
     * @param Database $db Database instance
     * @param Logger $logger Logger instance
     * @param User|null $user Current user instance
     */
    protected function __construct(Database $db, Logger $logger) {
        $this->db = $db;
        $this->logger = $logger;
    }

    /**
     * Returns a QueryBuilder instance
     * 
     * @param string $methodName Name of the calling method - for logging purposes
     * @return QueryBuilder QueryBuilder instance
     */
    protected function qb(string $methodName) {
        $qb = $this->db->createQueryBuilder();
        $qb->setCallingMethod($methodName);

        return $qb;
    }
}

?>