<?php

namespace App\Repositories;

use App\Constants\CacheCategories;
use App\Core\CacheManager;
use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Entities\UserEntity;

class UserRepository extends ARepository {
    private CacheManager $cm;

    public function __construct(Database $db, Logger $logger) {
        parent::__construct($db, $logger);

        $this->cm = CacheManager::getTemporaryObject(CacheCategories::USERS);
    }

    public function getAllUsers() {
        $qb = $this->composeQueryForGrid(__METHOD__);
        
        $qb->execute();

        $users = [];
        while($row = $qb->fetchAssoc()) {
            $users[] = UserEntity::createUserEntityFromDbRow($row);
        }

        return $users;
    }

    public function getUserById(int $id) {
        return $this->cm->loadUser($id, function() use ($id) {
            $qb = $this->qb(__METHOD__);

            $qb ->select(['*'])
                ->from('users')
                ->where('id = ?', [$id])
                ->execute();

            $user = null;
            while($row = $qb->fetchAssoc()) {
                $user = UserEntity::createUserEntityFromDbRow($row);
            }
            
            return $user;
        });
    }

    public function composeQueryForGrid(?string $method = null) {
        $qb = $this->qb($method ?? __METHOD__);

        $qb ->select(['*'])
            ->from('users');

        return $qb;
    }

    public function getUserCount() {
        $qb = $this->qb(__METHOD__);

        $qb ->select(['COUNT(id) AS cnt'])
            ->from('users')
            ->execute();

        $count = 0;

        while($row = $qb->fetchAssoc()) {
            $count = $row['cnt'];
        }

        return $count;
    }

    public function createUser(string $username, string $fullname, string $password) {
        $qb = $this->qb(__METHOD__);

        $qb ->insert('users', ['username', 'fullname', 'password'])
            ->values([$username, $fullname, $password])
            ->execute();

        return $qb->fetch();
    }

    public function deleteUser(int $id) {
        $qb = $this->qb(__METHOD__);

        $qb ->delete()
            ->from('users')
            ->where('id = ?', [$id])
            ->execute();

        $qb->fetch();

        $qb->clean();

        $qb ->delete()
            ->from('client_users')
            ->where('id_user = ?', [$id])
            ->execute();

        return $qb->fetch();
    }

    public function getAllUsersExceptFor(array $ids) {
        $qb = $this->qb(__METHOD__);

        $qb ->select(['*'])
            ->from('users')
            ->where($qb->getColumnNotInValues('id', $ids))
            ->execute();

        $users = [];
        while($row = $qb->fetchAssoc()) {
            $users[] = UserEntity::createUserEntityFromDbRow($row);
        }

        return $users;
    }

    public function getUsersByIds(array $ids) {
        $qb = $this->qb(__METHOD__);

        $qb ->select(['*'])
            ->from('users')
            ->where($qb->getColumnInValues('id', $ids))
            ->execute();

        $users = [];
        while($row = $qb->fetchAssoc()) {
            $users[] = UserEntity::createUserEntityFromDbRow($row);
        }

        return $users;
    }
}

?>