<?php

namespace App\Components\Forms;

use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Core\ScriptLoader;
use App\Repositories\UserRepository;
use App\UI\FormBuilder\FormBuilder;

class UserFormFactory extends AFormFactory {
    private UserRepository $userRepository;
    private ?int $idUser;

    public function __construct(Database $db, Logger $logger, UserRepository $userRepository, string $formHandlerUrl = '', ?int $idUser = null) {
        parent::__construct($db, $logger, $formHandlerUrl);

        $this->userRepository = $userRepository;
        $this->idUser = $idUser;

        $this->createForm();
    }

    public function createComponent() {
        $this->applyReducer();

        return $this->fb->build();
    }

    private function createForm() {
        $this->setReducer('/tms/js/forms/UserForm.js');

        $this->fb   ->setMethod('POST')->setAction($this->formHandlerUrl)
                    ->addLabel('Username', 'username', true)
                    ->addText('username', '', '', true)

                    ->addLabel('Fullname', 'fullname', true)
                    ->addText('fullname', '', '', true)

                    ->addLabel('Password', 'password', true)
                    ->addPassword('password', '', '', true)

                    ->addLabel('Password again', 'password_again', true)
                    ->addPassword('password_again', '', '', true)

                    ->addElement($this->fb->createSubmit('Create'));
    }
}

?>