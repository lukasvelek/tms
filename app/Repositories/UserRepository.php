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

    public function getUserById(int $id) {
        $cachedUser = $this->cm->loadUserByIdFromCache($id);

        if($cachedUser !== NULL) {
            return $cachedUser;
        } else {
            $qb = $this->qb(__METHOD__);

            $qb ->select(['*'])
                ->from('users')
                ->where('id = ?', [$id])
                ->execute();

            $row = $qb->fetch();

            $user = UserEntity::createUserEntityFromDbRow($row);

            $this->cm->saveUserToCache($user);

            return $user;
        }
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
}

?>