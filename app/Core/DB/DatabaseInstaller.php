<?php

namespace App\Core\DB;

use App\Constants\ArchiveStatus;
use App\Constants\ArchiveType;
use App\Constants\BulkActionRights;
use App\Constants\DocumentAfterShredActions;
use App\Constants\DocumentRank;
use App\Constants\DocumentShreddingStatus;
use App\Constants\DocumentStatus;
use App\Constants\FileStorageSystemLocations;
use App\Constants\ProcessStatus;
use App\Constants\ProcessTypes;
use App\Constants\Ribbons;
use App\Constants\UserActionRights;
use App\Constants\UserStatus;
use App\Core\AppConfiguration;
use App\Core\CryptManager;
use App\Core\Logger\LogFileTypes;
use App\Core\Logger\Logger;

/**
 * Database installation definition
 * 
 * @author Lukas Velek
 */
class DatabaseInstaller {
    private Database $db;
    private Logger $logger;

    /**
     * Class constructor
     * 
     * @param Database $db Database instance
     * @param Logger $logger Logger instance
     */
    public function __construct(Database $db, Logger $logger) {
        $this->db = $db;
        $this->logger = $logger;
    }

    /**
     * Installs the database
     */
    public function install() {
        $this->logger->setType(LogFileTypes::INSTALL);

        $this->createTables();
        $this->createIndexes();

        $this->insertDefaultUsers();
        
        $this->logger->setType(LogFileTypes::DEFAULT);
    }

    /**
     * Creates the database tables
     * 
     * @return true
     */
    private function createTables() {
        $tables = array(
            'users' => array(
                'id' => 'INT(32) NOT NULL PRIMARY KEY AUTO_INCREMENT',
                'fullname' => 'VARCHAR(256) NOT NULL',
                'username' => 'VARCHAR(256) NOT NULL',
                'password' => 'VARCHAR(256) NULL',
                'email' => 'VARCHAR(256) NULL',
                'date_created' => 'DATETIME NOT NULL DEFAULT current_timestamp()',
                'date_updated' => 'DATETIME NOT NULL DEFAULT current_timestamp()',
                'last_login_hash' => 'VARCHAR(256) NULL'
            ),
            'clients' => array(
                'id' => 'INT(32) NOT NULL PRIMARY KEY AUTO_INCREMENT',
                'name' => 'VARCHAR(256) NOT NULL',
                'id_manager' => 'INT(32) NOT NULL',
                'date_created' => 'DATETIME NOT NULL DEFAULT current_timestamp()'
            ),
            'projects' => array(
                'id' => 'INT(32) NOT NULL PRIMARY KEY AUTO_INCREMENT',
                'name' => 'VARCHAR(256) NOT NULL',
                'id_client' => 'INT(32) NOT NULL',
                'date_created' => 'DATETIME NOT NULL DEFAULT current_timestamp()'
            ),
            'client_users' => array(
                'id' => 'INT(32) NOT NULL PRIMARY KEY AUTO_INCREMENT',
                'id_client' => 'INT(32) NOT NULL',
                'id_user' => 'INT(32) NOT NULL'
            )
        );

        foreach($tables as $table => $columns) {
            $col = '';

            $i = 0;
            foreach($columns as $columnName => $columnValue) {
                $column = '`' . $columnName . '` ' . $columnValue;

                if(($i + 1) == count($columns)) {
                    $col .= $column;
                } else {
                    $col .= $column . ', ';
                }

                $i++;
            }

            $sql = 'CREATE TABLE IF NOT EXISTS `' . $table . '` (' . $col . ')';

            $this->logger->sql($sql, __METHOD__);

            $this->db->query($sql);
        }

        return true;
    }

    /**
     * Inserts indexes for selected database tables
     * 
     * @return true
     */
    private function createIndexes() {
        /*$indexes = [
            [
                'table_name' => 'documents',
                'columns' => [
                    'id_folder'
                ]
            ]
        ];

        $tables = [];
        foreach($indexes as $array) {
            $tableName = $array['table_name'];
            $columns = $array['columns'];

            $c = 1;
            foreach($tables as $table) {
                if($table == $tableName) {
                    $c++;
                }
            }
            $tables[] = $tableName;

            $sql = 'CREATE INDEX `$INDEX_NAME$` ON `$TABLE_NAME$` (';

            $params = [
                '$INDEX_NAME$' => $tableName . '_' . $c,
                '$TABLE_NAME$' => $tableName
            ];

            foreach($params as $paramName => $paramValue) {
                $sql = str_replace($paramName, $paramValue, $sql);
            }

            $i = 0;
            foreach($columns as $col) {
                if(($i + 1) == count($columns)) {
                    $sql .= $col . ')';
                } else {
                    $sql .= $col . ', ';
                }

                $i++;
            }

            $this->logger->sql($sql, __METHOD__);
            $this->db->query($sql);
        }*/

        return true;
    }

    private function insertDefaultUsers() {
        $users = [
            [
                'username' => 'admin',
                'fullname' => 'Administrator',
                'password' => password_hash('admin', PASSWORD_BCRYPT)
            ]
        ];

        foreach($users as $user) {
            $sql = "INSERT INTO `users` (`username`, `fullname`, `password`) VALUES ('";
            $sql .= $user['username'] . '\', \'' . $user['fullname'] . '\', \'' . $user['password'] . '\')';
            $this->logger->sql($sql, __METHOD__);
            $this->db->query($sql);
        }

        return true;
    }
}

?>