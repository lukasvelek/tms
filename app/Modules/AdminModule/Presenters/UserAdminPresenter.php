<?php

namespace App\Modules\AdminModule;

use App\Components\Forms\UserFormFactory;
use App\Constants\FlashMessageTypes;
use App\Core\CryptManager;
use App\UI\FormBuilder\FormBuilder;
use App\UI\LinkBuilder;
use Exception;

class UserAdminPresenter extends AAdminPresenter {
    public function __construct() {
        parent::__construct('UserAdminPresenter', 'User administration');

        $this->createSubList();
    }

    public function renderList() {
        $this->template->scripts = ['<script type="text/javascript">userGridPaginator(0);</script>'];
        $this->template->user_grid = '';
        $this->template->user_grid_control = '';
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

    public function handleDelete() {
        global $app;

        $delete = $this->httpGet('isDelete');

        if($delete == '1') {
            $idUser = $this->httpGet('idUser');

            $user = $app->userRepository->getUserById($idUser);

            if($idUser === NULL) {
                $app->flashMessage('No user selected.', FlashMessageTypes::ERROR);
                $app->redirect('list');
            }

            $result = $app->userRepository->deleteUser($idUser);

            if($result === NULL) {
                $app->flashMessage('User \'' . $user->getUsername() . '\' deleted.', FlashMessageTypes::SUCCESS);
                $app->redirect('list');
            } else {
                throw new Exception($result);
            }
        }
    }

    public function renderDelete() {
        global $app;

        $idUser = $this->httpGet('idUser');

        if($idUser === NULL) {
            $app->flashMessage('No user selected.', FlashMessageTypes::ERROR);
            $app->redirect('list');
        }

        $user = $app->userRepository->getUserById($idUser);

        $this->template->username = $user->getUsername();
        $this->template->links = [];
        $this->template->links[] = LinkBuilder::createAdvLink(['page' => 'UserAdmin:list'], '&larr; Back');

        $button = '<button type="button" onclick="location.href = \'?page=AdminModule:UserAdmin:delete&isDelete=1&idUser=' . $idUser . '\';">Delete</button>';

        $this->template->form = $button;
    }
}

?>