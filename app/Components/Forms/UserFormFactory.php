<?php

namespace App\Components\Forms;

use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Repositories\UserRepository;

class UserFormFactory extends AFormFactory {
    private UserRepository $userRepository;
    private ?int $idUser;

    public function __construct(Database $db, Logger $logger, UserRepository $userRepository, string $formHandlerUrl = '', ?int $idUser = null) {
        parent::__construct($db, $logger, $formHandlerUrl);

        $this->userRepository = $userRepository;
        $this->idUser = $idUser;

        $this->createForm();
    }

    private function createForm() {
        $this->setReducer('/tms/js/forms/UserForm.js');

        $username = '';
        $fullname = '';

        if($this->idUser !== NULL) {
            $user = $this->userRepository->getUserById($this->idUser);

            $username = $user->getUsername();
            $fullname = $user->getFullname();
        }

        $this->fb   ->setMethod('POST')->setAction($this->formHandlerUrl)
                    ->addLabel('Username', 'username', true)
                    ->addText('username', $username, '', true)

                    ->addLabel('Fullname', 'fullname', true)
                    ->addText('fullname', $fullname, '', true)

                    ->addLabel('Password', 'password', true)
                    ->addPassword('password', '', '', true)

                    ->addLabel('Password again', 'password_again', true)
                    ->addPassword('password_again', '', '', true)

                    ->addElement($this->fb->createSubmit(($this->idUser === NULL) ? 'Create' : 'Save'));
    }
}

?>