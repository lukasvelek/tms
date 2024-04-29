<?php

namespace App\Components\Forms;

use App\Components\AComponent;
use App\Components\IFactory;
use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Core\ScriptLoader;
use App\Repositories\UserRepository;
use App\UI\FormBuilder\FormBuilder;

class UserFormFactory extends AComponent implements IFactory {
    private UserRepository $userRepository;
    private ?int $idUser;
    private string $formHandlerUrl;
    private ?string $reducerUrl;
    private FormBuilder $fb;

    public function __construct(Database $db, Logger $logger, UserRepository $userRepository, string $formHandlerUrl = '', ?int $idUser = null) {
        parent::__construct($db, $logger);

        $this->userRepository = $userRepository;
        $this->idUser = $idUser;
        $this->formHandlerUrl = $formHandlerUrl;
        $this->reducerUrl = null;
        $this->fb = new FormBuilder();

        $this->createForm();
    }

    public function setReducer(string $jsReducerSrc) {
        $this->reducerUrl = $jsReducerSrc;
    }

    public function createComponent() {
        if($this->reducerUrl !== NULL) {
            $html = ScriptLoader::loadJSScript($this->reducerUrl);

            $this->fb->addJSScript($html);
        }

        return $this->fb->build();
    }

    private function createForm() {
        $this->fb   ->setMethod('POST')->setAction($this->formHandlerUrl)
                    ->addLabel('Username', 'username', true)
                    ->addText('username', '', '', true)

                    ->addLabel('Fullname', 'fullname', true)
                    ->addText('fullname', '', '', true)

                    ->addLabel('Password', 'password', true)
                    ->addPassword('password', '', '', true)

                    ->addElement($this->fb->createSubmit('Create'));
    }
}

?>