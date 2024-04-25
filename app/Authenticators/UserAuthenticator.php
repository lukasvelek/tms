<?php

namespace App\Authenticators;

use App\Core\DB\Database;
use App\Core\Logger\Logger;

class UserAuthenticator extends AAuthenticator {
    public function __construct(Database $db, Logger $logger) {
        parent::__construct($db, $logger);
    }

    public function authenticateUser(string $username, string $password) {
        $qb = $this->qb(__METHOD__);

        $qb ->select(['id', 'password'])
            ->from('users')
            ->where('username = ?', [$username])
            ->execute();

        while($row = $qb->fetchAssoc()) {
            if(password_verify($password, $row['password'])) {
                return $row['id'];
            } else {
                return null;
            }
        }
    }
}

?>