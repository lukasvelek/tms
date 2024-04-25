<?php

namespace App\Entities;

class UserEntity extends AEntity {
    private string $username;
    private string $fullname;
    private ?string $email;

    public function __construct(int $id, string $dateCreated, string $dateUpdated, string $username, string $fullname, ?string $email = null) {
        parent::__construct($id, $dateCreated, $dateUpdated);

        $this->username = $username;
        $this->fullname = $fullname;
        $this->email = $email;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getFullname() {
        return $this->fullname;
    }
    
    public function getEmail() {
        return $this->email;
    }
}

?>