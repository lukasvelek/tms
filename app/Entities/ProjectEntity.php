<?php

namespace App\Entities;

class ProjectEntity extends AEntity {
    private string $name;
    private int $idClient;

    public function __construct(int $id, string $dateCreated, string $name, int $idClient) {
        parent::__construct($id, $dateCreated, null);

        $this->name = $name;
        $this->idClient = $idClient;
    }

    public function getName() {
        return $this->name;
    }

    public function getIdClient() {
        return $this->idClient;
    }
}

?>