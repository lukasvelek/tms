<?php

namespace App\Entities;

class TicketEntity extends AEntity {
    private string $title;
    private string $description;
    private int $idAuthor;
    private ?int $idResolver;
    private int $status;

    public function __construct(int $id, string $dateCreated, string $dateUpdated, string $title, string $description, int $idAuthor, ?int $idResolver, int $status) {
        parent::__construct($id, $dateCreated, $dateUpdated);

        $this->title = $title;
        $this->description = $description;
        $this->idAuthor = $idAuthor;
        $this->idResolver = $idResolver;
        $this->status = $status;
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
}

?>