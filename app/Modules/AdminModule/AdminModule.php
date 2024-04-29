<?php

namespace App\Modules\AdminModule;

use App\Modules\AModule;
use App\UI\LinkBuilder;

class AdminModule extends AModule {
    public function __construct() {
        parent::__construct('AdminModule', 'Admin module');

        $this->navbar();
    }

    private function navbar() {
        global $app;

        if($app->user !== NULL) {
            $logo = LinkBuilder::createAdvLink(['page' => 'AdminModule:Home:dashboard'], 'TMS', 'navbar-link');
            $this->fillNavbar('$LOGO$', $logo);

            $links = [
                LinkBuilder::createAdvLink(['page' => 'AdminModule:Home:dashboard'], 'Dashboard', 'navbar-link'),
                LinkBuilder::createAdvLink(['page' => 'AdminModule:Tickets:queues'], 'Ticket queues', 'navbar-link'),
                LinkBuilder::createAdvLink(['page' => 'AdminModule:Administration:dashboard'], 'Administration', 'navbar-link')
            ];
            $this->fillNavbar('$LINKS$', $links, true);

            $user = [];
            $user[] = '<span class="navbar-text">' . $app->user->getUsername() . '</span>';
            $user[] = LinkBuilder::createAdvLink(['page' => 'AdminModule:Logout:logout'], 'Logout', 'navbar-link');
            $this->fillNavbar('$USER$', $user, true);
        } else {
            $logo = '';
            $this->fillNavbar('$LOGO$', $logo);

            $links = '';
            $this->fillNavbar('$LINKS$', $links);

            $user = LinkBuilder::createAdvLink(['page' => 'AdminModule:Login:form'], 'Log in', 'navbar-link');
            $this->fillNavbar('$USER$', $user);
        }
    }
}

?>