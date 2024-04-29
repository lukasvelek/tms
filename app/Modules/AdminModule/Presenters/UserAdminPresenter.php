<?php

namespace App\Modules\AdminModule;

use App\Components\Grids\UserGridFactory;

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
    }
}

?>