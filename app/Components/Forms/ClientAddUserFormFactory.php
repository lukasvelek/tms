<?php

namespace App\Components\Forms;

use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Repositories\UserRepository;

class ClientAddUserFormFactory extends AFormFactory {
    private UserRepository $userRepository;
    private ?int $idClient;

    public function __construct(Database $db, Logger $logger, UserRepository $userRepository, string $formHandlerUrl = '', ?int $idClient = null) {
        parent::__construct($db, $logger, $formHandlerUrl);

        $this->userRepository = $userRepository;
        $this->idClient = $idClient;

        $this->createForm();
    }

    private function createForm() {
        
    }
}

?>