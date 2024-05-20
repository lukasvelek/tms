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

    public function __construct(Database $db, Logger $logger, UserRepository $userRepository, string $filter = 'default', array $data = []) {
        parent::__construct($db, $logger);

        $this->userRepository = $userRepository;

        $this->gb = new GridBuilder();

        $this->gb->addColumns(['username' => 'Username', 'fullname' => 'Fullname', 'email' => 'Email']);
        $this->gb->addDataSource($this->getUsersForGrid($filter, $data));
        $this->gb->addAction(function(UserEntity $user) {
            return LinkBuilder::createAdvLink(['page' => 'AdminModule:Users:profile', 'idUser' => $user->getId()], 'Profile');
        });
        $this->gb->addAction(function(UserEntity $user) {
            return LinkBuilder::createAdvLink(['page' => 'AdminModule:UserAdmin:form', 'idUser' => $user->getId()], 'Edit');
        });
        $this->gb->addAction(function(UserEntity $user) {
            return LinkBuilder::createAdvLink(['page' => 'AdminModule:UserAdmin:delete', 'idUser' => $user->getId()], 'Delete');
        });
    }

    public function createGridControls() {
        return $this->gb->createGridControls('userGridPaginator', (int)($this->get('gridPage') ?? 0), (int)(ceil($this->userRepository->getUserCount() / GRID_SIZE)));
    }

    public function createComponent() {
        return $this->gb->build();
    }

    private function getUsersForGrid(string $filter, array $data) {
        $page = $this->get('grid_page');

        if($page !== NULL && $page > 0) {
            $page -= 1;
        } else {
            $page = 0;
        }

        $qb = $this->userRepository->composeQueryForGrid(__METHOD__);

        $offset = GRID_SIZE * $page;

        if($offset > 0) {
            $qb ->offset($offset);
        }
        
        $qb->execute();

        $users = [];
        while($row = $qb->fetchAssoc()) {
            switch($filter) {
                case 'usersForClient':
                    if(in_array($row['id'], $data['users'])) {
                        $users[] = UserEntity::createUserEntityFromDbRow($row);
                    }

                    break;

                case 'default':
                    $users[] = UserEntity::createUserEntityFromDbRow($row);
                    
                    break;
            }
        }

        return $users;
    }
}

?>