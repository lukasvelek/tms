<?php

namespace App\Entities;

class TicketCommentEntity extends AEntity {
    private int $idAuthor;
    private int $idTicket;
    private string $text;

    public function __construct(int $id, string $dateCreated, int $idAuthor, int $idTicket, string $text) {
        parent::__construct($id, $dateCreated, null);

        $this->idAuthor = $idAuthor;
        $this->idTicket = $idTicket;
        $this->text = $text;
    }

    public function getIdAuthor() {
        return $this->idAuthor;
    }

    public function getIdTicket() {
        return $this->idTicket;
    }

    public function getText() {
        return $this->text;
    }
}

?>