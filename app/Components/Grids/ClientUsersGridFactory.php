<?php

namespace App\Components\Grids;

use App\Components\AComponent;
use App\Components\IFactory;
use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Repositories\ClientRepository;
use App\Repositories\UserRepository;
use App\UI\GridBuilder;

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
    }

    public function createGridControls() {
        return $this->gb->createGridControls('clientUsersGridPaginator', (int)($this->get('gridPage') ?? 0), (int)(ceil($this->clientRepository->getUserCountForClient($this->get('idClient')) / GRID_SIZE)));
    }

    public function createComponent() {
        return $this->gb->build();
    }
}

?>