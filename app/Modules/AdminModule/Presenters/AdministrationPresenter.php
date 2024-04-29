<?php

namespace App\Modules\AdminModule;

use App\Modules\APresenter;
use App\UI\LinkBuilder;

class AdministrationPresenter extends APresenter {
    public function __construct() {
        parent::__construct('AdministrationPresenter', 'Administration');
    }

    public function renderDashboard() {
        $links = [];
        $links[] = LinkBuilder::createAdvLink(['page' => 'Administration:dashboard'], 'Dashboard');
        $links[] = LinkBuilder::createAdvLink(['page' => 'UserAdmin:list'], 'Users');
        $links[] = LinkBuilder::createAdvLink(['page' => 'ProjectAdmin:list'], 'Projects');
        $links[] = LinkBuilder::createAdvLink(['page' => 'ClientAdmin:list'], 'Clients');

        $tmp = '<ul>';

        foreach($links as $link) {
            $tmp .= '<li>' . $link . '</li>';
        }

        $tmp .= '</ul>';
        
        $links = $tmp;

        $data = [
            '$LINKS$' => $links
        ];

        $this->fill($data);
    }
}

?>