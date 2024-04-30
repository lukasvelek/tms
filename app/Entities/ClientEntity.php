<?php

namespace App\Entities;

class ClientEntity extends AEntity {
    private string $name;
    private int $idManager;
    
    public function __construct(int $id, string $dateCreated, string $name, int $idManager) {
        parent::__construct($id, $dateCreated, null);

        $this->name = $name;
        $this->idManager = $idManager;
    }

    public function getName() {
        return $this->name;
    }

    public function getIdManager() {
        return $this->idManager;
    }

    public static function createClientEntityFromDbRow($row) {
        $id = $row['id'];
        $dateCreated = $row['date_created'];
        $name = $row['name'];
        $idManager = $row['id_manager'];

        return new self($id, $dateCreated, $name, $idManager);
    }
}

?>