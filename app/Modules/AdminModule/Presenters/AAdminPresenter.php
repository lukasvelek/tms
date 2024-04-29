<?php

namespace App\Modules\AdminModule;

use App\Components\AdministrationSubList;
use App\Modules\APresenter;

abstract class AAdminPresenter extends APresenter {
    public function createSubList(string $templateVariableName = 'list') {
        $list = AdministrationSubList::createList();

        $this->addBeforeRenderCallback(function(object &$template) use ($templateVariableName, $list) {
            $template->{$templateVariableName} = $list;
        });
    }
}

?>