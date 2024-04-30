<?php

namespace App\Components\Forms;

use App\Components\AComponent;
use App\Components\IFactory;
use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Core\ScriptLoader;
use App\UI\FormBuilder\FormBuilder;

abstract class AFormFactory extends AComponent implements IFactory {
    protected FormBuilder $fb;
    protected string $formHandlerUrl;

    private ?string $reducerUrl;

    protected function __construct(Database $db, Logger $logger, string $formHandlerUrl) {
        parent::__construct($db, $logger);

        $this->formHandlerUrl = $formHandlerUrl;
        $this->reducerUrl = null;

        $this->fb = new FormBuilder();
    }

    public function setReducer(string $jsReducerSrc) {
        $this->reducerUrl = $jsReducerSrc;
    }

    protected function applyReducer() {
        if($this->reducerUrl !== NULL) {
            $html = ScriptLoader::loadJSScript($this->reducerUrl);

            $this->fb->addJSScript($html);
        }
    }
}

?>