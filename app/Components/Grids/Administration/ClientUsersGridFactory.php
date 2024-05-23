<?php

namespace App\Components\Grids;

use App\Components\AComponent;
use App\Components\IFactory;
use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Entities\UserEntity;
use App\Repositories\ClientRepository;
use App\Repositories\UserRepository;
use App\UI\GridBuilder;
use App\UI\LinkBuilder;

class ClientUsersGridFactory extends AComponent implements IFactory {
    private ClientRepository $clientRepository;
    private UserRepository $userRepository;
    private GridBuilder $gb;

    public function __construct(Database $db, Logger $logger, ClientRepository $clientRepository, UserRepository $userRepository) {
        parent::__construct($db, $logger);
        
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;

        $this->gb = new GridBuilder();

        $this->gb->addColumns(['fullname' => 'Fullname']);
        $this->gb->addDataSource($this->getData());
        $this->gb->addAction(function(UserEntity $user) {
            return LinkBuilder::createAdvLink(['page' => 'AdminModule:ClientAdmin:removeUserForm', 'idClient' => $this->get('idClient'), 'idUser' => $user->getId()], 'Remove');
        });
    }

    public function createGridControls() {
        return $this->gb->createGridControls('clientUsersGridPaginator', (int)($this->get('gridPage') ?? 0), (int)(ceil($this->clientRepository->getUserCountForClient($this->get('idClient')) / GRID_SIZE)));
    }

    public function createComponent() {
        return $this->gb->build();
    }

    private function getData() {
        $idUsers = $this->clientRepository->getUsersForClient($this->get('idClient'));

        if(empty($idUsers)) {
            return [];
        }

        return $this->userRepository->getUsersByIds($idUsers);
    }
}

?>