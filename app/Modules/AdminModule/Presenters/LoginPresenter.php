<?php

namespace App\Modules\AdminModule;

use App\Modules\APresenter;

class LoginPresenter extends APresenter {
    public const DRAW_TOPPANEL = false;

    public function __construct() {
        parent::__construct('LoginPresenter', 'Login');
    }

    protected function renderForm() {
        $this->template->page_title = 'Login form';
    }
}

?>