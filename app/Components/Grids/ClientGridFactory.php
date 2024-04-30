<?php

namespace App\Components\Grids;

use App\Components\AComponent;
use App\Components\IFactory;
use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Entities\ClientEntity;
use App\Repositories\ClientRepository;
use App\Repositories\UserRepository;
use App\UI\GridBuilder;

class ClientGridFactory extends AComponent implements IFactory {
    private ClientRepository $clientRepository;
    private UserRepository $userRepository;
    private GridBuilder $gb;

    public function __construct(Database $db, Logger $logger, ClientRepository $clientRepository, UserRepository $userRepository) {
        parent::__construct($db, $logger);

        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;

        $this->gb = new GridBuilder();

        $this->gb->addColumns(['name' => 'Name', 'manager' => 'Manager']);
        $this->gb->addOnColumnRender('manager', function(ClientEntity $client) {
            $manager = $this->userRepository->getUserById($client->getIdManager());

            if($manager === NULL) {
                return '-';
            } else {
                return $manager->getFullname();
            }
        });
    }

    public function createGridControls() {
        return $this->gb->createGridControls('clientGridPaginator', (int)($this->get('gridPage') ?? 0), (int)(ceil($this->clientRepository->getClientCount() / GRID_SIZE)));
    }

    public function createComponent() {
        return $this->gb->build();
    }
}

?>