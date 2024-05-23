<?php

namespace App\Modules\AdminModule;

use App\Components\Grids\UserGridFactory;
use App\Modules\APresenter;

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
        $this->template->profile = '
            <p><b>Manager: </b>' . $manager->getFullname() . '</p>
        ';
        $this->template->user_grid = $userGridFactory->createComponent();
        $this->template->user_grid_control = $userGridFactory->createGridControls();
    }
}

?>