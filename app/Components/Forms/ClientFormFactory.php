<?php

namespace App\Components\Forms;

use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Repositories\ClientRepository;
use App\Repositories\UserRepository;

class ClientFormFactory extends AFormFactory {
    private ClientRepository $clientRepository;
    private UserRepository $userRepository;
    private ?int $idClient;

    public function __construct(Database $db, Logger $logger, ClientRepository $clientRepository, UserRepository $userRepository, string $formHandlerUrl = '', ?int $idClient = null) {
        parent::__construct($db, $logger, $formHandlerUrl);

        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
        $this->idClient = $idClient;

        $this->createForm();
    }
    
    private function createForm() {
        $clientName = '';
        $idManager = '';

        if($this->idClient !== NULL) {
            $client = $this->clientRepository->getClientById($this->idClient);

            $clientName = $client->getName();
            $idManager = $client->getIdManager();
        }

        $users = $this->userRepository->getAllUsers();
        
        $usersArr = [];
        foreach($users as $user) {
            $tmp = [
                'text' => $user->getFullname(),
                'value' => $user->getId()
            ];

            if($idManager == $user->getId()) {
                $tmp['selected'] = 'selected';
            }

            $usersArr[] = $tmp;
        }

        $this->fb   ->setMethod('POST')->setAction($this->formHandlerUrl)
                    ->addLabel('Client name', 'client_name', true)
                    ->addText('client_name', $clientName, '', true)

                    ->addLabel('Manager', 'manager', true)
                    ->addElement($this->fb->createSelect()->setName('manager')->addOptionsBasedOnArray($usersArr))

                    ->addElement($this->fb->createSubmit('Create'))
        ;
    }
}

?>