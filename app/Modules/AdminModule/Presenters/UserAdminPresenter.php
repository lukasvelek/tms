<?php

namespace App\Modules\AdminModule;

use App\Components\Forms\UserFormFactory;
use App\Components\Grids\UserGridFactory;
use App\Core\CryptManager;
use App\UI\LinkBuilder;

class UserAdminPresenter extends AAdminPresenter {
    public function __construct() {
        parent::__construct('UserAdminPresenter', 'User administration');

        $this->createSubList();
    }

    public function renderList() {
        global $app;

        $userGridFactory = new UserGridFactory($app->getConn(), $app->logger, $app->userRepository);

        $this->template->user_grid = $userGridFactory->createComponent();
        $this->template->user_grid_control = $userGridFactory->createGridControls();
        $this->template->links = [];
        $this->template->links = LinkBuilder::createAdvLink(['page' => 'UserAdmin:form'], 'New user');
    }

    public function handleForm() {
        if(isset($_POST) && !empty($_POST) && isset($_POST['username'])) {
            $username = $this->httpPost('username');
            $password = $this->httpPost('password');
            $fullname = $this->httpPost('fullname');

            $password = CryptManager::hashPassword($password);
        }
    }

    public function renderForm() {
        global $app;

        $userFormFactory = new UserFormFactory($app->getConn(), $app->logger, $app->userRepository, '?page=AdminModule:UserAdmin:form');

        $this->template->links = [];
        $this->template->links[] = LinkBuilder::createAdvLink(['page' => 'UserAdmin:list'], '&larr; Back');
        $this->template->form = $userFormFactory->createComponent();
    }
}

?>