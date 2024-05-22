<?php

namespace App\Components\Forms;

use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Repositories\ClientRepository;
use App\Repositories\UserRepository;

class ClientAddUserFormFactory extends AFormFactory {
    private UserRepository $userRepository;
    private ClientRepository $clientRepository;
    private int $idClient;

    public function __construct(Database $db, Logger $logger, UserRepository $userRepository, ClientRepository $clientRepository, string $formHandlerUrl = '', int $idClient) {
        parent::__construct($db, $logger, $formHandlerUrl);

        $this->userRepository = $userRepository;
        $this->clientRepository = $clientRepository;
        $this->idClient = $idClient;

        $this->createForm();
    }

    private function createForm() {
        $users = [];

        $idAddedUsers = $this->clientRepository->getUsersForClient($this->idClient);

        $dbUsers = $this->userRepository->getAllUsersExceptFor($idAddedUsers);

        foreach($dbUsers as $dbUser) {
            $users[] = [
                'text' => $dbUser->getFullname(),
                'value' => $dbUser->getId()
            ];
        }

        $this->fb   ->setMethod('POST')->setAction($this->formHandlerUrl)
                    ->addLabel('User', 'user', true)
                    ->addElement($this->fb->createSelect()->setName('user')->addOptionsBasedOnArray($users))

                    ->addElement($this->fb->createSubmit('Add'))
        ;
    }
}

?>