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
}

?>