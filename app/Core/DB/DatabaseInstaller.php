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
        
        $this->logger->setType(LogFileTypes::DEFAULT);
    }

    /**
     * Creates the database tables
     * 
     * @return true
     */
    private function createTables() {
        /*$tables = array(
            'users' => array(
                'id' => 'INT(32) NOT NULL PRIMARY KEY AUTO_INCREMENT',
                'firstname' => 'VARCHAR(256) NOT NULL',
                'lastname' => 'VARCHAR(256) NOT NULL',
                'username' => 'VARCHAR(256) NOT NULL',
                'password' => 'VARCHAR(256) NULL',
                'status' => 'INT(2) NOT NULL DEFAULT 1',
                'email' => 'VARCHAR(256) NULL',
                'address_street' => 'VARCHAR(256) NULL',
                'address_house_number' => 'VARCHAR(256) NULL',
                'address_city' => 'VARCHAR(256) NULL',
                'address_zip_code' => 'VARCHAR(256) NULL',
                'address_country' => 'VARCHAR(256) NULL',
                'date_created' => 'DATETIME NOT NULL DEFAULT current_timestamp()',
                'date_password_changed' => 'DATETIME NOT NULL',
                'password_change_status' => 'INT(2) NOT NULL DEFAULT 1',
                'default_user_page_url' => 'VARCHAR(256) NULL',
                'date_updated' => 'DATETIME NOT NULL DEFAULT current_timestamp()',
                'default_user_datetime_format' => 'VARCHAR(256) NULL',
                'last_login_hash' => 'VARCHAR(256) NULL'
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
        }*/

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
}

?>