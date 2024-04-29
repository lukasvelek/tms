<?php

namespace App\Modules\AdminModule;

class AdminPresenter extends AAdminPresenter {
    public function __construct() {
        parent::__construct('AdminPresenter', 'Administration');
        
        $this->createSubList();
    }

    public function renderDashboard() {
    }
}

?>