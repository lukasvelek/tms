<?php

namespace App\Modules\AdminModule;

use App\Components\Grids\UserGridFactory;
use App\Modules\APresenter;
use App\UI\LinkBuilder;

class ClientsPresenter extends APresenter {
    public function __construct() {
        parent::__construct('ClientsPresenter', 'Clients');
    }

    public function renderProfile() {
        global $app;

        $idClient = $this->httpGet('idClient');

        $client = $app->clientRepository->getClientById($idClient);
        $manager = $app->userRepository->getUserById($client->getIdManager());

        $clientUsers = $app->clientRepository->getUsersForClient($idClient);

        $userGridFactory = new UserGridFactory($app->getConn(), $app->logger, $app->userRepository, 'usersForClient', ['users' => $clientUsers]);

        $this->template->name = $client->getName();
        $this->template->links = [];
        $this->template->grid_links = [];
        $this->template->grid_links[] = LinkBuilder::createAdvLink(['page' => 'Clients:addUserForm', 'idClient' => $idClient], 'Add user');
        $this->template->profile = '
            <p><b>Manager: </b>' . $manager->getFullname() . '</p>
        ';
        $this->template->user_grid = $userGridFactory->createComponent();
    }
}

?>