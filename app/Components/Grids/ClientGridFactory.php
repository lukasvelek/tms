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
use App\UI\LinkBuilder;

class ClientGridFactory extends AComponent implements IFactory {
    private ClientRepository $clientRepository;
    private UserRepository $userRepository;
    private GridBuilder $gb;

    public function __construct(Database $db, Logger $logger, ClientRepository $clientRepository, UserRepository $userRepository) {
        parent::__construct($db, $logger);

        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;

        $this->gb = new GridBuilder();

        $this->gb->addDataSource($this->getClientsForGrid());
        $this->gb->addColumns(['name' => 'Name', 'manager' => 'Manager']);
        $this->gb->addOnColumnRender('manager', function(ClientEntity $client) {
            $manager = $this->userRepository->getUserById($client->getIdManager());

            if($manager === NULL) {
                return '-';
            } else {
                return $manager->getFullname();
            }
        });
        $this->gb->addAction(function(ClientEntity $client) {
            return LinkBuilder::createAdvLink(['page' => 'AdminModule:ClientAdmin:profile', 'idClient' => $client->getId()], 'Profile');
        });
        $this->gb->addAction(function(ClientEntity $client) {
            return LinkBuilder::createAdvLink(['page' => 'AdminModule:ClientAdmin:form', 'idClient' => $client->getId()], 'Edit');
        });
        $this->gb->addAction(function(ClientEntity $client) {
            return LinkBuilder::createAdvLink(['page' => 'AdminModule:ClientAdmin:delete', 'idClient' => $client->getId()], 'Delete');
        });
    }

    public function createGridControls() {
        return $this->gb->createGridControls('clientGridPaginator', (int)($this->get('gridPage') ?? 0), (int)(ceil($this->clientRepository->getClientCount() / GRID_SIZE)));
    }

    public function createComponent() {
        return $this->gb->build();
    }

    private function getClientsForGrid() {
        $page = $this->Get('grid_page');

        if($page !== NULL && $page > 0) {
            $page -= 1;
        } else {
            $page = 0;
        }

        $qb = $this->clientRepository->composeQueryForGrid(__METHOD__);

        $offset = GRID_SIZE * $page;

        if($offset > 0) {
            $qb->offset($offset);
        }

        $qb->execute();

        $clients = [];
        while($row = $qb->fetchAssoc()) {
            $clients[] = ClientEntity::createClientEntityFromDbRow($row);
        }

        return $clients;
    }
}

?>