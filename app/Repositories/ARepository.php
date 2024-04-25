<?php

namespace App\Repositories;

use App\Core\DB\Database;
use App\Core\Logger\Logger;
use QueryBuilder\ExpressionBuilder;

/**
 * Common class for all repositories
 * 
 * @author Lukas Velek
 */
abstract class ARepository {
    protected Database $db;
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
     * Creates QueryBuilder instance
     * 
     * @param string $methodName Name of the calling method
     * @return \QueryBuilder\QueryBuilder QueryBuilder instance
     */
    protected function qb(string $methodName) {
        $qb = $this->db->createQueryBuilder();
        $qb->setCallingMethod($methodName);

        return $qb;
    }

    /**
     * Creates ExpressionBuilder instance
     * 
     * @return ExpressionBuilder ExpressionBuilder instance
     */
    protected function xb() {
        return new ExpressionBuilder();
    }
}

?>