<?php

namespace App\Components\Grids;

use App\Components\AComponent;
use App\Components\IFactory;
use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Entities\ProjectEntity;
use App\Repositories\ClientRepository;
use App\Repositories\ProjectRepository;
use App\UI\GridBuilder;

class ProjectGridFactory extends AComponent implements IFactory {
    private ClientRepository $clientRepository;
    private ProjectRepository $projectRepository;
    private GridBuilder $gb;

    public function __construct(Database $db, Logger $logger, ClientRepository $clientRepository, ProjectRepository $projectRepository) {
        parent::__construct($db, $logger);

        $this->clientRepository = $clientRepository;
        $this->projectRepository = $projectRepository;

        $this->gb = new GridBuilder();

        $this->gb->addDataSource($this->getProjectsForGrid());
        $this->gb->addColumns(['name' => 'Name']);
    }

    public function createGridControls() {
        return $this->gb->createGridControls('projectGridPaginator', (int)($this->get('gridPage') ?? 0), (int)(ceil($this->projectRepository->getProjectCount() / GRID_SIZE)));
    }

    public function createComponent() {
        return $this->gb->build();
    }

    private function getProjectsForGrid() {
        $page = $this->get('grid_page');

        if($page !== NULL && $page > 0) {
            $page -= 1;
        } else {
            $page = 0;
        }

        $qb = $this->projectRepository->composeQueryForGrid(__METHOD__);

        $offset = GRID_SIZE * $page;

        if($offset > 0) {
            $qb->offset($offset);
        }

        $qb->execute();

        $projects = [];
        while($row = $qb->fetchAssoc()) {
            $projects[] = ProjectEntity::createProjectEntityFromDbRow($row);
        }

        return $projects;
    }
}

?>