<?php

namespace App\Components;

use App\UI\LinkBuilder;

class AdministrationSubList {
    protected array $links;

    private function __construct() {
        $this->links = [
            LinkBuilder::createAdvLink(['page' => 'Admin:dashboard'], 'Dashboard'),
            LinkBuilder::createAdvLink(['page' => 'UserAdmin:list'], 'Users'),
            LinkBuilder::createAdvLink(['page' => 'ProjectAdmin:list'], 'Projects'),
            LinkBuilder::createAdvLink(['page' => 'ClientAdmin:list'], 'Clients')
        ];
    }

    public static function createList() {
        $self = new self();

        $list = '<ul>';

        foreach($self->links as $link) {
            $list .= '<li>' . $link . '</li>';
        }

        $list .= '</ul>';

        return $list;
    }
}

?>