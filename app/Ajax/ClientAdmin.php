<?php

use App\Components\Grids\ClientGridFactory;
use App\Entities\ClientEntity;

require_once('Ajax.php');
require_once('AjaxCommonMethods.php');

$action = null;

if(isset($_GET['action'])) {
    $action = get('action');
} else if(isset($_POST['action'])) {
    $action = post('action');
}

if($action === NULL) {
    throw new Exception('$action is null');
}

try {
    echo($action());
} catch(Exception $e) {
    echo $e->getMessage();
    exit;
}

function ajaxList() {
    global $db, $logger, $clientRepository, $userRepository;

    $page = get('page');

    $qb = $clientRepository->composeQueryForGrid(__METHOD__);

    $offset = GRID_SIZE * $page;

    if($offset > 0) {
        $qb ->offset($offset);
    }

    $qb->execute();

    $clients = [];
    while($row = $qb->fetchAssoc()) {
        $clients[] = ClientEntity::createClientEntityFromDbRow($row);
    }

    $cgf = new ClientGridFactory($db, $logger, $clientRepository, $userRepository);

    $json = json_encode(['table' => $cgf->createComponent(), 'controls' => $cgf->createGridControls()]);

    echo $json;
}

exit;

?>