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
                ->from('clients')
                ->where('id = ?', [$id])
                ->execute();

            $client = null;
            while($row = $qb->fetchAssoc()) {
                $client = ClientEntity::createClientEntityFromDbRow($row);
            }

            return $client;
        });
    }

    public function composeQueryForGrid(?string $method = null) {
        $qb = $this->qb($method ?? __METHOD__);

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

    public function createClient(string $clientName, int $idManager) {
        $qb = $this->qb(__METHOD__);

        $qb ->insert('clients', ['name', 'id_manager'])
            ->values([$clientName, $idManager])
            ->execute();

        return $qb->fetch();
    }

    public function deleteClient(int $id) {
        $qb = $this->qb(__METHOD__);

        $qb ->delete()
            ->from('clients')
            ->where('id = ?', [$id])
            ->execute();

        $qb->fetch();

        $this->cm->invalidateCache();

        $qb->clean();

        $qb ->delete()
            ->from('client_users')
            ->where('id_client = ?', [$id])
            ->execute();

        return $qb->fetch();
    }

    public function getUsersForClient(int $id) {
        $qb = $this->qb(__METHOD__);

        $qb ->select(['*'])
            ->from('client_users')
            ->where('id_client = ?', [$id])
            ->execute();

        $users = [];
        while($row = $qb->fetchAssoc()) {
            $users[] = $row['id_user'];
        }

        return $users;
    }

    public function getUserCountForClient(int $id) {
        $qb = $this->qb(__METHOD__);

        $qb ->select(['id_user'])
            ->from('client_users')
            ->where('id_client = ?', [$id])
            ->execute();

        if($qb->fetchAll() !== NULL) {
            return $qb->fetchAll()->num_rows;
        }
    }

    public function addUserToClient(int $idClient, int $idUser) {
        $qb = $this->qb(__METHOD__);

        $qb ->insert('client_users', ['id_client', 'id_user'])
            ->values([$idClient, $idUser])
            ->execute();

        return $qb->fetch();
    }

    public function removeUserFromClient(int $idClient, int $idUser) {
        $qb = $this->qb(__METHOD__);
        
        $qb ->delete()
            ->from('client_users')
            ->where('id_user = ?', [$idUser])
            ->andWhere('id_client = ?', [$idClient])
            ->execute();

        return $qb->fetch();
    }
}

?>