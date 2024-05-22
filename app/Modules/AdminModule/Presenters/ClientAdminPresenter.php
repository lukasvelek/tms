<?php

namespace App\Modules\AdminModule;

use App\Components\Forms\ClientAddUserFormFactory;
use App\Components\Forms\ClientFormFactory;
use App\Constants\FlashMessageTypes;
use App\UI\LinkBuilder;
use Exception;

class ClientAdminPresenter extends AAdminPresenter {
    public function __construct() {
        parent::__construct('ClientAdminPresenter', 'Client administration');

        $this->createSubList();
    }

    public function renderList(?int $gridPage) {
        if($gridPage === NULL) {
            $gridPage = 0;
        }
        $this->template->scripts = ['<script type="text/javascript">clientGridPaginator(' . $gridPage . ');</script>'];
        $this->template->links = [];
        $this->template->links[] = LinkBuilder::createAdvLink(['page' => 'ClientAdmin:form'], 'New client');
    }

    public function renderForm() {
        global $app;

        $idClient = $this->httpGet('idClient');

        $clientFormFactory = new ClientFormFactory($app->getConn(), $app->logger, $app->clientRepository, $app->userRepository, '?page=AdminModule:ClientAdmin:form', $idClient);

        $this->template->links = [];
        $this->template->links[] = LinkBuilder::createAdvLink(['page' => 'ClientAdmin:list'], '&larr; Back');
        $this->template->form = $clientFormFactory->createComponent();
    }

    public function handleForm() {
        global $app;

        if(isset($_POST) && !empty($_POST) && isset($_POST['client_name'], $_POST['manager'])) {
            $clientName = $this->httpPost('client_name');
            $idManager = $this->httpPost('manager');

            $result = $app->clientRepository->createClient($clientName, $idManager);

            if($result === NULL) {
                $app->flashMessage('Client \'' . $clientName . '\' created.', FlashMessageTypes::INFO);
                $app->redirect('list');
            } else {
                throw new Exception($result);
            }
        }
    }

    public function renderDelete() {
        global $app;

        $idClient = $this->httpGet('idClient');

        if($idClient === NULL) {
            $app->flashMessage('No client selected.', FlashMessageTypes::ERROR);
            $app->redirect('list');
        }

        $client = $app->clientRepository->getClientById($idClient);

        $this->template->name = $client->getName();
        $this->template->links = [];
        $this->template->links[] = LinkBuilder::createAdvLink(['page' => 'ClientAdmin:list'], '&larr; Back');

        $button = '<button type="button" onclick="location.href = \'\?page=AdminModule:ClientAdmin:delete&isDelete=1&idClient=' . $idClient . '\';">Delete</button>';

        $this->template->form = $button;
    }

    public function handleDelete() {
        global $app;
        
        $isDelete = $this->httpGet('isDelete');

        if($isDelete === NULL) {
            return;
        }

        if($isDelete == '1') {
            $idClient = $this->httpGet('idClient');

            if($idClient === NULL) {
                $app->flashMessage('No client selected.', FlashMessageTypes::ERROR);
                $app->redirect('list');
            }

            $client = $app->clientRepository->getClientById($idClient);

            $result = $app->clientRepository->deleteClient($idClient);

            if($result === NULL) {
                $app->flashMessage('Client \'' . $client->getName() . '\' deleted.', FlashMessageTypes::SUCCESS);
                $app->redirect('list');
            } else {
                throw new Exception($result);
            }
        } else {
            $app->flashMessage('No user could not be deleted because no confirmation was given.');
            $app->redirect('list');
        }
    }

    public function renderManageUsersList(?int $gridPage) {
        global $app;

        $idClient = $this->httpGet('idClient');
        $client = $app->clientRepository->getClientById($idClient);

        if($gridPage === NULL) {
            $gridPage = 0;
        }

        $this->template->scripts = ['<script type="text/javascript">clientUsersGridPaginator(' . $gridPage . ', ' . $idClient . ');</script>'];
        $this->template->client_name = $client->getName();
        $this->template->links = [];
        $this->template->links[] = LinkBuilder::createAdvLink(['page' => 'ClientAdmin:addUserForm', 'idClient' => $idClient], 'Add user');
    }

    public function renderAddUserForm() {
        global $app;

        $idClient = $this->httpGet('idClient');

        $client = $app->clientRepository->getClientById($idClient);

        $cauff = new ClientAddUserFormFactory($app->getConn(), $app->logger, $app->userRepository, $app->clientRepository, '?page=AdminModule:ClientAdmin:addUserForm&idClient=' . $idClient, $idClient);

        $this->template->form = $cauff->createComponent();
        $this->template->client_name = $client->getName();
        $this->template->links = [];
    }

    public function handleAddUserForm() {
        global $app;

        $idClient = $this->httpGet('idClient');
        $idUser = $this->httpPost('user');

        if($idClient === NULL || $idUser === NULL) {
            return;
        }
        
        $user = $app->userRepository->getUserById($idUser);
        $client = $app->clientRepository->getClientById($idClient);

        $app->clientRepository->addUserToClient($idClient, $idUser);

        $app->flashMessage('User \'' . $user->getFullname() . '\' added to client \'' . $client->getName() . '\'.', FlashMessageTypes::SUCCESS);
        $app->redirect('manageUsersList', ['idClient' => $idClient]);
    }

    public function renderRemoveUserForm() {
        global $app;

        $idClient = $this->httpGet('idClient');
        $idUser = $this->httpGet('idUser');

        if($idClient === NULL || $idUser === NULL) {
            $app->flashMessage('Not enough parameters passed.', FlashMessageTypes::ERROR);
            $app->redirect('manageUsersList', ['idClient' => $idClient]);
        }

        $user = $app->userRepository->getUserById($idUser);
        $client = $app->clientRepository->getClientById($idClient);

        $this->template->client_name = $client->getName();
        $this->template->user_name = $user->getFullname();
        $this->template->links = [];
        $this->template->links[] = LinkBuilder::createAdvLink(['page' => 'manageUsersList', 'idClient' => $idClient], '&larr; Back');

        $button = '<button type="button" onclick="location.href = \'?page=AdminModule:ClientAdmin:removeUserForm&isDelete=1&idClient=' . $idClient .'&idUser=' . $idUser . '\'">Remove</button>';

        $this->template->form = $button;
    }

    public function handleRemoveUserForm() {
        global $app;

        $idClient = $this->httpGet('idClient');
        $idUser = $this->httpGet('idUser');
        $isDelete = $this->httpGet('isDelete');

        if($isDelete !== NULL && $isDelete == '1' && ($idUser !== NULL && $idClient !== NULL)) {
            $app->clientRepository->removeUserFromClient($idClient, $idUser);

            $client = $app->clientRepository->getClientById($idClient);
            $user = $app->userRepository->getUserById($idUser);

            $app->flashMessage('User \'' . $user->getFullname() . '\' removed from client \'' . $client->getName() . '\'.', FlashMessageTypes::SUCCESS);
            $app->redirect('manageUsersList', ['idClient' => $idClient]);
        }
    }
}

?>