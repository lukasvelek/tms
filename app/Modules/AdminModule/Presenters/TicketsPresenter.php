<?php

namespace App\Modules\AdminModule;

use App\Modules\APresenter;

class TicketsPresenter extends APresenter {
    public function __construct() {
        parent::__construct('TicketsPresenter', 'Tickets');
    }

    public function renderQueues() {
        $this->template->widget1_title = 'Unassigned tickets';
        $this->template->scripts = ['<script type="text/javascript" src="js/TicketQueue.js"></script>', '<script type="text/javascript">loadTicketQueueWidget1()</script>'];
    }

    private function createWidget1() {
        //return '<script type'
    }
}

?>