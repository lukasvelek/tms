<?php

namespace App\Repositories;

use App\Constants\CacheCategories;
use App\Core\CacheManager;
use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Entities\UserEntity;

/**
 * User repository allows performing operations on users
 * 
 * @author Lukas Velek
 */
class UserRepository extends ARepository {
    private CacheManager $cm;

    /**
     * Class constructor
     * 
     * @param Database $db Database instance
     * @param Logger $logger Logger instance
     */
    public function __construct(Database $db, Logger $logger) {
        parent::__construct($db, $logger);

        $this->cm = CacheManager::getTemporaryObject(CacheCategories::USERS);
    }

    /**
     * Returns all users as an array of UserEntity instances
     * 
     * @return array Array of UserEntity instances
     */
    public function getAllUsers() {
        $qb = $this->composeQueryForGrid(__METHOD__);
        
        $qb->execute();

        $users = [];
        while($row = $qb->fetchAssoc()) {
            $users[] = UserEntity::createUserEntityFromDbRow($row);
        }

        return $users;
    }

    /**
     * Returns a single UserEntity instance or null
     * 
     * First it checks if the user is cached and if not it performs an database query
     * 
     * @param int $id User ID
     * @return UserEntity|null UserEntity instance or null
     */
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

    /**
     * Returns a QueryBuilder instance where only SELECT and FROM parts are defined
     * 
     * @param string|null $method Calling method name (if not given the composeQueryForGrid will be used)
     * @return QueryBuilder QueryBuilder instance
     */
    public function composeQueryForGrid(?string $method = null) {
        $qb = $this->qb($method ?? __METHOD__);

        $qb ->select(['*'])
            ->from('users');

        return $qb;
    }

    /**
     * Returns the count of all users
     * 
     * @return int Count of all users
     */
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

    /**
     * Inserts a user to the database
     * 
     * @param string $username User's username
     * @param string $fullname User's fullname
     * @param string $password User's password (hashed)
     * @return mixed Query result
     */
    public function createUser(string $username, string $fullname, string $password) {
        $qb = $this->qb(__METHOD__);

        $qb ->insert('users', ['username', 'fullname', 'password'])
            ->values([$username, $fullname, $password])
            ->execute();

        return $qb->fetch();
    }

    /**
     * Removes a user from the database
     * 
     * @param int $id User's ID
     * @return mixed Query result
     */
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

    /**
     * Returns UserEntity instances for users except for IDs given in the given array
     * 
     * @param array $ids Array of user IDs to remove from the query
     * @return array Array of UserEntity instances
     */
    public function getAllUsersExceptFor(array $ids) {
        $qb = $this->qb(__METHOD__);

        $qb ->select(['*'])
            ->from('users');

        if(!empty($ids)) {
            $qb->where($qb->getColumnNotInValues('id', $ids));
        }

        $qb->execute();

        $users = [];
        while($row = $qb->fetchAssoc()) {
            $users[] = UserEntity::createUserEntityFromDbRow($row);
        }

        return $users;
    }

    /**
     * Returns UserEntity instances for users with IDs within the given array
     * 
     * @param array $ids Array of user IDs
     * @return array Array of UserEntity instances
     */
    public function getUsersByIds(array $ids) {
        if(empty($ids)) {
            return [];
        }

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