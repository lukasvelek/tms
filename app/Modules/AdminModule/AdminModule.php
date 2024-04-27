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
            $logo = LinkBuilder::createAdvLink(['page' => 'AdminModule:Home:dashboard'], 'TMS', 'toppanel-link');
            $this->fillNavbar('$LOGO$', $logo);

            $links = [
                LinkBuilder::createAdvLink(['page' => 'AdminModule:Home:dashboard'], 'Dashboard', 'toppanel-link'),
                LinkBuilder::createAdvLink(['page' => 'AdminModule:Tickets:queues'], 'Ticket queues', 'toppanel-link')
            ];
            $this->fillNavbar('$LINKS$', $links, true);

            $user = [];
            $user[] = '<span class="toppanel-link">' . $app->user->getUsername() . '</span>';
            $user[] = LinkBuilder::createAdvLink(['page' => 'AdminModule:Logout:logout'], 'Logout', 'toppanel-link');
            $this->fillNavbar('$USER$', $user, true);
        } else {
            $logo = '';
            $this->fillNavbar('$LOGO$', $logo);

            $links = '';
            $this->fillNavbar('$LINKS$', $links);

            $user = LinkBuilder::createAdvLink(['page' => 'AdminModule:Login:form'], 'Log in', 'toppanel-link');
            $this->fillNavbar('$USER$', $user);
        }
    }
}

?>