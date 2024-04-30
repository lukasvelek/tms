<?php

namespace App\Repositories;

use App\Constants\CacheCategories;
use App\Core\CacheManager;
use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Entities\ClientEntity;

class ClientRepository extends ARepository {
    private CacheManager $cm;

    public function __construct(Database $db, Logger $logger) {
        parent::__construct($db, $logger);

        $this->cm = CacheManager::getTemporaryObject(CacheCategories::CLIENTS);
    }

    public function getClientById(int $id) {
        return $this->cm->loadClient($id, function() use ($id) {
            $qb = $this->qb(__METHOD__);

            $qb ->select(['*'])
                ->from('users')
                ->where('id = ?', [$id])
                ->execute();

            $client = null;
            while($row = $qb->fetchAssoc()) {
                $client = ClientEntity::createClientEntityFromDbRow($row);
            }

            return $client;
        });
    }

    public function composeClientQuery() {
        $qb = $this->qb(__METHOD__);

        $qb ->select(['*'])
            ->from('clients');

        return $qb;
    }

    public function getClientCount() {
        $qb = $this->qb(__METHOD__);

        $qb ->select(['COUNT(id) AS cnt'])
            ->from('clients')
            ->execute();

        $count = 0;

        while($row = $qb->fetchAssoc()) {
            $count = $row['cnt'];
        }

        return $count;
    }
}

?>