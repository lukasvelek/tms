<?php

namespace App\Components\Grids;

use App\Components\AComponent;
use App\Components\IFactory;
use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Entities\UserEntity;
use App\Repositories\UserRepository;
use App\UI\GridBuilder;
use App\UI\LinkBuilder;

class UserGridFactory extends AComponent implements IFactory {
    private UserRepository $userRepository;

    private GridBuilder $gb;

    public function __construct(Database $db, Logger $logger, UserRepository $userRepository) {
        parent::__construct($db, $logger);

        $this->userRepository = $userRepository;

        $this->gb = new GridBuilder();

        $this->gb->addColumns(['username' => 'Username', 'fullname' => 'Fullname', 'email' => 'Email']);
        $this->gb->addDataSource($this->getUsersForGrid());
        $this->gb->addAction(function(UserEntity $user) {
            return LinkBuilder::createAdvLink(['page' => 'Users:profile', 'idUser' => $user->getId()], 'Profile');
        });
        $this->gb->addAction(function(UserEntity $user) {
            return LinkBuilder::createAdvLink(['page' => 'form', 'idUser' => $user->getId()], 'Edit');
        });
        $this->gb->addAction(function(UserEntity $user) {
            return LinkBuilder::createAdvLink(['page' => 'delete', 'idUser' => $user->getId()], 'Delete');
        });
    }

    public function createGridControls() {
        return $this->gb->createGridControls('userGridPaginator', (int)($this->get('grid_page') ?? 0), (int)(ceil($this->userRepository->getUserCount() / GRID_SIZE)));
    }

    public function createComponent() {
        return $this->gb->build();
    }

    private function getUsersForGrid() {
        $page = $this->get('grid_page');

        if($page !== NULL && $page > 0) {
            $page -= 1;
        } else {
            $page = 0;
        }

        $qb = $this->userRepository->composeQueryForGrid(__METHOD__);

        $offset = GRID_SIZE * $page;

        if($offset > 0) {
            $qb ->offset($page * GRID_SIZE);
        }
        
        $qb->execute();

        $users = [];
        while($row = $qb->fetchAssoc()) {
            $users[] = UserEntity::createUserEntityFromDbRow($row);
        }

        return $users;
    }
}

?>