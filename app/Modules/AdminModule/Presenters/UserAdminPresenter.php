<?php

namespace App\Modules\AdminModule;

use App\Components\Forms\UserFormFactory;
use App\Components\Grids\UserGridFactory;
use App\Constants\FlashMessageTypes;
use App\Core\CryptManager;
use App\UI\LinkBuilder;
use Exception;

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
        global $app;

        if(isset($_POST) && !empty($_POST) && isset($_POST['username'])) {
            $username = $this->httpPost('username');
            $password = $this->httpPost('password');
            $fullname = $this->httpPost('fullname');

            $password = CryptManager::hashPassword($password);

            $result = $app->userRepository->createUser($username, $fullname, $password);

            if($result === NULL) {
                $app->flashMessage('User \'' . $username . '\' created.', FlashMessageTypes::SUCCESS);
                $app->redirect('list');
            } else {
                throw new Exception($result);
            }
        }
    }

    public function renderForm() {
        global $app;

        $idUser = $this->httpGet('idUser');

        $userFormFactory = new UserFormFactory($app->getConn(), $app->logger, $app->userRepository, '?page=AdminModule:UserAdmin:form', $idUser);

        $this->template->links = [];
        $this->template->links[] = LinkBuilder::createAdvLink(['page' => 'UserAdmin:list'], '&larr; Back');
        $this->template->form = $userFormFactory->createComponent();
    }
}

?>