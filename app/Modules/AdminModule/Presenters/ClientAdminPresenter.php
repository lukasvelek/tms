<?php

namespace App\Modules\AdminModule;

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
        $this->template->client_grid = '';
        $this->template->client_grid_control = '';
        $this->template->links = [];
        $this->template->links = LinkBuilder::createAdvLink(['page' => 'ClientAdmin:form'], 'New client');
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
}

?>