<?php

use App\Components\Grids\ClientGridFactory;
use App\Components\Grids\ClientUsersGridFactory;
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

    $cgf = new ClientGridFactory($db, $logger, $clientRepository, $userRepository);

    $json = json_encode(['table' => $cgf->createComponent(), 'controls' => $cgf->createGridControls()]);

    return $json;
}

function ajaxUsersList() {
    global $db, $logger, $clientRepository, $userRepository;

    $cugf = new ClientUsersGridFactory($db, $logger, $clientRepository, $userRepository);

    $json = json_encode(['table' => $cugf->createComponent(), 'controls' => $cugf->createGridControls()]);

    return $json;
}

exit;

?>