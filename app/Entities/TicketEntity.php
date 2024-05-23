<?php

namespace App\Entities;

class TicketEntity extends AEntity {
    private string $title;
    private string $description;
    private int $idAuthor;
    private ?int $idResolver;
    private int $status;
    private ?string $dateDue;

    public function __construct(int $id, string $dateCreated, string $dateUpdated, string $title, string $description, int $idAuthor, ?int $idResolver, int $status, ?string $dateDue) {
        parent::__construct($id, $dateCreated, $dateUpdated);

        $this->title = $title;
        $this->description = $description;
        $this->idAuthor = $idAuthor;
        $this->idResolver = $idResolver;
        $this->status = $status;
        $this->dateDue = $dateDue;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getIdAuthor() {
        return $this->idAuthor;
    }

    public function getIdResolver() {
        return $this->idResolver;
    }

    public function getStatus() {
        return $this->status;
    }

    public static function createTicketEntityFromDbRow($row) {
        return new self($row['id'], $row['date_created'], $row['date_updated'], $row['title'], $row['description'], $row['id_author'], $row['id_resolver'], $row['status'], $row['date_due']);
    }
}

?>