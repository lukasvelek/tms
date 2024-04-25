<?php

namespace App\Modules\AdminModule;

use App\Constants\FlashMessageTypes;
use App\Modules\APresenter;
use App\UI\FormBuilder\FormBuilder;

class LoginPresenter extends APresenter {
    public const DRAW_TOPPANEL = false;

    public function __construct() {
        parent::__construct('LoginPresenter', 'Login');
    }

    protected function renderForm() {
        $this->template->page_title = 'Login form';
        $this->template->login_form = $this->createForm();
    }

    protected function handleProcessForm(string $username, string $password) {
        global $app;

        $result = $app->userAuthenticator->authenticateUser($username, $password);

        if($result !== NULL) {
            $user = $app->userRepository->getUserById($result);

            $app->setCurrentUser($user);

            $app->flashMessage('Logged in as \'' . $username . '\'.', FlashMessageTypes::SUCCESS);
            $app->redirect('Home:dashboard');
        } else{
            $app->flashMessage('Bad credentials entered.', FlashMessageTypes::ERROR);
            $app->redirect('form');
        }
    }

    private function createForm() {
        $fb = FormBuilder::getTemporaryObject();

        $fb ->setMethod('POST')->setAction('?page=AdminModule:Login:processForm')
            ->addLabel('Username', 'username', true)
            ->addText('username', '', '', true)
            ->addLabel('Password', 'password', true)
            ->addPassword('password', '', '', true)
            ->addElement($fb->createSubmit('Log in'));

        return $fb->build();
    }
}

?>