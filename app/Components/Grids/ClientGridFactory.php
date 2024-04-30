<?php

namespace App\Components\Grids;

use App\Components\AComponent;
use App\Components\IFactory;
use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Repositories\ClientRepository;
use App\UI\GridBuilder;

class ClientGridFactory extends AComponent implements IFactory {
    private ClientRepository $clientRepository;
    private GridBuilder $gb;

    public function __construct(Database $db, Logger $logger, ClientRepository $clientRepository) {
        parent::__construct($db, $logger);

        $this->clientRepository = $clientRepository;

        $this->gb = new GridBuilder();

        //$this->gb->addColumns
    }

    public function createGridControls() {
        return $this->gb->createGridControls('clientGridPaginator', (int)($this->get('gridPage') ?? 0), (int)(ceil($this->clientRepository->getClientCount() / GRID_SIZE)));
    }

    public function createComponent() {
        return $this->gb->build();
    }
}

?>