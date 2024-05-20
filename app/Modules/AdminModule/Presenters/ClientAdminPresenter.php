<?php

namespace App\Modules\AdminModule;

use App\Components\Forms\ClientFormFactory;
use App\UI\LinkBuilder;

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
}

?>