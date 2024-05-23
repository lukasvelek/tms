<?php

require_once('Ajax.php');
require_once('AjaxCommonMethods.php');

function loadWidget1() {
    global $ticketRepository;

    $tickets = $ticketRepository->getAllUnassignedTickets();

    $maxCount = 5;

    $code = '';
    if(!empty($tickets)) {
        $cnt = 0;
        foreach($tickets as $ticket) {
            if($cnt == $maxCount) break;

            $code .= $ticket->getTitle() . '<hr>';
            $cnt++;
        }
    } else {
        $code .= 'No data found!';
    }

    return json_encode(['content' => $code]);
}

exit;

?>