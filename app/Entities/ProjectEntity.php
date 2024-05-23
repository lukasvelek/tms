<?php

namespace App\Entities;

class ProjectEntity extends AEntity {
    private string $name;
    private int $idClient;
    private int $status;
    private int $idProjectManager;

    public function __construct(int $id, string $dateCreated, string $name, int $idClient, int $status, int $idProjectManager) {
        parent::__construct($id, $dateCreated, null);

        $this->name = $name;
        $this->idClient = $idClient;
        $this->status = $status;
        $this->idProjectManager = $idProjectManager;
    }

    public function getName() {
        return $this->name;
    }

    public function getIdClient() {
        return $this->idClient;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getIdProjectManager() {
        return $this->idProjectManager;
    }

    public static function createProjectEntityFromDbRow($row) {
        return new self($row['id'], $row['date_created'], $row['name'], $row['id_client'], $row['status'], $row['id_project_manager']);
    }
}

?>