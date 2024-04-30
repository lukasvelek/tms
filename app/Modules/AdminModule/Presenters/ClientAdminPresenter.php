<?php

namespace App\Modules\AdminModule;

use App\UI\LinkBuilder;

class ClientAdminPresenter extends AAdminPresenter {
    public function __construct() {
        parent::__construct('ClientAdminPresenter', 'Client administration');

        $this->createSubList();
    }

    public function renderList() {
        $this->template->client_grid = '';
        $this->template->client_grid_control = '';
        $this->template->links = [];
        $this->template->links = LinkBuilder::createAdvLink(['page' => 'ClientAdmin:form'], 'New client');
    }
}

?>